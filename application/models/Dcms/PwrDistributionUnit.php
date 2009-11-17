<?php
/** Include Inno	_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';

class dcms_PwrDistributionUnit extends Inno_Db_Table_Abstract implements Inno_Db_Table_BaseFormInterface 
{
	protected $_name = "dcms.pwr_distrib_unit";

	/**
	 *  @deprecated
	 *  Inno_Db_Table_BaseFormInterface selectElements() implementation
	 */
	public function selectElements(array $criteria = null)
	{
		
		return $this->selectPDUs($criteria);
	}
	
	
	/**
	 *  @deprecated
	 *  Select all pdus from the table  
	 */
	private function selectPDUs(array $criteria = null)
	{
		$select = new Zend_Db_Select($this->getAdapter()); 
		
		$select->from(array('pdus'=>$this->_name),array('refPk'=>'id','name'=>'name'));
		
		
		/**
		 * Add criteria where clause
		 * It is possible to add more criterias as AndWhere clauses.
		 */ 
		foreach ($criteria as $key=>$value) {
			$select->where("$key = ?",$value);
		}
		
		return $select;
	}
}
?>