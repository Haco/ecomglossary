<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'dividers2tabs' => TRUE,
		'hideAtCopy' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,term_type,short_description,external_link,description,sources,related_terms,visits',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecomglossary') . 'Resources/Public/Icons/tx_ecomglossary_domain_model_term.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, term_type, short_description,external_link,description,sources,related_terms,visits',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, --palette--;;top_palette, --palette--;Header Options;header_palette, short_description, description;;;richtext:rte_transform[mode=ts_links],--palette--;Relations;bottom_palette, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'top_palette' => array('showitem' => 'hidden;;1,visits', 'canNotCollapse' => 1),
		'header_palette' => array('showitem' => 'title, external_link,--linebreak--,term_type', 'canNotCollapse' => 1),
		'bottom_palette' => array('showitem' => 'sources,--linebreak--, related_terms', 'canNotCollapse' => 1),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_ecomglossary_domain_model_term',
				'foreign_table_where' => 'AND tx_ecomglossary_domain_model_term.pid=###CURRENT_PID### AND tx_ecomglossary_domain_model_term.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => 1
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.title',
			'l10n_mode' => 'prefixLangTitle',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'term_type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.term_type',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('Language', 1),
					array('Definition', 2),
					array('Acronym (e.g. NATO)', 3),
					array('Abbreviation (e.g. etc.)', 4),
				),
				'size' => 1,
				'maxitems' => 1,
			),
		),
		'short_description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.short_description',
			'config' => array(
				'type' => 'text',
				'cols' => 30,
				'rows' => 5,
				'eval' => 'trim'
			)
		),
		'description' => array(
			'exclude' => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.description',
			'config' => array(
				'type' => 'text',
				'cols' => 50,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'script' => 'wizard_rte.php',
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
		),
		'external_link' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.external_link',
			'config' => array(
				'type' => 'input',
				'size' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'params' => array(
							'blindLinkOptions' => 'page,file,folder,spec',
						),
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'sources' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.sources',
			'config' => array(
				'type' => 'text',
				'rows' => 8,
				'cols' => 50,
				'eval' => 'trim',
				'wizards' => array(
					'_PADDING' => 5,
					'link' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:cms/locallang_ttc.xml:header_link_formlabel',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
					),
				),
				'softref' => 'typolink',
			),
		),
		'visits' => array(
			'exclude' => 1,
			'label' => 'Visits',
			'l10n_display' => 'hideDiff',
			'config' => array(
				'foreign_table' => 'tx_ecomglossary_domain_model_term',
				'foreign_field' => 'visits',
				'readOnly' => 1,
				'type' => 'input',
				'size' => 11,
				'eval' => 'trim'
			),
		),
		'related_terms' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.related_terms',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_ecomglossary_domain_model_term',
				'foreign_table_where' => 'AND l10n_parent = 0 ORDER BY title',
				'MM' => 'tx_ecomglossary_term_term_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'wizards' => array(
					'_PADDING' => 3,
					'_VERTICAL' => 1,
					'edit' => array(
						'type' => 'popup',
						'title' => 'Edit',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new',
						'icon' => 'add.gif',
						'params' => array(
							'table' => 'tx_ecomglossary_domain_model_term',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'suggest' => array(
						'type' => 'suggest',
					),
				),
			),
		),
	),
);
