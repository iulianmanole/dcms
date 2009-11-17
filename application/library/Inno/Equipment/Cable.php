<?php

Class Inno_Equipment_Cable extends Inno_Equipment 
{
	protected $_label; 
	protected $_length; 
	/**
	 * Describes this cable connectors
	 * ex : Fiber SC/LC 
	 */
	protected $_connectors = array();

	public function getLength ()
	{
		return $this->_length; 
	}
	
	public function setLength ($length)
	{
		$this->_length = $length;
	}
	
	public function getLabel ()
	{
		return $this->_label;
	}
	
	public function setLabel ($label)
	{ 
		$this->_label  = $label;
	}
	
	public function setConnectors (array $connectors)
	{
		$this->_connectors = $connectors;
	}
}

?>