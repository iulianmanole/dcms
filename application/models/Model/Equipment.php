<?php
/**
 * @author Iulian
 *@TODO find a way to restrict the select to certain hardware types 
 *			( for example exclude racks because they have the own controller)
 *
 *Supported hw types should be: servers, routers, switches, modems ( ..generally active equipments.)
 *Maybe if we add a new column in database ( active equipment?)
 *
 *  -- Cand procesezi form-ul, daca echipament-ul introdus are chassis...trebuie sa adaugi un rack nou.
 */
class Model_Equipment extends Inno_Model_Abstract
{
	protected $_defaultGatewayClass = 'Model_EquipmentGateway';
	
	protected $_allowed = array (
		'id',
		'name',
		'description',
		'hw_type',			//used for view purposes
		'manufacturer',		//used for view purposes
		'hw_product',		//used for view purposes
		'hw_product_id',
		'hw_location_id',
		'hw_location',		//used for view purposes
		'SN',
		'rack_id',
		'rack',				//used for view purposes
		'warranty_start',
		'warranty_end',
		'state_id',
		'state'				//used for view purpose
		
	);
	
	/**
	 * Define form prefix it is used to create the full path to the form. 
	 */
	protected $_formPrefix = 'forms_Equipment_';
	
	/**
	 * @return Model_EquipmentGateway
	 */
	public function getGateway() 
	{
		return parent::getGateway(); 
	}
	
	public function fetch($criteria)
	{
		$modelGw = $this->getGateway();
		$select = $modelGw->_getSelect();

		if ($criteria) {
			$select->where('e.id = ?', $criteria);
		} else {
			if (isset($this->id)) {
				$select->where('e.id = ?', $this->id);
				$criteria = $this->id; 
			} else {
				if ($criteria === null) {
					throw new Inno_Model_Exception('No criteria provided. Unable to fetch model data.');
				}
			}	
		}
		
		$row = $modelGw->getPrimaryTable()->fetchRow($select);
        if ($row) {
            $this->populate($row);
            return true;
        }		
        return false;
	}
	
	public function save($data, $form = null)
	{
		//save the object, by using the default save.
		return parent::save($data,$form);
	
	}
	//  ....to continue.
	
}
?>