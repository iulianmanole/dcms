<?php
class Model_PwrDistributionUnitGateway extends Inno_Model_Gateway
{
	protected $_primaryTable = 'dcms_PwrDistributionUnit';

	public function selectAllPdus ($criteria = array())
	{
		$select = $this->_getSelect();
		//based on the initial select we add RefPk
		$select->columns(array('refPk' => 'id'));
		
		if (count($criteria)) {
			foreach ($criteria as $criterion) {
				if (is_array($criterion)) {
			 	$statement 	= array_shift($criterion);
			 	$value 		= array_shift($criterion);
			 	$select->where($statement, $value);
				} else {
					$select->where($criterion);
				}
			}
		}
		return $select;
	}

	public function deletePdus(array $ids)
	{
		$table = $this->getPrimaryTable(); 
		
		try {
			$deletedRows = $table->deleteIds($ids);
		}
		catch (Exception $e) {
			return false;
		}	
		return $deletedRows;
	}
	
	/**
	 * @return associative array (id -> name) with all the manufacturers
	 */
	public function getManufacturers()
	{
		$table 	 = $this->getDbTable('dcms_Manufacturer');
	
		return $table->getManufacturers();		
	}
	
	/** Retrieves Manufacturer's hardware products 
	 * 
	 * @param array $manufacturersIds represent a list of mfctrs ids. 
	 * @return assoc array (id => hwProduct Name)
	 * @return false
	 */
	public function getHwProducts(array $manufacturersIds)
	{
		$table = $this->getDbTable('dcms_HwProduct');
		
		return $table->getProducts($manufacturersIds);
		
	}
	
	protected function _getSelect()
	{
		$table = $this->getPrimaryTable();

		$select = $table->select()->setIntegrityCheck(false)
								->from(array('p' => 'dcms.pwr_distrib_unit'));
			
		$select	->joinLeft(array('h' => 'dcms.hw_product'), 'h.id = p.hw_product_id', array('hw_product'=> 'h.name'))
				->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = h.manufacturer_id', array('manufacturer'=> 'm.name'));
			
		return $select;
	}
	
	
}

?>