<?php

/** Include Inno_Equipment */ 
require_once 'Inno/Equipment.php';


/**
 * Defines a Device. A device is formed from HW and Software. 
 *
 */
Class Inno_Equipment_Device extends Inno_Equipment 
{
	
	/**
	 * All Adapters of this device, as Inno_Equipment_Component_Adapter objects 
	 * 
	 * @var array
	 */  
	protected $_adapters = array();
	
	/**
	 * All Power Supplies of this device, as Inno_Equipment_Component_Power objects
	 * 
	 * @var array
	 */
	protected $_powerSupplies = array();
	
	/**
	 * The minimum number of Power Supplies required for this device to work properly.
	 * 
	 * @var int
	 */
	protected $_minPowerSupplies;

	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function getAdapters () 
	{
		return $this->_adapters; 
	}
	
	public function setAdapter (Inno_Equipment_Component_Adapter  $adapter) 
	{
		$this->adapter[] = $adapter; 	
	}
	
	public function getPowerSupplies () 
	{
		return $this->_powerSupplies;
	}
	
	public function setPowerSupply(Inno_Equipment_Component_Power $powerSupply )
	{
		$this->_powerSupplies = $powerSupply; 
	}
	
	public function getMinPowerSupplies () 
	{
		return (int) $this->_minPowerSupplies;
	}
	
	public function setMinPowerSupplies ($minPowerSupplies)
	{
		$this->_minPowerSupplies = (int) $minPowerSupplies;
	}
	
}


?>