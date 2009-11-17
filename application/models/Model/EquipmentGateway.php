<?php

class Model_EquipmentGateway extends Inno_Model_Gateway 
{
	protected $_primaryTable = 'dcms_Equipment';
	
	/**
	 * Retrieve all Hardware Types (Relies on Model_HwTypesGateway)
	 * @param class {equipment|power|passive} type_class of the object
	 * @return Hardware Types from dcms.hw_type 
	 */
	public function getHwTypes()
	{
		$hwTypeGw = new Model_HwTypeGateway();
		return $hwTypeGw->fetchOrganizersByPath();
	}
	
	public function getLocations()
	{
		$location =  new Model_Location(array('id' => 1));
		return $location->getGateway()->fetchOrganizersByPath();
	}
	
	public function getManufacturers()
	{
		$table 	 = $this->getDbTable('dcms_Manufacturer');
		return $table->getManufacturers();
	}
	
	/**  
	 * Retrieve all Groups
	 * @return array (id => full path to Group)
	 */
	public function getGroups()
	{
		$grp = new Model_Group(array('id' => 1));
		return $grp->getGateway()->fetchOrganizersByPath();
	}
	
	public function getSystems()
	{
		$systm = new Model_System(array('id' => 1));
		return $systm->getGateway()->fetchOrganizersByPath();
	}
	
	/**
	 * Retrieve Hardware Products. 
	 * @param $mfctr
	 * @param $hwType
	 * @return unknown_type
	 */
	public function getHwProducts($mfctr = null, $hwType = null)
	{
		$table 	= $this->getDbTable('dcms_HwProduct');
		return $table->getHwProducts($mfctr, $hwType);
	} 
	/**
	 * @deprecated 
	 * Retrieve Racks with respect to "standalone" attribute
	 * If HwProduct -> isStandAlone then the result will be null; 
	 * 		else it will return rack list.
	 * 
	 * @return null | racks list ( rack id => rack name)
	 */
	public function getRacks($locationId = null, $hwProdxuctId = null)
	{
		trigger_error("This function is not used anymore and will be removed.". 
					'Please use $this->getRacksByLocation');
		//get hwProduct standalone characteristic 
		//return null if hwProduct is standalone
		//return racks from $location if hwProduct is not standalone.
		if (($hwProductId === null) or ($locationId === null)){
			return null;
		}
		
		$hwProduct = new Model_HwProduct(array('id' => $hwProductId)); 
		
		if ($hwProduct->isStandalone()) {
			//product is stand alone.
			return null;
		}
		else {
			//product is not standalone
			//we'll return all the racks from the location
			$table = $this->getDbTable('dcms_Rack');
			return $table->getRacksByLocation($locationId);
		}
	}
	
	/**
	 * Fetch all racks for a location
	 * 
	 * @param $locationId
	 * @return null| array racks list ( rack id => rack name)
	 */
	public function getRacksByLocation($locationId)
	{
		if (($locationId === null)){
			return null;
		}
		$table = $this->getDbTable('dcms_Rack');
		
		return $table->getRacksByLocation($locationId);
	}
	
	public function selectEquipments()
	{
		$select = $this->_getSelect();
		//based on the initial select we construct data to show.
		$select->columns(array('refPk' => 'id'));

		return $select;
	}
	
	/**
	 * Change(Overwrite) the location for selected equipments ids. 
	 * 
	 * @return int affected equipments or false in case of failure
	 */
	public function setLocation(array $ids, $locationId = null, $rackId = null)
	{		
		if ($locationId !== null && count($ids)) {
			$table 	= $this->getPrimaryTable();
			$data 	= array('hw_location_id' => $locationId);
			
			if (isset($rackId) and  (boolean)$rackId != false) {
				$data['rack_id'] = $rackId;
			}else {
				$data['rack_id'] = null; 
			}
			//print_r($data);die('asdd');
			
			//create where clause
			$idString = "('".implode("','", $ids)."')";
			$where 	= "id in $idString";
			
			$affectedRows = $table->update($data, $where);
			return $affectedRows;
		}
		else {
			return false;
		}
	}
	
