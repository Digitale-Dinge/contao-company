<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Company;

use Contao\StringUtil;
use DigitaleDinge\CompanyBundle\Model\CompanyModel;
use Spatie\OpeningHours\Day;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\TimeRange;

final class OpeningTimes
{
    private OpeningHours|null $openingHours = null;

    public function __construct(
        readonly private CompanyModel $company,
    ) {
    }

    public function getOpeningHours(): OpeningHours
    {
        if (null !== $this->openingHours) {
            return $this->openingHours;
        }

        $rows = array_values(StringUtil::deserialize($this->company->opening_times, true));
        $week = [];

        foreach (Day::cases() as $day) {
            $row = array_values((array) ($rows[$day->toISO() - 1] ?? []));
            $ranges = [];

            foreach ([[0, 1], [2, 3]] as [$start, $end]) {
                $opens = $this->normalizeTime($row[$start] ?? null);
                $closes = $this->normalizeTime($row[$end] ?? null);

                if (null !== $opens && null !== $closes && $closes > $opens) {
                    $ranges[] = $opens.'-'.$closes;
                }
            }

            sort($ranges);

            $week[$day->value] = $ranges;
        }

        return $this->openingHours = OpeningHours::createAndMergeOverlappingRanges($week);
    }

    public function getTimezone(): \DateTimeZone
    {
        try {
            return new \DateTimeZone($this->company->timezone);
        } catch (\Exception) {
            return new \DateTimeZone(date_default_timezone_get());
        }
    }

    public function getClosingTimes(): array
    {
        $periods = [];

        foreach (StringUtil::deserialize($this->company->closing_times, true) as $row) {
            $from = $this->normalizeDate($row['start'] ?? null);
            $to = $this->normalizeDate($row['stop'] ?? null) ?? $from;

            if (null !== $from && null !== $to && $to >= $from) {
                $periods[] = ['from' => $from, 'to' => $to];
            }
        }

        usort($periods, static fn (array $a, array $b): int => $a['from'] <=> $b['from']);

        return $periods;
    }

    public function getStatusConfig(): array
    {
        $days = [];

        foreach (Day::cases() as $day) {
            $days[$day->toISO()] = $this->getOpeningHours()->forDay($day)->map(
                static fn (TimeRange $range): array => [
                    'opens' => (string) $range->start(),
                    'closes' => (string) $range->end(),
                ],
            );
        }

        return [
            'timezone' => $this->getTimezone()->getName(),
            'days' => $days,
            'closingTimes' => $this->getClosingTimes(),
        ];
    }

    public function getSchemaSpecification(): array
    {
        return $this->getOpeningHours()->asStructuredData();
    }

    public function getSchemaSpecialSpecification(): array
    {
        return array_map(
            static fn (array $period): array => [
                '@type' => 'OpeningHoursSpecification',
                'opens' => '00:00',
                'closes' => '00:00',
                'validFrom' => $period['from'],
                'validThrough' => $period['to'],
            ],
            $this->getClosingTimes(),
        );
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
