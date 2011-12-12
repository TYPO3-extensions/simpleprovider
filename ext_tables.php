<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_simpleprovider_selection');

$TCA['tx_simpleprovider_selection'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'requestUpdate' => 'reference_table',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Images/icon_tx_simpleprovider_selection.png',
	),
);

	// Register simpleprovider as a secondary Data Provider
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['columns']['tx_displaycontroller_provider2']['config']['allowed'] .= ',tx_simpleprovider_selection';

	// Add a wizard for adding a simpleprovider
$addSimpleProviderWizard = array(
	'type' => 'script',
	'title' => 'LLL:EXT:simpleprovider/locallang_db.xml:wizards.add_simpleprovider',
	'script' => 'wizard_add.php',
	'icon' => 'EXT:simpleprovider/Resources/Public/Images/add_simpleprovider_wizard.png',
	'params' => array(
		'table' => 'tx_simpleprovider_selection',
		'pid' => '###CURRENT_PID###',
		'setValue' => 'append'
	)
);
$TCA['tt_content']['columns']['tx_displaycontroller_provider2']['config']['wizards']['add_simpleprovider'] = $addSimpleProviderWizard;
?>