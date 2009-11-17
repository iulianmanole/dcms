<?php

class Model_Manufacturer extends Inno_Model_Abstract 
{	
	protected $_allowed = array (
		'id',
		'name',
	);
	
	/**
	 * Define form prefix it is used to create the full path to the form. 
	 */
	protected $_formPrefix = 'forms_Manufacturer_';
	
	public function __construct($data, $options)
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
		$select = $this->getGateway()->_getSelect();
		
		if ($criteria) {
			$select->where('m.id = ?', $criteria);
		} else {
			if (isset($this->id)) {
				$select->where('m.id = ?', $this->id);
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
	
}

?>