<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Ecom.' . $_EXTKEY,
    'Ecomglossary',
    array(
        'Term' => 'list, show, reset',
    ),
    // non-cacheable actions
    array(
        'Term' => 'list, show, reset',
    )
);