	/** 
	 * Set All the groups to witch the devices are members 
	 * All previous data is erased. 
	 * @param $groups
	 * @return unknown_type
	 */
	public function setGroups(array $devices, array $groups)
	{
		if (count($devices) && count($groups)) {
			$table = $this->getDbTable('dcms_RelGroupEquipment');
			$table->getAdapter()->beginTransaction();
			
			//deletes all previous relations for the devices 
			$where = "`equipment_id` in(".implode(',',$devices).")";  
			$result = $table->delete($where);
			echo "<br/>Number of rows deleted = ".$result; //to be added in debug.

			//All values in $devices and $groups must be != 0;
			$groups 	= array_diff($groups,array('0'));
			$devices 	= array_diff($devices,array('0'));

			//insert new relations 
			foreach ($devices as $device) {
				foreach ($groups as $group) {
					$data = array(
						'group_id'  	=> $group, 
						'equipment_id' 	=> $device,
					);
					try {
						//Zend_Debug::dump($data,'data');
						$result = $table->insert($data);	
					}
					catch (Exception $e) {
						Zend_Debug::dump($e->getMessage(),'Exception EquipmentGW->setGroup');
						return false; 
					}
				}
			}
			//commit
			$table->getAdapter()->commit();
		}
		
		//return
		return true;
	}
	
	/** 
	 * Set All the system to witch the devices are members
	 * @param $devices array 
	 * @param $systems organizers
	 * @return 
	 */
	public function setSystems($devices, $systems)
	{
		if (count($devices) && count($systems)) {
			$table = $this->getDbTable('dcms_RelSystemEquipment');
			$table->getAdapter()->beginTransaction();
			
			//deletes all previous relations for the devices 
			$where = "`equipment_id` in(".implode(',',$devices).")";  
			$result = $table->delete($where);
			echo "<br/>Number of rows deleted = ".$result; //to be added in debug.

			//All values in $devices and $systems must be != 0;
			$systems 	= array_diff($systems,array('0'));
			$devices 	= array_diff($devices,array('0'));

			//insert new relations 
			foreach ($devices as $device) {
				foreach ($systems as $system) {
					$data = array(
						'system_id'  	=> $system, 
						'equipment_id' 	=> $device,
					);
					try {
						//Zend_Debug::dump($data,'data');
						$result = $table->insert($data);	
					}
					catch (Exception $e) {
						Zend_Debug::dump($e->getMessage(),'Exception EquipmentGW->setSystems');
						return false; 
					}
				}
			}
			//commit
			$table->getAdapter()->commit();
		}
		
		//return
		return true;
	}
	
	
	/**
	 * Select Statement for Equipments  
	 *
	 * @return Zend_Db_Select 
	 */
	public function _getSelect()
	{
		//LocationSelect will be used to retrieve locations as path/to/location, instead of simple names.
		$locationsGw 	 = new Model_LocationGateway();
		$locationsSelect = $locationsGw->selectOrganizersByPath();
		$hwTypesGw  = new Model_HwTypeGateway();
		$hwTypesSelect = $hwTypesGw->selectOrganizersByPath(); 
		
		$select = $this->getPrimaryTable()->select();
		$select->setIntegrityCheck(false)
			->from(array('e' => 'dcms.equipment','*'))
			//default ->joinLeft(array('l' => 'dcms.locations'), 'l.id = e.hw_location_id', array('location'=> 'l.name'))
			->joinLeft(array('l' => (new Zend_Db_Expr('('.$locationsSelect.')'))), 'l.id = e.hw_location_id', array('hw_location'=> 'l.path'))
			->joinLeft(array('p' => 'dcms.hw_product'), 'p.id = e.hw_product_id', array('hw_product'=> 'p.name'))
			//->joinLeft(array('t' => 'dcms.hw_type'), 't.id = p.type_id', array('hw_type'=> 't.name')) //simple hw type
			->joinLeft(array('t' => (new Zend_Db_Expr('('.$hwTypesSelect.')'))), 't.id = p.type_id', array('hw_type'=> 't.path')) //full path hw type
			->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = p.manufacturer_id', array('manufacturer'=> 'm.name'))
			->joinLeft(array('r' => 'dcms.rack'), 'r.id = e.rack_id', array('rack'=> 'r.name'))
		;

		//Zend_Debug::dump($select->__toString(),'EQUIPMENT SELECT');	
		return $select;	
	}
	
	
	
	
	
	
}
?>