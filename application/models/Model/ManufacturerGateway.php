<?php

class Model_ManufacturerGateway extends Inno_Model_Gateway 
{
	protected $_primaryTable = 'dcms_Manufacturer';
	
	/**
	 * Select statement for manufacturer
	 * @return Zend_Db_Select
	 */
	public function selectManufacturers()
	{
		$select = $this->_getSelect();
		//based on the initial select we construct data to show.
		$select->columns(array('refPk' => 'id'));

		return $select;
	}
	
	/**
	 * delete all objects referenced by $ids
	 * @see application/library/Inno/Model/Inno_Model_Gateway#delete()
	 */
	public function delete(array $ids)
	{
		return parent::delete($ids);
	}
	
	/**
	 * Select Statement for Manufacturers. 
	 *
	 * @return Zend_Db_Select 
	 */
	protected function _getSelect()
	{
		$select = $this->getPrimaryTable()->select();
		
		$select->from(array('m' => 'dcms.manufacturer'));
		
		return $select;
	}
}

?>