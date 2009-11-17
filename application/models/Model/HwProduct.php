<?php

class Model_HwProduct extends Inno_Model_Abstract 
{	
	protected $_allowed = array (
		'id',
		'name',
		'type',
		'type_id', 				
		'manufacturer',		
		'manufacturer_id', 	
		'weight',
		'width',
		'depth',
		'height',
		'height_eia_units',	
		'is_rackable',
		'is_standalone',
		'thermal_output',	
		'air_flow',
		'require_grounding'
	);
	
	/**
	 * Define form prefix it is used to create the full path to the form. 
	 */
	protected $_formPrefix = 'forms_HwProduct_';

	/** 
	 * default Gateway Class.
	 */
	protected $_defaultGatewayClass = 'Model_HwProductGateway';
	
	public function __construct(array $data = array(), array $options = array())
	{
		parent::__construct($data, $options);
	}
	
	/**
	 * fetch model data based on a supplied criteria or on the current id property value.
	 *  
	 * @param integer criteria 
	 * @return boolean  
	 */
	public function fetch($criteria = null)
	{ 
		
		$table 	 = $this->getGateway()->getDbTable('dcms_HwProduct');
		$select = $table->select();

		/**
		 * By default table objects do not allow join queries, because they break the concept 
		 * 	that a table object represent only one table.
		 * If we set setIntegrityCheck to false, we can perform joins, but the table object will be 
		 *  in readonly state. 
		 */
		$select->setIntegrityCheck(false)
				->from(array('p'=> 'dcms.hw_product'))
				->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = p.manufacturer_id', array('manufacturer'=> 'm.name'))
				->joinLeft(array('t' => 'dcms.hw_type'), 't.id = p.id', array('type'=> 't.name'));

		if ($criteria) {
			$select->where('p.id = ?', $criteria);
		} else {
			if (isset($this->id)) {
				$select->where('p.id = ?', $this->id);
				$criteria = $this->id; 
			} else {
				if ($criteria === null) {
					throw new Inno_Model_Exception('No criteria provided. Unable to fetch model data.');
				}
			}	
		}
		//echo $select->__toString();
		
		$row = $table->fetchRow($select);
        if ($row) {
            $this->populate($row);
            return true;
        }		

        return false;
	}
	
	/**
	 * save data by using default save method.
	 * @see application/library/Inno/Model/Inno_Model_Abstract#save()
	 */
	public function save($data, $form = null)
	{
		return parent::save($data, $form);
	} 
	
	/**
	 * Proxy to parent::delete default delete function.
	 * @return parent::delete
	 * @see application/library/Inno/Model/Inno_Model_Abstract#delete()
	 */
	public function delete($primaryKey = 'id')
	{
		return parent::delete($primaryKey);
	}
	
	/** 
	 * 
	 * @return true if this hardware product is standalone, false otherwise
	 */
	public function isStandalone()
	{
		if (!isset($this->_data['is_standalone'])){
			$this->fetch();
		}
		
		return (boolean)$this->_data['is_standalone'];
	}
	
	
}

?>