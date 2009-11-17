<?php

Class Inno_Equipment_Rack extends Inno_Equipment 
{
	/**
	 *  All PDUs of a rack, as Inno_Component_Power objects
	 * 
	 * @var array
	 */
	protected $_pdus = array();
	
	/**
	 *  Rack's own weight when it is empty 
	 */
	protected $_ownWeight; 
	/**
	 *  Current weight, based on the devices that it contains.
	 *  
	 *  @var int
	 */
	protected $_currentWeight;
	
	/**
	 *  Maximum weight that the Rack can support and includes his own weight.
	 * 
	 * @var int
	 */
	protected $_maxWeight;
	
	/**
	 *  Flag that signals if there are any weight problems. Default is False.
	 * 
	 * @var boolean
	 */
	protected $_weightProblem;
	
	
	public function getCurrentWeight () 
	{
		return $this->_currentWeight;
	}
	
	public function setCurrentWeight ($currentWeight) 
	{
		$this->_currentWeight = $currentWeight;
	}
	
	public function getMaxWeight () 
	{
		return $this->_maxWeight;
	}
	
	public function setMaxWeight ($maxWeight) 
	{
		$this->_maxWeight = $maxWeight;
	}
	
	protected function _setWeightProblem () 
	{
		if ( $this->getCurrentWeight() >= $this->getMaxWeight() ) {
			$this->_weightProblem = True; 
		}else {
			$this->_weightProblem = False;
		}
	}
	
	
}

?>