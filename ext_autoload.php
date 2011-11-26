<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php 37484 2010-08-31 09:47:13Z francois $
 */
$extensionPath = t3lib_extMgm::extPath('simpleprovider');
return array(
	'tx_simpleprovider_tx_simpleprovider_selection_reference_table' => $extensionPath . 'class.tx_simpleprovider_tx_simpleprovider_selection_reference_table.php',
);
?>
