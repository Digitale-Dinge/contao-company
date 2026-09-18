<?php

declare(strict_types=1);

namespace DigitaleDinge\CompanyBundle\Widget;

use Contao\System;
use Contao\TextField;

class DateField extends TextField
{
    public function generate(): string
    {
        return System::getContainer()->get('twig')->render('@Contao/backend/widget/date.html.twig', [
            'id' => $this->strId,
            'name' => $this->strName,
            'value' => $this->varValue,
            'class' => $this->strClass,
            'attributes' => $this->getAttributes(),
        ]);
    }
}
