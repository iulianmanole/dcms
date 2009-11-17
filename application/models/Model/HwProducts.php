<?php
class Model_HwProducts extends Inno_Model_ResultSet 
{	
	
	protected $_resultClass = 'Model_HwProduct';
	
	protected $_gateway; 
	

	/**
	 * 
	 */
	public function __construct($result, $gateway = null)
	{		
		if ( null === $gateway) {
			throw new Inno_Model_Exception('Gateway object must be provided in construct');
		}
		$this->setGateway($gateway);
		
		parent::__construct($result);
	}
	
	/**
	 * 
	 */
	public function getGateway()
	{
		return $this->_gateway;
	}
	
	/**
	 * 
	 */
	public function setGateway($gateway)
	{
		return $this->_gateway = $gateway;
	}
	
	/**
	 * Overload current(), to allow HwProducts instantiation with gateway parameter.
	 * 
	 * @return object, instance of _resultClass.
	 */
	public function current()
    {
        if (is_array($this->_resultSet)) {
            $result = current($this->_resultSet);
        } else {
            $result = $this->_resultSet->current();
        }
 
        if (is_array($result)) {
            $key = key($this->_resultSet);
            $this->_resultSet[$key] = new $this->_resultClass(
                $result,
                array('gateway' => $this->getGateway())
            );
            $result = $this->_resultSet[$key];
        }
        return $result;
    }
}
?>