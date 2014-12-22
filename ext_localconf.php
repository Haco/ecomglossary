<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Ecom.' . $_EXTKEY,
	'Ecomglossary',
	array(
		'Term' => 'list, show',
	),
	// non-cacheable actions
	array(
		'Term' => '',
	)
);
