<?php

########################################################################
# Extension Manager/Repository config file for ext "simpleprovider".
#
# Auto generated 02-11-2011 11:13
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Simple Data Provider - Tesseract project',
	'description' => 'Provides a simple selector for choosing one or more records from one specific table in the TYPO3 database, that can then be rendered using a Tesseract Data Consumer.',
	'category' => 'misc',
	'author' => 'Francois Suter (Cobweb)',
	'author_email' => 'typo3@cobweb.ch',
	'shy' => '',
	'dependencies' => 'tesseract',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'tesseract' => '1.3.0-0.0.0',
			'overlays' => '2.0.0-0.0.0'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"5449";s:10:"README.txt";s:4:"ee2d";s:71:"class.tx_simpleprovider_tx_simpleprovider_selection_reference_table.php";s:4:"2c21";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"4750";s:14:"ext_tables.sql";s:4:"0952";s:36:"icon_tx_simpleprovider_selection.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"095e";s:7:"tca.php";s:4:"1f55";s:19:"doc/wizard_form.dat";s:4:"b327";s:20:"doc/wizard_form.html";s:4:"e477";}',
);

?>