<?php

declare(strict_types=1);

use Contao\CoreBundle\EventListener\Widget\HttpUrlListener;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\System;

System::loadLanguageFile('tl_member');

$GLOBALS['TL_DCA']['tl_company'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['name'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,filter,limit',
        ],
        'label' => [
            'fields' => ['name'],
            'showColumns' => true,
        ],
    ],
    'palettes' => [
        'default' =>
            '{general_legend},name,logo;' .
            '{address_legal_legend},street,postal,city,state,country,vat;' .
            '{contact_legend},emails,websites,phone_numbers,fax_numbers;' .
            '{times_legend},opening_times,closing_times;' .
            '{misc_legend:hide},socials,additional;',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'name' => [
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
                'decodeEntities' => true,
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'logo' => [
            'inputType' => 'fileTree',
            'eval' => [
                'fieldType' => 'radio',
                'extensions' => '%contao.image.valid_extensions%',
                'filesOnly' => true,
                'tl_class' => 'w50',
            ],
            'sql' => 'binary(16) NULL',
        ],
        'street' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'postal' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w25',
            ],
            'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
        ],
        'city' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w25',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'state' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'country' => [
            'inputType' => 'select',
            'eval' => [
                'tl_class' => 'w50',
                'includeBlankOption' => true,
                'chosen' => true,
            ],
            'options_callback' => static fn () => System::getContainer()->get('contao.intl.countries')->getCountries(),
            'sql' => ['type' => 'string', 'length' => 2, 'default' => ''],
        ],
        'vat' => [
            'inputType' => 'text',
            'eval' => [
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'emails' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'email' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_member']['email'][0],
                    'inputType' => 'text',
                    'eval' => [
                        'maxlength' => 255,
                        'rgxp' => 'email',
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => 'text NULL',
        ],
        'websites' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'website' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_member']['website'][0],
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => HttpUrlListener::RGXP_NAME,
                        'maxlength' => 255,
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'phone_numbers' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'phone' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_member']['phone'][0],
                    'inputType' => 'text',
                    'eval' => [
                        'maxlength' => 64,
                        'rgxp' => 'phone',
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => 'text NULL',
        ],
        'fax_numbers' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'fax' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['fax'][0],
                    'inputType' => 'text',
                    'eval' => [
                        'maxlength' => 64,
                        'rgxp' => 'phone',
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50'],
            'sql' => 'text NULL',
        ],
        'socials' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'social' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['socials'][2],
                    'inputType' => 'select',
                    'eval' => [
                        'includeBlankOption' => true,
                        'chosen' => true,
                        'tl_class' => 'clr',
                    ],
                ],
                'url' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['socials'][3],
                    'inputType' => 'text',
                    'eval' => [
                        'rgxp' => HttpUrlListener::RGXP_NAME,
                        'maxlength' => 255,
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50 clr', 'sortable' => false],
            'sql' => 'text NULL',
        ],
        'opening_times' => [
            'inputType' => 'openingTimesTable',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "text NULL",
        ],
        'closing_times' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'start' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['closing_times'][2],
                    'inputType' => 'date',
                ],
                'stop' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['closing_times'][3],
                    'inputType' => 'date',
                ],
            ],
            'eval' => ['tl_class' => 'w50', 'sortable' => false],
            'sql' => "text NULL",
        ],
        'additional' => [
            'inputType' => 'rowWizard',
            'exclude' => true,
            'fields' => [
                'key' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['additional'][2],
                    'inputType' => 'text',
                    'eval' => [
                        'maxlength' => 255,
                        'decodeEntities' => true,
                    ],
                ],
                'value' => [
                    'label' => &$GLOBALS['TL_LANG']['tl_company']['additional'][3],
                    'inputType' => 'text',
                    'eval' => [
                        'maxlength' => 255,
                        'decodeEntities' => true,
                    ],
                ],
            ],
            'eval' => ['tl_class' => 'w50', 'sortable' => false],
            'sql' => 'text NULL',
        ],
    ],
];
