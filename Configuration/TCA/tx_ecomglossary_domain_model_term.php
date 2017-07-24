<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',
        'dividers2tabs' => true,
        'hideAtCopy' => true,
        'versioningWS' => true,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,meta_description,meta_title,term_type,short_description,external_link,description,sources,related_terms,visits,tx_realurl_pathsegment',
        'requestUpdate' => 'external_link',
        'typeicon_classes' => [
            'default' => 'mimetypes-x-tx_rtehtmlarea_acronym'
        ],
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, meta_description, meta_title, term_type, short_description,external_link,description,sources,related_terms,visits,tx_realurl_pathsegment',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, --palette--;;top_palette, --palette--;Header Options;header_palette, short_description, description;;;richtext:rte_transform[mode=ts_links],--palette--;Relations;bottom_palette, --div--;SEO, meta_title, meta_description, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        'top_palette' => ['showitem' => 'hidden;;1,visits', 'canNotCollapse' => 1],
        'header_palette' => ['showitem' => 'title, tx_realurl_pathsegment, --linebreak--, external_link, --linebreak--, term_type', 'canNotCollapse' => 1],
        'bottom_palette' => ['showitem' => 'sources,--linebreak--, related_terms', 'canNotCollapse' => 1],
    ],
    'columns' => [

        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_ecomglossary_domain_model_term',
                'foreign_table_where' => 'AND tx_ecomglossary_domain_model_term.pid=###CURRENT_PID### AND tx_ecomglossary_domain_model_term.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'tx_realurl_pathsegment' => [
            'label'   => 'RealURL Path Segment (eg: mobile-safety or ptt-devices. Use only a-z, 0-9 and "-" dash characters). If missing - fill in the currently used from the website.',
            'exclude' => 1,
            'default' => '',
            'l10n_mode' => 'noCopy',
            'config'  => [
                'type' => 'input',
                'max'  => 255,
                'eval' => 'trim,nospace,lower,required'
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.title',
            'l10n_mode' => 'prefixLangTitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'term_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.term_type',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', ''],
                    ['Language', 1],
                    ['Definition', 2],
                    ['Acronym (e.g. NATO)', 3],
                    ['Abbreviation (e.g. etc.)', 4],
                ],
                'renderType' => 'selectSingle',
                'size' => 1,
                'multiple' => 0,
                'maxitems' => 1,
            ],
        ],
        'short_description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.short_description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'eval' => 'trim'
            ]
        ],
        'description' => [
            'displayCond' => 'FIELD:external_link:REQ:false',
            'exclude' => 1,
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.description',
            'config' => [
                'type' => 'text',
                'cols' => 50,
                'rows' => 15,
                'eval' => 'trim',
                'wizards' => [
                    'RTE' => [
                        'icon' => 'wizard_rte2.gif',
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'module' => [
                            'name' => 'wizard_rte'
                        ],
                        'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                        'type' => 'script'
                    ]
                ]
            ],
        ],
        'external_link' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.external_link',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
                        'params' => [
                            'blindLinkOptions' => 'page,file,folder,spec',
                        ],
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_element_browser',
                            'urlParameters' => [
                                'mode' => 'wizard'
                            ]
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'softref' => 'typolink',
            ],
        ],
        'sources' => [
            'displayCond' => 'FIELD:external_link:REQ:false',
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.sources',
            'config' => [
                'type' => 'text',
                'rows' => 8,
                'cols' => 50,
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_element_browser',
                            'urlParameters' => [
                                'mode' => 'wizard'
                            ]
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'softref' => 'typolink',
            ],
        ],
        'visits' => [
            'exclude' => 1,
            'label' => 'Visits',
            'l10n_display' => 'hideDiff',
            'config' => [
                'foreign_table' => 'tx_ecomglossary_domain_model_term',
                'foreign_field' => 'visits',
                'readOnly' => 1,
                'type' => 'input',
                'size' => 11,
                'eval' => 'trim'
            ],
        ],
        'related_terms' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.related_terms',
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ecomglossary_domain_model_term',
                'foreign_table_where' => 'AND l10n_parent = 0 AND tx_ecomglossary_domain_model_term.uid != ###THIS_UID### ORDER BY title',
                'MM' => 'tx_ecomglossary_term_term_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'wizards' => [
                    'edit' => [
                        'type' => 'popup',
                        'title' => 'Edit',
                        'module' => [
                            'name' => 'wizard_edit'
                        ],
                        'icon' => 'edit2.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams' => 'height=900,width=900,status=0,menubar=0,scrollbars=1',
                    ],
                    'add' => [
                        'type' => 'script',
                        'title' => 'Create new',
                        'icon' => 'add.gif',
                        'params' => [
                            'table' => 'tx_ecomglossary_domain_model_term',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'prepend'
                        ],
                        'module' => [
                            'name' => 'wizard_add'
                        ],
                    ],
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ],
        'meta_title' => [
            'exclude' => 1,
            'label' => 'META Term-Title for Google (SHORT!). If not set it uses default title.',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'meta_description' => [
            'exclude' => 1,
            'label' => 'META Description for Google',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'eval' => 'trim'
            ]
        ],
    ],
];
