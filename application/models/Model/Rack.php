<?php
class Model_Rack extends Inno_Model_Abstract 
{
	protected $_allowed = array (
		'id',
		'name',
		'hw_product_id',
		'hw_product',	//used for view purposes
		'manufacturer', //used for view purposes
		'location_id',
		'location',		//used for view purposes
//		'type' @deprecated
//		'height_eia_units', @deprecated
//		'own_weight',@deprecated
//		'max_weight',@deprecated
	);
	
	/**
	 * Define form prefix it is used to create the full path to the form. 
	 */
	protected $_formPrefix = 'forms_Rack_';
	
	public function __construct($data, $options)
	{
		parent::__construct($data, $options);
	}
	
	/**
	 * Fetch a certain Rack attributes 
	 * @param $criteria
	 * @return boolean
	 */
	public function fetch ($criteria = null)
	{
		$table = $this->getGateway()->getPrimaryTable();
		$select = $table->select();
			
		$select->setIntegrityCheck(false)
				->from(array('r' => 'dcms.rack'))
				->joinLeft(array('l' => 'dcms.locations'), 'l.id = r.location_id', array('location'=> 'l.name'))
				->joinLeft(array('p' => 'dcms.hw_product'), 'p.id = r.hw_product_id', array('hw_product'=> 'p.name'))
				->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = p.manufacturer_id', array('manufacturer'=> 'm.name'));
				
		if ($criteria) {
			$select->where('r.id = ?', $criteria);
		} else {
			if (isset($this->id)) {
				$select->where('r.id = ?', $this->id);
				$criteria = $this->id; 
			} else {
				if ($criteria === null) {
					throw new Inno_Model_Exception('No criteria provided. Unable to fetch model data.');
				}
			}	
		}

		$row = $table->fetchRow($select);
        if ($row) {
            $this->populate($row);
            return true;
        }		

        return false;
	}
}

?>