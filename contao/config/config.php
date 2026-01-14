<?php

declare(strict_types=1);

use DigitaleDinge\CompanyBundle\Model;
use DigitaleDinge\CompanyBundle\Widget;

$GLOBALS['BE_MOD']['content']['company'] = [
    'tables' => [
        'tl_company',
    ]
];

$GLOBALS['TL_MODELS']['tl_company'] = Model\CompanyModel::class;

$GLOBALS['BE_FFL']['openingTimesTable'] = Widget\OpeningTimesTable::class;
$GLOBALS['BE_FFL']['date'] = Widget\DateField::class;
