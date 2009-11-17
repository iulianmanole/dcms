<?php
/**
 * @desc 
 * 	Gateway will provide logic for domain model access
 * 	It will provide functionality that will be injected in Model.
 * 	The Gateway will be used for: 
 * 		- data access 
 *  	- inject functionality to the model, by loading other objectss   
 *  	- provide coordinated domain model access 
 * 
 *
 */

abstract class Inno_Model_Gateway 
{
	/**
	 * Table objects that will be used by this model 
	 * @var array registry of table Objects 
	 */
	protected $_dbTables = array(); 
	
	/**
	 * primary table for operations 
	 * MUST be set in final classes.
	 * @var  string 
	 */
	protected $_primaryTable = null; 
	
	public function __construct()
	{
		if ($this->_primaryTable === null) {
			throw new Inno_Organizer_Exception('Primary Table is not set. primaryTable attribute cannot be null.');  
		}
	}
	
	/**
	 * Lazy loaded Db Table 
	 * 
	 * @param string $name - full name of Table class ( ex: dcms_Location)
	 * @return Inno_Db_Table_Abstract
	 */
	public function getDbTable($name = null)
	{
		if ($name === null) {
			throw new Inno_Model_Exception('Database name is null.Cannot instantiate db table object.'); 
		}
		
		if (!isset($this->_dbTables[$name])) {
            $class = $name; 
			if (class_exists($class)) {
            	$this->_dbTables[$name] = new $class;
			}
			else { 
				throw new Inno_Model_Exception('Db Table Class > ' . $class . ' < doesn\'t exist.' . 
            									'Unable to instantiate db'
            									,Inno_Model_Exception::INEXISTENT_CLASS); 
			}
        }
        return $this->_dbTables[$name];
	}
	
	/**
	 * primary Table object that will be used for operations.
	 * @return Inno_Db_Table_Abstract
	 */
	public function getPrimaryTable() 
	{
		return $this->getDbTable($this->_primaryTable);
	}
	
	/**
	 * @TODO consideration must be taken for interdependent data (FK constraints).
	 * 
	 * Default Delete Operation. 
	 * It proxies to the primaryTable->deleteIds.
	 * 
	 * @param $ids
	 * @return number of affected rows 
	 */
	public function delete(array $ids)
	{
		$table = $this->getPrimaryTable();

		try {
			$deletedRows = $table->deleteIds($ids);
		}
		catch (Exception $e) {
			Zend_Registry::get('logger')->err(__METHOD__.' '.$e->getMessage());
			return false;
		}
		
		return $deletedRows;
	}

}
?>