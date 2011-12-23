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
 * Implements a TCEforms hook to dynamically modify the TCA of the tx_simpleprovider_selection table
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_simpleprovider
 *
 * $Id: class.tx_simpleprovider_provider.php 55409 2011-12-12 21:52:25Z francois $
 */
class Tx_Simpleprovider_Hooks_TceForms {

	/**
	 * Changes the TCA setup for the "records" field
	 *
	 * @param string $table The table that is currently rendered
	 * @param array $row Data set of the current record
	 * @param t3lib_TCEforms $caller Back-reference to the calling object
	 * @return void
	 */
	public function getMainFields_preProcess($table, $row, t3lib_TCEforms $caller) {
			// If a single table has been chosen, tweak the TCA definition of the "records" selector
		if ($row['reference_table'] !== '*') {
				// Use that single table as the only allowed one
			$GLOBALS['TCA'][$table]['columns']['records']['config']['allowed'] = $row['reference_table'];
				// TODO: check the possibility to dynamically add a suggest wizard
				// This does not seem to be so simple, as the suggest wizard uses an AJAX call
				// and there's apparently no corresponding hook during the AJAX call process
				// to perform a similar manipulation of the TCA
		}
	}
}
?>