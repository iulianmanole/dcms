<?php
/**
 * The model class represent a single object model from domain.
 * It contains the logic to manipulate this single object.
 * 
 * It will rely heavily on the Model Gateway for data access.
 * 
 * 
 */

abstract class Inno_Model_Abstract extends Inno_Model_Value 
{
	/**
	 * gateway object.
	 * Gateway Object takes precedence over defaultGateway.
	 * 
	 * @var Inno_Model_Gateway
	 */
	protected $_gateway;
	
	/**
	 * default gateway Class
	 * If the gateway object is not set using options, 
	 * the constructor will try to instantiate the defaultGatewayClass.
	 *  
	 * @var string 
	 */
	protected $_defaultGatewayClass;
	
	
	/**
	 * Forms that can be addressed in this model
	 * Form will be used for : 
	 * 		1. Data validation
	 * 		2. In views, for display
	 * @var array registry of form objects.   
	 */
	protected $_forms = array();
	
	/**
	 * prefix of form classes. 
	 * 
	 */
	protected $_formPrefix = 'forms_';
	
	/**
	 * 
	 * @param array $data
	 * @param array $options
	 * @return unknown_type
	 */
	public function __construct(array $data = array(), array $options = array())
	{
		parent::__construct($data, $options);
		
		if ( null === $this->getGateway()) {
			//Gateway is not set, so we try to instantiate the default.
			if (class_exists($this->_defaultGatewayClass)) {
				$gw = new $this->_defaultGatewayClass();
				$this->setGateway($gw);
			}
			else {
				throw new Inno_Model_Exception('class ['.$this->_defaultGatewayClass.'] doesn\'t exists.'
								.'Gateway Object is not set.'
								.'It must be set via $model->setGateway(),' 
								.'construct options or _defaultGatewayClass property.');
			}
		}
	}
	
	/**
	 * set Gateway property 
	 *
	 * @param Inno_Model_Gateway object
	 * @return Inno_Model_Abstract 
	 * 
	 */
	public function setGateway(Inno_Model_Gateway $gateway) 
	{
		$this->_gateway = $gateway; 
		return $this; 
	}
	
	/**
	 * @return Inno_Model_Gateway
	 */
	public function getGateway() 
	{
		return $this->_gateway; 
	}
	
 	/**
	 * Get a form attached to the model.
	 *  @param string $type of the form (it will be used to instantiate the form_$type class)  
	 *  @return Inno_Dojo_Form | null 
	 */
	public function getForm($type = null)
	{
		$type  = ucfirst($type);
		
		if (!isset($this->_forms[$type])) {
            $class = $this->_formPrefix . $type;
    
            //Instantiate form 
            if ( class_exists($class) ) {
            	$this->_forms[$type] = new $class;
            }
            else { 
            	throw new Inno_Model_Exception('Form Class > ' . $class . ' < doesn\'t exist.' . 
            									'Unable to instantiate form.'
            									,Inno_Model_Exception::INEXISTENT_CLASS); 
            }
        }
        return $this->_forms[$type];
	} 
	
	/**
	 * @TODO After enabling this abstract function you must modify all the classes to implement this function
	 * 
	 * fetch model data based on a supplied criteria or on the current id property value.
	 * Must be defined in all subclasses
	 * 
	 * @param $criteria
	 * @return boolean
	 */
	//abstract function fetch($criteria);

	/**
	 * Saves data to backend storage. 
	 * It will save the attributes that exist both in _data and in storage. 
	 * Before saving data it can update object data. 
	 * !Atention: It assumes that the primary Key is ID.
	 * 		@TODO ...allow for other primary keys, different from ID.    
	 *
	 * @param array|object data that will be updated before saving the object.  
	 * @return saved id value  or false in case of failure  
	 */
	public function save (array $data, $form = null)
	{
		//update object with $data
		if (null !== $data) {
			$data = (array) $data;
			$inputFilter = $this->getForm('add');
			if (!$inputFilter->isValid($data)) { 
				return false;
			}
			$this->populate($inputFilter->getValues($data));
		}
		//save object to backend
		$table = $this->getGateway()->getPrimaryTable();
		//retrieve all cals of the table.
		foreach ($table->info('cols') as $key => $value ){
			$tableAllowedCols[$value] = '';
		}
		
		//we will insert ONLY the model properties that are defined in DB
		$data = array_intersect_key($this->_data, $tableAllowedCols);
		
		if (!$this->id) {
			$id = $table->insert($data);
			$this->id = $id;
		}else {
			//data that will be updated must not contain primary key(ID).
			//We will use PK in where clause/
            unset($data['id']);
            
            $id = $this->id;
            $where = $table->getAdapter()->quoteInto('id = ?', $id);
           	
            $table->update($data, $where);	
		}
		return $id;
	}
	
	/**
	 * Default delete function. Performs a simple delete in the primary table.
	 * @param string primary Key name. By default it is considered 'id'
	 * 
	 * @return boolean 
	 */
	public function delete($primaryKey = 'id')
	{
		$table = $this->getGateway()->getPrimaryTable();
		
		$id 	= $this->id; 
		$where 	= $table->getAdapter()->quoteInto($primaryKey. ' = ?', $id);
		
		if ($table->delete($where)) {
			//clean object data
			$this->_data = array();
			return true; 
		}else {
			return false; 
		}
	}
	
	public function __call($methodName, $args)
	{
		throw new Zend_Exception(sprintf('Method "%s" does not exist and was not trapped in __call()', $methodName), 500);
	}
	
}
?>