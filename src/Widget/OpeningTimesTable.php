<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Widget;

use Contao\System;
use Contao\Widget;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @property bool $decodeEntities
 */
class OpeningTimesTable extends Widget
{
    private const string TIME_PATTERN = '/^([01]\d|2[0-3]):[0-5]\d$/';

    protected $blnSubmitInput = true;

    protected $strTemplate = 'be_widget';

    /**
     * @var array<int, string>
     */
    private array $rowErrors = [];

    /**
     * @var array<int, array<int, true>>
     */
    private array $invalidFields = [];

    public function __construct($arrAttributes = null)
    {
        parent::__construct($arrAttributes);

        $this->preserveTags = true;
        $this->decodeEntities = true;
    }

    public function generate()
    {
        $rows = [];

        $this->varValue = \is_array($this->varValue) ? $this->varValue : [];
        $rowCount = \count($this->varValue);

        while ($rowCount < 7) {
            $this->varValue[] = [''];
            ++$rowCount;
        }

        foreach (array_values($this->varValue) as $i => $row) {
            $row = \is_array($row) ? array_values($row) : [];

            for ($j = 0; $j <= 3; ++$j) {
                $rows[$i][$j] = \is_string($row[$j] ?? null) ? $row[$j] : '';
            }
        }

        return System::getContainer()->get('twig')->render('@Contao/backend/widget/opening_times.html.twig', [
            'id' => $this->strId,
            'rows' => $rows,
            'row_errors' => $this->rowErrors,
            'invalid_fields' => $this->invalidFields,
        ]);
    }

    /**
     * @return list<list<string>>
     */
    protected function validator($varInput): array
    {
        $rows = [];

        foreach (array_values(\is_array($varInput) ? $varInput : []) as $dayIndex => $row) {
            $row = \is_array($row) ? array_values($row) : [];
            $times = [];

            for ($column = 0; $column <= 3; ++$column) {
                $time = \is_string($row[$column] ?? null) ? parent::validator($row[$column]) : '';
                $times[] = \is_string($time) ? $time : '';
            }

            $this->validateRange($dayIndex, $times, 0, 1);
            $this->validateRange($dayIndex, $times, 2, 3);

            $rows[] = $times;
        }

        if ([] !== $this->rowErrors) {
            $this->addError($this->getTranslator()->trans('ERR.company_opening_times', [], 'contao_default'));
        }

        return $rows;
    }

    /**
     * @param list<string> $times
     */
    private function validateRange(int $dayIndex, array $times, int $opens, int $closes): void
    {
        foreach ([$opens, $closes] as $column) {
            if ('' !== $times[$column] && !preg_match(self::TIME_PATTERN, $times[$column])) {
                $this->markInvalid($dayIndex, $column, 'company.widget.time_invalid');
            }
        }

        if (isset($this->invalidFields[$dayIndex][$opens]) || isset($this->invalidFields[$dayIndex][$closes])) {
            return;
        }

        if (('' === $times[$opens]) !== ('' === $times[$closes])) {
            $this->markInvalid($dayIndex, '' === $times[$opens] ? $opens : $closes, 'company.widget.range_incomplete');

            return;
        }

        if ('' !== $times[$opens] && $times[$closes] <= $times[$opens]) {
            $this->markInvalid($dayIndex, $closes, 'company.widget.range_order');
        }
    }

    private function markInvalid(int $dayIndex, int $column, string $message): void
    {
        $this->invalidFields[$dayIndex][$column] = true;
        $this->rowErrors[$dayIndex] ??= $message;
    }

    private function getTranslator(): TranslatorInterface
    {
        return System::getContainer()->get('translator');
    }
}
