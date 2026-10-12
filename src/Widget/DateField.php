<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Widget;

use Contao\System;
use Contao\TextField;

/**
 * @property bool $time
 */
class DateField extends TextField
{
    public function generate(): string
    {
        return System::getContainer()->get('twig')->render('@Contao/backend/widget/company_date.html.twig', [
            'type' => $this->time ? 'time' : 'date',
            'id' => $this->strId,
            'name' => $this->strName,
            'value' => $this->varValue,
            'class' => $this->strClass,
            'attributes' => $this->getAttributes(),
        ]);
    }
}
