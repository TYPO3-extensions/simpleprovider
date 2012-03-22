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
 * Implementation of the simple provider Data Provider
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_simpleprovider
 *
 * $Id$
 */
class Tx_Simpleprovider_Services_Provider extends tx_tesseract_providerbase {
    /**
     * @var string Type of data structure to provide (default is idList)
     */
    protected $dataStructureType = tx_tesseract::IDLIST_STRUCTURE_TYPE;

	/**
	 * @var language Local instance of a language object
	 */
	protected $languageObject;

	/**
	 * @var array List of selected records
	 */
	protected $selectedRecords = array();

	/**
	 * This method is used to load the details about the Data Provider passing it whatever data it needs
	 * It expands on the parent method to load the records referenced by the provider
	 *
	 * @param	array	$data: Data for the Data Provider
	 * @return	void
	 */
	public function loadData($data) {
		parent::loadData($data);
			// Get all the records related to this provider
		$this->selectedRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tablenames, uid_foreign, sorting',
			'tx_simpleprovider_selection_records_mm',
			'uid_local = ' . $this->uid,
			'',
			'sorting ASC'
		);
	}

	/**
	 * This method returns the type of data structure that the Data Provider can prepare
	 *
	 * @return string Type of the provided data structure
	 */
	public function getProvidedDataStructure() {
		return $this->dataStructureType;
    }

	/**
	 * This method indicates whether the Data Provider can create the type of data structure requested or not
	 *
	 * @param string $type Type of data structure
	 * @return boolean TRUE if it can handle the requested type, FALSE otherwise
	 */
	public function providesDataStructure($type) {
		// Check which type was requested and return true if type can be provided
		// Store requested type internally for later processing
		if ($type == tx_tesseract::IDLIST_STRUCTURE_TYPE) {
			$this->dataStructureType = tx_tesseract::IDLIST_STRUCTURE_TYPE;
			$result = TRUE;
		} elseif ($type == tx_tesseract::RECORDSET_STRUCTURE_TYPE) {
			$this->dataStructureType = tx_tesseract::RECORDSET_STRUCTURE_TYPE;
			$result = TRUE;
		} else {
			$result = FALSE;
		}
		return $result;
    }

	/**
	 * This method returns the type of data structure that the Data Provider can receive as input
	 *
	 * @return string Type of used data structures
	 */
	public function getAcceptedDataStructure() {
		return tx_tesseract::IDLIST_STRUCTURE_TYPE;
    }

	/**
	 * This method indicates whether the Data Provider can use as input the type of data structure requested or not
	 *
	 * @param string $type Type of data structure
	 * @return boolean TRUE if it can use the requested type, FALSE otherwise
	 */
	public function acceptsDataStructure($type) {
			// TODO: implement support for such input
		return $type == tx_tesseract::IDLIST_STRUCTURE_TYPE;
    }

	/**
	 * This method assembles the data structure and returns it
	 *
	 * @return array standardised data structure
	 */
	public function getDataStructure() {
			// Dispatch to appropriate method depending on requested structure type
		if ($this->dataStructureType == tx_tesseract::IDLIST_STRUCTURE_TYPE) {
			$structure = $this->assembleIdListStructure();
		} else {
			$structure = $this->assembleRecordsetStructure();
		}
		return $structure;
    }

	/**
	 * Assembles an id list-type data structure from the selected records
	 *
	 * @return array Id list-type data structure
	 */
	protected function assembleIdListStructure() {
			// Assemble a list of all different tables, of all uid's
			// and of all table names and uids concatenated together
		$tables = array();
		$uids = array();
		$uidList = array();
		foreach ($this->selectedRecords as $record) {
			$tables[] = $record['tablenames'];
			$uids[] = $record['uid_foreign'];
			$uidList[] = $record['tablenames'] . '_' . $record['uid_foreign'];
		}
			// Assemble the data structure and return it
		$tables = array_unique($tables);
		$numberOfRecords = count($uidList);
		$dataStructure = array(
			'uniqueTable' => (count($tables) == 1) ? array_shift($tables) : '',
			'uidList' => implode(',', $uids),
			'uidListWithTable' => implode(',', $uidList),
			'count' => $numberOfRecords,
			'totalCount' => $numberOfRecords,
		);
		return $dataStructure;
	}

	/**
	 * Assembles a recordset-type data structure from the selected records
	 *
	 * @return array Recordset-type data structure
	 */
	protected function assembleRecordsetStructure() {
		$tables = array();
		$recordsPerTable = array();
		$recordsSortingPerTable = array();
			// Loop on all records and sort them per table
		foreach ($this->selectedRecords as $row) {
			$table = $row['tablenames'];
			if (!isset($recordsPerTable[$table])) {
				$tables[] = $table;
				$recordsPerTable[$table] = array();
				$recordsSortingPerTable[$table] = array();
			}
			$recordsPerTable[$table][] = $row['uid_foreign'];
			$recordsSortingPerTable[$table][$row['uid_foreign']] = $row['sorting'];
		}
			// Fetch the records only for the first table found
			// There's no sensible way to return data from multiple tables, but maybe this could
			// evolve in the future of Tesseract
			// Log a warning about this
		$this->getController()->addMessage(
			'simpleprovider',
			'The record selection contained multiple tables. Only the first one was preserved.',
			'Multiple tables ignored',
			t3lib_FlashMessage::WARNING,
			$tables
		);
			// Extract the id list from the first table and assemble a SQL condition based on it
		$firstTable = $tables[0];
		$uidList = implode(',', $recordsPerTable[$firstTable]);
		$where = 'uid IN (' . $uidList . ')';
			// Apply any existing filter
		$sqlParts = $this->applyFilter($firstTable);
		if (!empty($sqlParts['where'])) {
			$where .= ' AND (' . $sqlParts['where'] . ')';
		}
			// Perform the actual query
		$records = tx_overlays::getAllRecordsForTable(
			'*',
			$firstTable,
			$where,
			'',
			(empty($sqlParts['order'])) ? '' : $sqlParts['order']
		);

			// Sort records according to uidList
			// First attribute to each record its sorting value according to the record selection,
			// then sort on this value
		$numberOfRecords = count($records);
		for ($i = 0; $i < $numberOfRecords; $i++) {
			$records[$i]['tx_simpleprovider:fixed_order'] = $recordsSortingPerTable[$firstTable][$records[$i]['uid']];
		}
		usort($records, array('Tx_Simpleprovider_Services_Provider', 'sortUsingFixedOrder'));

			// Prepare the header information
		$localizedInformation = $this->getTablesAndFields('', $firstTable);
		$header = array();
		foreach ($localizedInformation[$firstTable]['fields'] as $fieldName => $fieldLabel) {
			$header[$fieldName] = array(
				'label' => $fieldLabel
			);
		}

			// Assemble the data structure and return it
		$dataStructure = array(
			'name' => $firstTable,
			'count' => $numberOfRecords,
			'totalCount' => $numberOfRecords,
			'uidList' => $uidList,
			'header' => $header,
			'filter' => $this->filter,
			'records' => $records
		);
		return $dataStructure;
	}

	/**
	 * Takes a Data Filter structure and processes its instructions into SQL statements
	 *
	 * @param string $mainTable Name of the main table from the record selection
	 * @return array Array containing the WHERE, ORDER BY and LIMIT clauses
	 */
	public function applyFilter($mainTable) {
		$sqlStatements = array(
			'where' => '',
			'order' => '',
			'limit' => ''
		);

		if (isset($this->filter['filters']) && count($this->filter['filters']) > 0) {
			$logicalOperator = (empty($this->filter['logicalOperator'])) ? 'AND' : $this->filter['logicalOperator'];

			foreach ($this->filter['filters'] as $index => $filterData) {
				$table = '';
					// Check if the condition must be explicitly ignored
					// (i.e. it is transmitted by the filter only for information)
					// If not, check the table name
				if ($filterData['void']) {
					$ignoreCondition = TRUE;
				} else {
					$ignoreCondition = FALSE;
						// If the table is not defined, apply condition to main table
					if (empty($filterData['table'])) {
						$table = $mainTable;

						// If table is defined, make sure it is among the selected tables. Otherwise ignore condition
					} else {
						$table = $filterData['table'];
						if ($table != $mainTable) {
							$ignoreCondition = TRUE;
						}
					}
				}
					// If the condition is to be ignored, log a notice,
					// otherwise apply it
				if ($ignoreCondition) {
					$this->getController()->addMessage(
						'simpleprovider',
						'The condition did not apply to a table used in the record selection.',
						'Condition ignored',
						t3lib_FlashMessage::NOTICE,
						$filterData
					);
				} else {
					$field = $filterData['field'];
					$fullField = $table . '.' . $field;
					$condition = '';
					foreach ($filterData['conditions'] as $conditionData) {
							// If the value is special value "\all", all values must be taken,
							// so the condition is simply ignored
						if ($conditionData['value'] != '\all') {
							if (!empty($condition)) {
								$condition .= ' AND ';
							}
							$condition .= '(' . tx_dataquery_SqlUtility::conditionToSql($fullField, $table, $conditionData) . ')';
						}
					}
						// Add the condition only if it wasn't empty
					if (!empty($condition)) {
						if (!empty($sqlStatements['where'])) {
							$sqlStatements['where'] .= ' ' . $logicalOperator . ' ';
						}
						$sqlStatements['where'] .= '(' . $condition . ')';
					}
				}
			}
		}
			// Add the eventual raw SQL in the filter
		if (!empty($filter['rawSQL'])) {
			$sqlStatements['where'] .= ' ' . $filter['rawSQL'];
		}
			// Handle the order by clauses
		if (count($filter['orderby']) > 0) {
			foreach ($filter['orderby'] as $orderData) {
				$table = ((empty($orderData['table'])) ? $mainTable : $orderData['table']);
					// Apply the ordering only if it matches an existing table,
					// otherwise log a notice
				if ($table == $mainTable) {
					$completeField = $table . '.' . $orderData['field'];
					$orderbyClause = $completeField . ' ' . $orderData['order'];
					if (!empty($sqlStatements['order'])) {
						$sqlStatements['order'] .= ', ';
					}
					$sqlStatements['order'] .= $orderbyClause;
				} else {
					$this->getController()->addMessage(
						'simpleprovider',
						'The ordering clause did not apply to a table used in the record selection.',
						'Ordering ignored',
						t3lib_FlashMessage::NOTICE,
						$orderData
					);
				}
			}
		}
		return $sqlStatements;
	}

	/**
     * This method returns a list of tables and fields (or equivalent) available in the data structure,
     * complete with localized labels
     *
     * @param string $language 2-letter iso code for language
	 * @param string $table Name of a specific table to fetch the information for
     * @return array List of tables and fields
     */
	public function getTablesAndFields($language = '', $table = '') {
		$localizedStructure = array();

			// Get language object
		$this->languageObject = tx_tesseract_utilities::getLanguageObject($language);

			// Include the full TCA ctrl section
		if (TYPO3_MODE == 'FE') {
			$GLOBALS['TSFE']->includeTCA();
		}

			// If no table is explicitly defined, check the reference table
		if (empty($table)) {
				// If any table can be chosen from, loop on all tables from the TCA
				// Otherwise, load localized information just for the selected table
			if ($this->providerData['reference_table'] == '*') {
				foreach ($GLOBALS['TCA'] as $table => $tableInformation) {
					$localizedStructure[$table] = $this->getLocalizedInformationForTable($table);
				}
			} else {
				$table = $this->providerData['reference_table'];
				$localizedStructure[$table] = $this->getLocalizedInformationForTable($table);
			}
		} else {
			$localizedStructure[$table] = $this->getLocalizedInformationForTable($table);
		}
		return $localizedStructure;
    }

	/**
	 * This method gets localized information for a table and its fields, if defined
	 *
	 * @param string $table Name of the table to get the information for
	 * @return array Localized information
	 */
	protected function getLocalizedInformationForTable($table) {
		$localizedInformation = array('table' => $table, 'fields' => array());
			// Set the table name, if defined in the TCA
		if (isset($GLOBALS['TCA'][$table]['ctrl']['title'])) {
			$tableName = $this->languageObject->sL($GLOBALS['TCA'][$table]['ctrl']['title']);
			$localizedInformation['name'] = $tableName;
		} else {
			$localizedInformation['name'] = $table;
		}
			// Load the full TCA for the table
		t3lib_div::loadTCA($table);
			// Get all the database fields for the table
		$fields = tx_overlays::getAllFieldsForTable($table);
			// Loop on all fields and get their localized label, if defined
		foreach ($fields as $fieldName => $fieldInformation) {
			if (isset($GLOBALS['TCA'][$table]['columns'][$fieldName]['label'])) {
				$localizedInformation['fields'][$fieldName] = $this->languageObject->sL($GLOBALS['TCA'][$table]['columns'][$fieldName]['label']);
			} else {
				$localizedInformation['fields'][$fieldName] = $fieldName;
			}
		}

		return $localizedInformation;
	}

	/**
	 * This static method is called when sorting records using a special fixed order value
	 *
	 * @param	mixed	$a: first element to sort
	 * @param	mixed	$b: second element to sort
	 *
	 * @return	integer	-1 if first argument is smaller than second argument, 1 if first is greater than second and 0 if both are equal
	 *
	 * @see	tx_dataquery_wrapper::prepareFullStructure()
	 */
	static public function sortUsingFixedOrder($a, $b) {
		$result = 1;
		if ($a['tx_simpleprovider:fixed_order'] == $b['tx_simpleprovider:fixed_order']) {
			$result = 0;
		} elseif ($a['tx_simpleprovider:fixed_order'] < $b['tx_simpleprovider:fixed_order']) {
			$result = -1;
		}
		return $result;
	}
}
?>