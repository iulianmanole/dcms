<?php
/** Include Inno	_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';

class dcms_RelationLocationsPdus extends Inno_Db_Table_Abstract implements Inno_Db_Table_BaseFormInterface 
{
	protected $_name = "dcms.relation_locations_pdus";
	
	/**
	 *  Select relations between locations and pdus from the table
	 */
	private function selectRelations(array $criteria = null)
	{
		$select = new Zend_Db_Select($this->getAdapter()); 
		
		$select->from(array('rels'=>$this->_name),array('refPk'=>'id'))
			   ->join(array('locations'=>'dcms.locations'),
						'rels.location = locations.id', 
						array('Location'=>'name'))
			   ->join(array('pdus'=>'dcms.pwr_distrib_units'),
						'rels.pdu = pdus.id', 
						array('Pdu'=>'name'))		
		;	
		/**
		 * Add criteria where clause
		 * It is possible to add more criterias as AndWhere clauses.
		 */ 
		foreach ($criteria as $key=>$value) {
			$select->where("$key = ?",$value);
		}
		
		return $select;
	}
	
	/**
	 *  Inno_Db_Table_BaseFormInterface selectElements() implementation
	 */
	public function selectElements(array $criteria = null)
	{
		
		return $this->selectRelations($criteria);
	}
}
?>