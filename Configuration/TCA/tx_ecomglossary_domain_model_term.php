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
		'dividers2tabs' => TRUE,

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
		'searchFields' => 'title,term_type,short_description,external_link,description,sources',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecomglossary') . 'Resources/Public/Icons/tx_ecomglossary_domain_model_term.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, term_type, short_description,external_link,description,sources',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, --palette--;Header Options;header_palette, short_description, description;;;richtext:rte_transform[mode=ts_links], sources, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'header_palette' => array('showitem' => 'title, external_link,--linebreak--,term_type', 'canNotCollapse' => 1),
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
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'term_type' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:ecomglossary/Resources/Private/Language/locallang_db.xlf:tx_ecomglossary_domain_model_term.term_type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
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
	),
);
