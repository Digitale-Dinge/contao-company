<?php

declare(strict_types=1);

use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_content']['palettes']['company_opening_times'] =
    '{type_legend},type,headline;' .
    '{company_legend},company,company_short_days;' .
    '{template_legend:hide},customTpl;' .
    '{expert_legend:hide},cssID;' .
    '{invisible_legend:hide},invisible,start,stop;'
;

$GLOBALS['TL_DCA']['tl_content']['palettes']['company_schema'] =
    '{type_legend},type;' .
    '{company_legend},company,company_schema_type,company_schema_url;' .
    '{invisible_legend:hide},invisible,start,stop;'
;

$GLOBALS['TL_DCA']['tl_content']['fields']['company'] = [
    'inputType' => 'select',
    'exclude' => true,
    'foreignKey' => "tl_company.CONCAT(name, ' (ID: ', id, ')')",
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['company_short_days'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    'eval' => ['tl_class' => 'w50 m12'],
    'sql' => ['type' => Types::BOOLEAN, 'default' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['company_schema_type'] = [
    'inputType' => 'select',
    'exclude' => true,
    'options' => ['LocalBusiness', 'Organization'],
    'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 32, 'default' => 'LocalBusiness'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['company_schema_url'] = [
    'inputType' => 'text',
    'exclude' => true,
    'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
    'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => ''],
];
