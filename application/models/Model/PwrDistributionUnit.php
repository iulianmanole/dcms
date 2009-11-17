<?php
/**
 * Pwr Distribution Unit is the model that defines general PDUs used to power on equipments and rackPDUs 
 * 
 */
class Model_PwrDistributionUnit extends Inno_Model_Abstract 
{
	protected $_allowed = array (
		'id',
		'name',
		//'inputs',  //integer 
		//'outputs', //integer
		'hw_product',
		'hw_product_id',
		'hw_type' 	=> 'Equipment.PDU',
		'hw_type_id',
		'manufacturer'
	);
	
	/**
	 * Define form prefix it is used to create the full path to the form. 
	 */
	protected $_formPrefix = 'forms_PwrDistributionUnit_';
	
	/**
	 * @param integer id of the PDU 
	 */
	public function fetch($criteria = null)
	{
		
		$table 	 = $this->getGateway()->getDbTable('dcms_PwrDistributionUnit');
		$select = $table->select();
		
		$select->setIntegrityCheck(false) 
				->from(array('p'=> 'dcms.pwr_distrib_unit'))
				->joinLeft(array('h' => 'dcms.hw_product'), 'h.id = p.hw_product_id', array('hw_product'=> 'h.name'))
				->joinLeft(array('m' => 'dcms.manufacturer'), 'm.id = h.manufacturer_id', array('manufacturer'=> 'm.name'));
				//->joinLeft(array('t' => 'dcms.hw_type'), 't.id = p.type_id', array('type'=> 't.type'));
		
		if ($criteria) {
			$select->where('p.id = ?', $criteria);
		} else {
			if (isset($this->id)) {
				$select->where('p.id = ?', $this->id);
				$criteria = $this->id; 
			} else {
				if ($criteria === null) {
					//throw new Inno_Model_Exception('No criteria provided. Unable to fetch model data.');
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
}
?>