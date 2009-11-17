<?php

/**
 * This class is used to describe all Power related components.
 * 
 */


Class Inno_Equipment_Component_Power extends Inno_Equipment_Component {

	/**
	 * @var $_inputPowerConn represent the number of input connections to 
	 * 						this  component
	 */
	protected $_inputConn = ''; 
	
	/**
	 * @var $_inputPowerPerConn represent the maximum power per one input 
	 * 						connection
	 */
	protected $_inputPowerPerConn = ''; 
	
	/**
	 *  Plug type for the input connections
	 */
	protected $_inputConnPlugType ='';
	
	protected $_inputPhase = '';
	
	/**
	 * @var $_outputConn represent the number of output connections that 
	 * 						this  component can provide
	 */
	protected $_outputConn = ''; 
	
	/**
	 * @var $_outputPowerPerConn represent the maximum power per one output 
	 * 						connection
	 */
	protected $_outputPowerPerConn = ''; 
	
	/**
	 *  Plug type for the output connections
	 */
	protected $_outputConnPlugType ='';
	
	protected $_outputPhase = '';
	
	
	public function getInputConn () {
		
		return $this->_inputConn ;
	}
	
	public function setInputConn ($inputConn) {
		
		$this->_inputConn = $inputConn ; 
	}
	
	public function getInputPowerPerConn () {
		
		return $this->_inputPowerPerConn ;
	}
	
	public function setInputPowerPerConn ($inputPowerPerConn) {
		
		$this->_inputPowerPerConn = $inputConnPlugType ; 
	}
	
	public function getInputConnPlugType () {
		
		return $this->_inputConnPlugType ;
	}
	
	public function setInputConnPlugType ($inputConnPlugType) {
		
		$this->_inputConnPlugType = $inputConnPlugType ; 
	}
	
	public function getInputPhase () {
		
		return $this->_inputPhase ;
	}
	
	public function setOutputPhase ($inputPhase) {
		
		$this->_inputPhase = $inputPhase ; 
	}
	
	public function getOutputConn () {
		
		return $this->_outputConn ;
	}
	
	public function setOutputConn ($outputConn) {
		
		$this->_outputConn = $outputConn ; 
	}
	
	public function getOutputPowerPerConn () {
		
		return $this->_outputPowerPerConn ;
	}
	
	public function setOutputPowerPerConn ($outputPowerPerConn) {
		
		$this->_outputPowerPerConn = $outputConnPlugType ; 
	}
	
	public function getOutputConnPlugType () {
		
		return $this->_outputConnPlugType ;
	}
	
	public function setOutputConnPlugType ($outputConnPlugType) {
		
		$this->_outputConnPlugType = $outputConnPlugType ; 
	}
	
	public function getOutputPhase () {
		
		return $this->_outputPhase ;
	}
	
	public function setOutputPhase ($outputPhase) {
		
		$this->_outputPhase = $outputPhase ; 
	}
	
	
	
	
}
?>