<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company;

use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\StringUtil;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Spatie\OpeningHours\Day;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\TimeRange;

/**
 * @phpstan-type Range array{opens: string, closes: string}
 * @phpstan-type ClosingPeriod array{from: string, from_time: string|null, to: string, to_time: string|null, reason: string}
 * @phpstan-type SpecialDay array{ranges: list<Range>, reason: string}
 * @phpstan-type ClosedDay array{windows: list<array{int, int}>, reasons: list<string>}
 */
final class OpeningTimes
{
    private OpeningHours|null $openingHours = null;

    /**
     * @var list<ClosingPeriod>|null
     */
    private array|null $closingTimes = null;

    /**
     * @var array<string, SpecialDay>|null
     */
    private array|null $specialOpeningHours = null;

    public function __construct(
        private readonly CompanyModel $company,
        private readonly InsertTagParser|null $insertTagParser = null,
    ) {
    }

    public function getOpeningHours(): OpeningHours
    {
        if ($this->openingHours instanceof OpeningHours) {
            return $this->openingHours;
        }

        $rows = array_values(StringUtil::deserialize($this->company->opening_times, true));
        $week = [];

        foreach (Day::cases() as $day) {
            $row = $rows[$day->toISO() - 1] ?? [];
            $week[$day->value] = $this->createWeekdayRanges(\is_array($row) ? $row : []);
        }

        return $this->openingHours = OpeningHours::createAndMergeOverlappingRanges($week);
    }

    public function getTimezone(): \DateTimeZone
    {
        try {
            return new \DateTimeZone($this->company->timezone);
        }
        catch (\Exception) {
            return new \DateTimeZone(date_default_timezone_get());
        }
    }

    /**
     * @return list<ClosingPeriod>
     */
    public function getClosingTimes(): array
    {
        if (null !== $this->closingTimes) {
            return $this->closingTimes;
        }

        $periods = [];

        foreach (StringUtil::deserialize($this->company->closing_times, true) as $row) {
            if (\is_array($row) && null !== ($period = $this->createClosingPeriod($row))) {
                $periods[] = $period;
            }
        }

        usort(
            $periods,
            static fn (array $a, array $b): int => [$a['from'], $a['from_time'] ?? ''] <=> [$b['from'], $b['from_time'] ?? ''],
        );

        return $this->closingTimes = $periods;
    }

    /**
     * @return array<string, SpecialDay>
     *
     * @throws \DateMalformedStringException
     */
    public function getSpecialOpeningHours(): array
    {
        if (null !== $this->specialOpeningHours) {
            return $this->specialOpeningHours;
        }

        $closedDates = [];

        foreach ($this->getClosingTimes() as $period) {
            foreach ($this->getClosedWindows($period) as $date => $closedDay) {
                $closedDates[$date] = [
                    'windows' => [...$closedDates[$date]['windows'] ?? [], ...$closedDay['windows']],
                    'reasons' => [...$closedDates[$date]['reasons'] ?? [], ...$closedDay['reasons']],
                ];
            }
        }

        $specialOpeningHours = [];

        foreach ($closedDates as $date => $closedDate) {
            $regular = array_map($this->toMinuteRange(...), $this->getRanges(Day::onDateTime(new \DateTimeImmutable($date))));
            $ranges = array_reduce($closedDate['windows'], $this->subtractWindow(...), $regular);

            if ($ranges !== $regular) {
                $specialOpeningHours[$date] = [
                    'ranges' => array_map($this->toTimeRange(...), $ranges),
                    'reason' => implode(', ', array_unique($closedDate['reasons'])),
                ];
            }
        }

        ksort($specialOpeningHours);

        return $this->specialOpeningHours = $specialOpeningHours;
    }

    /**
     * @return array{timezone: string, days: array<int, list<Range>>, specialOpeningHours: array<string, list<Range>>}
     */
    public function getStatusConfig(): array
    {
        $days = [];

        foreach (Day::cases() as $day) {
            $days[$day->toISO()] = $this->getRanges($day);
        }

        return [
            'timezone' => $this->getTimezone()->getName(),
            'days' => $days,
            'specialOpeningHours' => array_map(static fn (array $specialDay): array => $specialDay['ranges'], $this->getSpecialOpeningHours()),
        ];
    }

    public function getSchemaSpecification(): array
    {
        return $this->getOpeningHours()->asStructuredData();
    }

    /**
     * @return list<array<string, string>>
     */
    public function getSchemaSpecialSpecification(): array
    {
        $specialOpeningHours = $this->getSpecialOpeningHours();

        return array_merge(...array_map(
            $this->createSpecialSpecifications(...),
            array_keys($specialOpeningHours),
            $specialOpeningHours,
        ));
    }

    /**
     * @param array<mixed> $row
     *
     * @return list<string>
     */
    private function createWeekdayRanges(array $row): array
    {
        $row = array_values($row);
        $ranges = [];

        foreach ([[0, 1], [2, 3]] as [$start, $end]) {
            $opens = $this->normalizeTime($row[$start] ?? null);
            $closes = $this->normalizeTime($row[$end] ?? null);

            if (null !== $opens && null !== $closes && $closes > $opens) {
                $ranges[] = $opens.'-'.$closes;
            }
        }

        sort($ranges);

        return $ranges;
    }

