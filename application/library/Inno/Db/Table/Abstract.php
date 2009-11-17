<?php

require_once 'Zend/Db/Table/Abstract.php';

/**
 * Extends Zend_Db_Table in order to provide additional functionality
 *
 */

class Inno_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * Find a list of values that match an array of $key => $value.
	 * The matching is made as an AND.
	 *  
	 * @param array 		$criterion - associative array $key=>$value
	 * @param string 		$retKeyAttr - the attribute that will be used to 
	 * 									  construct the return array ids; this is usually the PK.
	 * @param array|string 	$retValueAttr - the attribute( or an attributes array) 
	 * 										that will be used to construct the return array values
	 * 	Example 1: 
	 * 		$models->findByKey( array ('manufacturer'=>'1', 'type'=>3), 
	 * 							'id', 'name');
	 * 		the result will be an array with pairs (id => name) from the table
	 * 
	 *  Example 2:
	 * 		$persons->findByKey ( array ('surname'=>'Vasile', 'surname' => 'Marioara'), 
	 * 							  'CNP', array ('name','surname')
	 *  	the result will be an array with pairs (cnp => name+surname)) 
	 * 
	 * @return array id=>value, that will contain 
	 * 
	 */
	public function findByKey(array $criterion, $retKeyAttr, $retValueAttr ) 
	{
		
		/** Create the select */
		$select = new Zend_Db_Select($this->getDefaultAdapter());
		$select->from($this->_name);
		
		
		foreach ($criterion as $key=>$value) {
				$select->where("$key = ?",$value);
		}
		
		
		
	
		/** Prepare and execute the statement*/
		$stmt 	= $this->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();
		
		/** Prepare the result array */
		$returnArray = array();
		
		if ( is_array($retValueAttr) ) {
			foreach ($rowset as $row) {
				$returnArray[$row[$retKeyAttr]] ='';
				foreach ( $retValueAttr as $key ) {	
						$returnArray[$row[$retKeyAttr]] =$row[$key].' ';
				}
			}
		}
		else {
			foreach ($rowset as $row) {
				$returnArray[$row[$retKeyAttr]] = $row[$retValueAttr];
			}
		}
			
		return $returnArray;
	}
	
		/**
	 * Converts a rowset to an associative array (pk => value).
	 *	 - 'pk' will be retrieve using Db_Table info function
	 *	 - 'value' is composed  from the supplied atributes values
	 *
	 * @limitation  doesn't work with composite primary Key.
	 * @return array ( 'pk' => 'value')
	 */
	protected function convertRowsetToList(Zend_Db_Table_Rowset_Abstract $rowset, array $cols)
	{
		$rowset 	= $rowset->toArray();
		$list 		= array();

		$primary = $this->info('primary');
		$primary = $primary[1];

		foreach ($rowset as $row) {
			$value = '';
			foreach ($cols as $col) {
				if ($col != $primary) {
					$value = $value.' '.$row[$col];
				}
			}
			$list[$row[$primary]] = $value;
		}

		return $list;  
	}

	/**
	 * Delete all the rows that have the id specified by $ids
	 * SQL: <delete from db_name where id in ('x', 'xss', 'ddss')>
	 * 
	 * @param array $ids - all the primary keys that will be deleted.
	 * @param $primaryKey by default is set to 'id' and it can be customised.
	 *  
	 * @return number of rows affected.
	 */
	public function deleteIds(array $ids, $primaryKey = 'id')
	{
		//Id string will be used for the where clause 
		$idString = "('".implode("','", $ids)."')";
		
		return $this->delete("$primaryKey in $idString");
	}

}

?>