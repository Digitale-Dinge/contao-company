<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_page']['fields']['dd_company'] = [
    'inputType' => 'select',
    'exclude' => true,
    'foreignKey' => "tl_company.CONCAT(name, ' (ID: ', id, ')')",
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50 clr'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

PaletteManipulator::create()
    ->addLegend('company_legend', 'global_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('dd_company', 'company_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page')
;
