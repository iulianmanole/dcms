<?php
/** Include Inno_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';

class dcms_HwType extends Inno_Db_Table_Abstract 
{
	protected $_name = "dcms.hw_type";
	
	/**
	 * @deprecated Get all hardware types defined in db. Model_HwType object family. 
	 * @param $class - restrict the result to only a certain class of hardware types ( equipment, power, passive)
	 * @return assoc array $result (PK => name) 
	 *  
	 */
	public function getHwTypes($class = null)
	{
		trigger_error('This method is deprecated. please use Model_HwType object family');
		
		$select = $this->getAdapter()->select(); 
		$select->from('dcms.hw_type',array('id','name'));
		if ($class !== null ) {
			$select->where('name = ?', $class);			
		}
		//debug
		//Zend_Debug::dump($select->__toString(), 'HWTYPES');
		
		//perform the fetch and prepare the result. 
		$rawResult = $this->getAdapter()->fetchAssoc($select);						
		$result = array();
		foreach ($rawResult as $item) {
			$result[$item['id']] = $item['name']; 
		}			
		
		return $result;			
	}
}
?>