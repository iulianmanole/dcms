<?php

Class Inno_Equipment_PatchPannel extends Inno_Equipment 
{
	protected $_totalPortsCount;
	
	protected $_freePortsCount;
	
	/**
	 * Connector type 
	 * ex: Ehernet Cat5E
	 * 
	 */
	protected $_portConnectorType;
	
	public function getTotalPortsCount ()
	{
		return (int) $this->_totalPortsCount;
	}
	
	public function setTotalPortsCount ($totalPortsCount)
	{
		$this->_totalPortsCount = (int) $totalPortsCount;
	}
	
	public function incFreePortsCount () 
	{
		$this->_freePortsCount++;
	}
	
	public function decFreePortsCount ()
	{
		$this->_freePortsCount--;
	}
	
	public function getPortConnectorType ()
	{
		return $this->_portConnectorType;
	}
	
	public function setPortConnectorType ($portConnectorType)
	{
		$this->_portConnectorType = $portConnectorType;
	}
	
	
}

?>