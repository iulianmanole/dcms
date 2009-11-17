<?php
class Inno_Device {
	
	protected $_id = ''; 
	
	protected $_hostname = '';
	
	/**
	 * The device type: server, switchm, router, storage
	 */
	protected $_type = '';
	
	/**
	 * Array that will contain all the interfaces of the device, 
	 * 			as Inno_Device_Adapter objects
	 */
	
	protected $_os				= '';
	
	protected $_adapters 		= array();
	
	protected $_manufacturer 	= ''; 
	
	protected $_model			= '';
	
	protected $_isRackable		= False; 
	
	/**
	 * @var _updated determines if the object was updated. Every setter must update this value.  
	 */
	protected $_updated				=False; 
	
	
	public function __construct ($id){
		
		$this->_id = $id;		
	}

	public function setAdapter ($adapter) { 
		
	} 
	/**
	 * Retrieves the adapters array
	 */ 
	public function getAdapters() {
		
	}
	
	public function isRackable () {
		
		return $this->_isRackable;
		
	}
	
}




?>