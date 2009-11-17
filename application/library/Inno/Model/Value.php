<?php
/**
 * Base Value Objects for models.
 *
 * An Value Object encapsulate a set of properties(values) that
 * 	define an object.
 *
 *
 */

abstract class Inno_Model_Value
{
	/**
	 * Array of allowed properties for the model
	 * These properties can be modified.
	 *
	 * @var array of allowed attributes.
	 */
	protected $_allowed = array ();

	/**
	 * Properties that were set for this object.
	 *
	 * @var array defined properties
	 */
	protected $_data = array ();

	public function __construct($data, $options = null)
	{
		if (empty($this->_allowed)) {
			throw new Inno_Model_Exception('No allowed fields specified!');
		}

		$this->setOptions($options)
		->populate($data);
	}

	/**
	 * Set options
	 * @param array $options
	 * @return Inno_Model_Value
	 */
	public function setOptions($options)
	{
		if (null === $options) {
			return $this;
		}

		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		} elseif (is_object($options)) {
			$options = (array) $options;
		}

		if (!is_array($options)) {
			throw new Inno_Model_Exception('Invalid options provided; must be an array or Config object');
		}

		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	/**
	 * Overload: set object property
	 *
	 * If not in {$_allowed} array, reject the option.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 * @throws Inno_Model_Exception
	 */
	public function __set($key, $value)
	{
		if (!in_array($key, $this->_allowed)) {
			throw new Inno_Model_Exception('Result does not allow setting arbitrary values ("' . $key . '")');
		}
		$this->_data[$key] = $value;
	}

	/**
	 * Overload: retrieve property
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (!array_key_exists($key, $this->_data)) {
			return null;
		}
		return $this->_data[$key];
	}

	/**
	 * Overload: is property set?
	 *
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->_data[$key]);
	}

	/**
	 * Overload: unset property
	 *
	 * @param string $key
	 * @return void
	 */
	public function __unset($key)
	{
		if ($this->__isset($key)) {
			unset($this->_data[$key]);
		}
	}

	/**
	 * Populate object with provided data
	 *
	 * @param array|object $data
	 * @return Inno_Model_Value
	 * @throws Inno_Model_Exception for invalid $data type
	 */
	public function populate($data)
	{
		if (null === $data) {
			return;
		}

		if (is_object($data) && method_exists($data, 'toArray')) {
			$data = $data->toArray();
		} elseif (is_object($data)) {
			$data = (array) $data;
		}

		if (!is_array($data)) {
			throw new Inno_Model_Exception('Invalid data provided to constructor');
		}

		foreach ($data as $key => $value) {
			if($value == '0') {
				//where the value is 0 or false,  we set it as null 
				// or otherwise if the $key is a FK the insert in db will fail. 
				$value = null;
			}
			$this->{$key} = $value;
			
		}
	}

	/**
	 * Cast value to array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_data;
	}

	/**
	 * Cast value to JSON
	 *
	 * @return string
	 */
	public function toJson()
	{
		return Zend_Json::encode($this->toArray());
	}
}
?>