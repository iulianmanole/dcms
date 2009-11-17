<?php

class Model_HwProductGateway extends Inno_Model_Gateway 
{
	protected $_primaryTable = 'dcms_HwProduct';
	
	/**
	 * fetch all the products or a restricted set of products.
	 * 
	 * @param integer manufacturerId - Equipment Manufacturer
	 * @param integer typeId  Equipment Type
	 * 
	 * @return Model_HwProducts	
	 */
	public function fetchAllProducts($manufacturerId = null, $typeId = null)
	{
		$criteria = array();
		if ($manufacturerId) {
			$criteria[] = array('manufacturer_id = ?', $manufacturerId);
		}
		if ($typeId) {
			$criteria[] = array('type_id = ?', $typeId);
		}
		$resultSet = $this->_fetchProducts($criteria);
		
		return new Model_HwProducts($resultSet->toArray(), $this);
	}
	/**
	 * @param $ids
	 * @return number of affected rows or false.
	 */
	public function deleteProducts(array $ids)
	{
		$table = $this->getPrimaryTable(); 
		
		try {
			$deletedRows = $table->deleteIds($ids);
		}
		catch (Exception $e) {
			/** @TODO add Error Message for debugging purpose. */
			echo "ERROR:" .$e->getMessage();
			return false;
		}
		return $deletedRows;
	}
	
	/**
	 *  Zend_Db_Select object for all products. 
	 *  It is prepared to be used in the Inno_Paginator 
	 *  It will be used in paginators
	 *  
	 * @param array criteria that will be used to restrict the resultSet
	 * @return Zend_Db_Select object
	 */
	public function selectAllProducts($criteria)
	{	
		$select = $this->_getSelect(); 
		//based on the initial select we construct data to show.
		$select->columns(array('refPk' => 'id'));
		
		foreach ($criteria as $criterion) {
			if (is_array($criterion)) {
			 	$statement 	= array_shift($criterion);
                $value 		= array_shift($criterion);
                $select->where($statement, $value);
            } else {
                $select->where($criterion);
			}
		}
		
		return $select; 
	}
	
	/**
	 * @return associative array (id -> name) with all the manufacturers
	 */
	public function getManufacturers()
	{
		$table 	 = $this->getDbTable('dcms_Manufacturer');
	
		return $table->getManufacturers();		
	}
	
	/**
	 * Get all hardware types 
	 * @return associative array (id => type name )
	 */
	public function getHwTypes()
	{
		$hwTypeGw = new Model_HwTypeGateway();

		return $hwTypeGw->fetchOrganizersByPath();
	}
	
	/**
	 * @param array criteria where clauses 
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	protected function _fetchProducts(array $criteria)
	{
		$select = $this->_getSelect();
		
		foreach ($criteria as $criterion) {
			if (is_array($criterion)) {
			 	$statement 	= array_shift($criterion);
                $value 		= array_shift($criterion);
                $select->where($statement, $value);
            } else {
                $select->where($criterion);
			}
		}
		
		$rowset = $this->getPrimaryTable()->fetchAll($select);
		
		return $rowset;
		
	}
	
	/**
	 * Base select that will be used to fetch products.
	 * 
	 * @return Zend_Db_Select
	 */
	protected function _getSelect() 
	{
		$hwTypeGw 	 = new Model_HwTypeGateway();
		$hwTypeGw = $hwTypeGw->selectOrganizersByPath();
		
		
		$productTable = $this->getPrimaryTable();
		
		$select = $productTable->select()->setIntegrityCheck(false)
					->from(array('p' => 'dcms.hw_product'));
					
		$select	->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = p.manufacturer_id', array('manufacturer'=> 'm.name'))
				->joinLeft(array('t' => (new Zend_Db_Expr('('.$hwTypeGw.')'))), 't.id = p.type_id', array('type'=> 't.path'));

		
		return $select; 		
	}
}

?>