<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Widget;

use Contao\System;
use Contao\Widget;

/**
 * @property bool $decodeEntities
 */
class OpeningTimesTable extends Widget
{
    protected $blnSubmitInput = true;

    protected $strTemplate = 'be_widget';

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
            $row = \is_array($row) ? $row : [];

            for ($j = 0; $j <= 3; ++$j) {
                $rows[$i][$j] = self::specialcharsValue($row[$j] ?? '');
            }
        }

        return System::getContainer()->get('twig')->render('@Contao/backend/widget/opening_times.html.twig', [
            'id' => $this->strId,
            'rows' => $rows,
        ]);
    }
}