    /**
     * @param array<mixed> $row
     *
     * @return ClosingPeriod|null
     */
    private function createClosingPeriod(array $row): array|null
    {
        if (null === ($from = $this->normalizeDate($row['start'] ?? null))) {
            return null;
        }

        $to = $this->normalizeDate($row['stop'] ?? null) ?? $from;
        $fromTime = $this->normalizeTime($row['start_time'] ?? null);
        $toTime = $this->normalizeTime($row['stop_time'] ?? null);

        if ($to.' '.($toTime ?? '24:00') <= $from.' '.($fromTime ?? '00:00')) {
            return null;
        }

        return [
            'from' => $from,
            'from_time' => $fromTime,
            'to' => $to,
            'to_time' => $toTime,
            'reason' => \is_string($row['reason'] ?? null) ? trim($row['reason']) : '',
        ];
    }

    /**
     * @param ClosingPeriod $period
     *
     * @return array<string, ClosedDay>
     */
    private function getClosedWindows(array $period): array
    {
        $dates = new \DatePeriod(
            new \DateTimeImmutable($period['from']),
            new \DateInterval('P1D'),
            new \DateTimeImmutable($period['to']),
            \DatePeriod::INCLUDE_END_DATE,
        );

        $windows = [];

        foreach ($dates as $date) {
            $key = $date->format('Y-m-d');

            $windows[$key] = [
                'windows' => [[
                    $key === $period['from'] ? $this->toMinutes($period['from_time'] ?? '00:00') : 0,
                    $key === $period['to'] ? $this->toMinutes($period['to_time'] ?? '24:00') : 1440,
                ]],
                'reasons' => '' !== $period['reason'] ? [$period['reason']] : [],
            ];
        }

        return $windows;
    }

    /**
     * @return list<Range>
     */
    private function getRanges(Day $day): array
    {
        $ranges = [];

        foreach ($this->getOpeningHours()->forDay($day) as $range) {
            if (!$range instanceof TimeRange) {
                continue;
            }

            $ranges[] = [
                'opens' => (string) $range->start(),
                'closes' => (string) $range->end(),
            ];
        }

        return $ranges;
    }

    /**
     * @param list<array{int, int}> $ranges
     * @param array{int, int}       $window
     *
     * @return list<array{int, int}>
     */
    private function subtractWindow(array $ranges, array $window): array
    {
        return array_merge(...array_map(
            fn (array $range): array => $this->subtractWindowFromRange($range, $window),
            $ranges,
        ));
    }

    /**
     * @param array{int, int} $range
     * @param array{int, int} $window
     *
     * @return list<array{int, int}>
     */
    private function subtractWindowFromRange(array $range, array $window): array
    {
        [$opens, $closes] = $range;
        [$from, $to] = $window;

        if ($to <= $opens || $from >= $closes) {
            return [$range];
        }

        return array_values(array_filter([
            $opens < $from ? [$opens, $from] : null,
            $to < $closes ? [$to, $closes] : null,
        ]));
    }

    /**
     * @param SpecialDay $specialDay
     *
     * @return list<array<string, string>>
     */
    private function createSpecialSpecifications(string $date, array $specialDay): array
    {
        $description = $this->parseReason($specialDay['reason']);

        return array_map(
            static fn (array $range): array => [
                '@type' => 'OpeningHoursSpecification',
                'opens' => $range['opens'],
                'closes' => $range['closes'],
                'validFrom' => $date,
                'validThrough' => $date,
                ...('' !== $description ? [
                    'description' => $description,
                ] : []),
            ],
            $specialDay['ranges'] ?: [[
                'opens' => '00:00',
                'closes' => '00:00',
            ]],
        );
    }

    private function parseReason(string $reason): string
    {
        if ('' === $reason || !$this->insertTagParser instanceof InsertTagParser) {
            return $reason;
        }

        return trim($this->insertTagParser->replaceInline($reason, OutputType::text));
    }

    /**
     * @param Range $range
     *
     * @return array{int, int}
     */
    private function toMinuteRange(array $range): array
    {
        return [$this->toMinutes($range['opens']), $this->toMinutes($range['closes'])];
    }

    /**
     * @param array{int, int} $range
     *
     * @return Range
     */
    private function toTimeRange(array $range): array
    {
        return [
            'opens' => $this->toTime($range[0]),
            'closes' => $this->toTime($range[1]),
        ];
    }

    private function toMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);

        return (int) $hours * 60 + (int) $minutes;
    }

    private function toTime(int $minutes): string
    {
        return \sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function normalizeTime(mixed $value): string|null
    {
        return \is_string($value) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) ? $value : null;
    }

    private function normalizeDate(mixed $value): string|null
    {
        if (!\is_string($value) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $match)) {
            return null;
        }

        return checkdate((int) $match[2], (int) $match[3], (int) $match[1]) ? $match[0] : null;
    }
}
