<?php
class Model_RackGateway extends Inno_Model_Gateway
{
	protected $_primaryTable = 'dcms_Rack';

	 //hardware type that will be used for Rack objects 
	const HW_TYPE_CLASS = 'passive';
	const HW_TYPE_NAME	= 'rack';

	/**
	 *
	 * @return array RackTypes
	 */
	public function  getTypes()
	{
		return $this->_types;
	}

	/**
	 * get all Locations as id => fullPathToLocation
	 *
	 * @return array
	 */
	public function getLocations()
	{
		$location =  new Model_Location(array('id' => 1));

		return $location->getGateway()->fetchOrganizersByPath();
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
	 * Get all rack Hardware Products (hwproduct type = self::HW_TYPE)
	 * The list can be filtered based on the manufacturer.  
	 * 
	 * @param $manufacturerId - manufacturer id.
	 * @return array (id,name)
	 */
	public function getRackProducts($manufacturerId = null)
	{
		if (null === $manufacturerId) {
			return array( 'no data to display, mfctrId is null');
		}
		else {
			$table = $this->getDbTable('dcms_HwProduct');
			$select = $table->select()
				->setIntegrityCheck(false)
				->from(array('hwp' => 'hw_product'),array('id','name'))
				->joinLeft(array('hwt' => 'hw_type'),'hwp.type_id = hwt.id',array())
				->where('hwt.name = \''.self::HW_TYPE_NAME.'\'')
				//->where('hwt.type_class = \''.self::HW_TYPE_CLASS.'\'')
				->where('hwp.manufacturer_id =\''.$manufacturerId.'\'');
			
			$rows = $table->getAdapter()->fetchPairs($select);
			return $rows;
		}
	}
	
	/**
	 * Select statement to be used by extendedPaginator.
	 * allows an criteria('column' => 'value') array to restrict the search
	 * @return Zend_Db_Table
	 */
	public function selectRacks($criteria)
	{
		$select = $this->_getSelect();
		//based on the initial select we construct data to show.
		$select->columns(array('refPk' => 'id'));
		
		foreach ($criteria as $attribute => $value) {
			$select->where("$attribute = ?", $value);
		}
		
		//Zend_Debug::dump($select->__toString(),'rackGateway->SelectRacks');
		return $select;
	}

	/**
	 *
	 * @param $ids
	 * @return number of affected rows
	 */
	public function delete(array $ids)
	{
		return parent::delete($ids);
	}

	/**
	 * get select statement for racks.
	 *
	 * @return Zend_Db_Select
	 */
	public function _getSelect()
	{
		//LocationSelect will be used to retrieve locations as path/to/location, instead of simple names.
		$locationsGw 	 = new Model_LocationGateway();
		$locationsSelect = $locationsGw->selectOrganizersByPath();
		
		$select = $this->getPrimaryTable()->select();
		$select->setIntegrityCheck(false)
			->from(array('r' => 'dcms.rack'))
			->joinLeft(array('l' => (new Zend_Db_Expr('('.$locationsSelect.')'))), 'l.id = r.location_id', array('location'=> 'l.path'))			
			->joinLeft(array('p' => 'dcms.hw_product'), 'p.id = r.hw_product_id', array('hw_product'=> 'p.name'))
			->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = p.manufacturer_id', array('manufacturer'=> 'm.name'));
			
		return $select;
	}
}
?>