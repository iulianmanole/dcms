<?php
/** Include Zend_Db_Table_Abstract */
require_once 'Zend/Db/Table/Abstract.php';

class dcms_Manufacturer extends Inno_Db_Table_Abstract implements Inno_Db_Table_BaseFormInterface 
{
	protected $_name = "dcms.manufacturer";
	
	/**
	 * 
	 * @return associtive array (id => name)
	 */
	public function getManufacturers()
	{
		return $this->getAdapter()->fetchPairs("select id, name from manufacturer");
	}
	
	/**
	 * select to retrieve manufacturers from DB. 
	 */
	public function selectManufacturers()
	{
		$select = new Zend_Db_Select($this->getAdapter()); 
		
		$select->from(array('manuf'=>$this->_name),array('refPk'=>'id','Name'=>'name'));
		
		return $select; 
	}
	
	/**
	 *  Inno_Db_Table_BaseFormInterface selectElements() implementation
	 */
	public function selectElements(array $criteria = null)
	{
		return $this->selectManufacturers();
	}
}
?>