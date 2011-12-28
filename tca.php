<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_simpleprovider_selection'] = array(
	'ctrl' => $TCA['tx_simpleprovider_selection']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,title,description,reference_table,records'
	),
	'feInterface' => $TCA['tx_simpleprovider_selection']['feInterface'],
	'columns' => array(
		't3ver_label' => array(
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array(
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table'       => 'tx_simpleprovider_selection',
				'foreign_table_where' => 'AND tx_simpleprovider_selection.pid=###CURRENT_PID### AND tx_simpleprovider_selection.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection.description',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'reference_table' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection.reference_table',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection.any_tables', '*'),
					array('', '--div--')
				),
				'itemsProcFunc' => 'Tx_Simpleprovider_Utilities_ReferenceTableItems->getListOfTables',
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'records' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:simpleprovider/locallang_db.xml:tx_simpleprovider_selection.records',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => '*',
				'prepend_tname' => TRUE,
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 100,
				'MM' => 'tx_simpleprovider_selection_records_mm',
				'wizards' => array(
					'edit' => array(
						'type' => 'popup',
						'title' => 'LLL:EXT:lang/locallang_common.xml:edit',
						'script' => 'wizard_edit.php',
						'icon' => 'edit2.gif',
						'popup_onlyOpenIfSelected' => 1,
						'notNewRecords' => 1,
						'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1,resizable=yes'
					),
				)
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden, title;;1;;2-2-2, reference_table;;;;3-3-3, records')
	),
	'palettes' => array(
		'1' => array('showitem' => 'description')
	)
);
?>