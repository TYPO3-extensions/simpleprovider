<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Francois Suter (Cobweb) <typo3@cobweb.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/



/**
 * Class called to manipulate the field reference_table from table tx_simpleprovider_selection
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_simpleprovider
 */
class Tx_Simpleprovider_Utilities_ReferenceTableItems {

	/**
	 * Add to the list of items all tables that have a TCA configuration
	 *
	 * @param array $params List of parameters of the field
	 * @param t3lib_TCEforms $parentObject Back-reference to the calling object
	 * @return void
	 */
	public function getListOfTables(&$params, t3lib_TCEforms $parentObject) {
		$tables = array();
			// First get a list of all tables
		foreach ($GLOBALS['TCA'] as $tableKey => $tableTCA) {
			$tables[$tableKey] = $GLOBALS['LANG']->sL($tableTCA['ctrl']['title']);
		}
			// Sort the tables on their name
		asort($tables);
			// Add the tables to the list of items
		foreach ($tables as $tableKey => $tableName) {
			$params['items'][] = array($tableName, $tableKey);
		}
	}
}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/simpleprovider/class.tx_simpleprovider_tx_simpleprovider_selection_reference_table.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/simpleprovider/class.tx_simpleprovider_tx_simpleprovider_selection_reference_table.php']);
}

?>