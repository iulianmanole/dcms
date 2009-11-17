<?php

Abstract Class Inno_Equipment 
{

	/**
	 *  Identifier for this object. Based on id, the object will be mapped to db
	 */
	protected $_id;

	protected $_name; 
	/**
	 * Equipment type, will be restricted, based on the class.
	 */
	protected $_type;

/** deprecated, must be removed */	
	protected $_model;

	protected $_manufacturer;

	protected $_partNumber;

	protected $_serialNumber;

	/**
	 * Equipment dimensions
	 */
	protected $_width;

	protected $_depth;

	protected $_height;

	protected $_weight;

	protected $_isRackable;

	protected $_heightEiaUnits;

	protected $_thermalOutput;

	protected $_airFlow;

	protected $_requireGrounding;

	/**
	 * A template of one equipment is used to add rapidly others equipments that have the same characteristics.
	 * A template will have only template components.
	 */
	protected $_isTemplate;

	/**
	 * Init an equipment model with default values.
	 */

	public function __construct()
	{
		$this->_id 					= null;
		$this->_type 				= null;
		$this->_model 				= null;
		$this->_manufacturer		= null;
		$this->_partNumber			= null;
		$this->_serialNumber		= null;
		$this->_width				= null;
		$this->_depth				= null;
		$this->_height				= null;
		$this->_weight				= null;
		$this->_isRackable			= null;
		$this->_heightEiaUnits		= null;
		$this->_thermalOutput		= null;
		$this->_airFlow				= null;
		$this->_requireGrounding	= null;

	}


	/**
	 * Getters and setters for protected and private variables.
	 *
	 */

	public function getName () 
	{

		return $this->_name;
	}
	
	public function setName ($name) 
	{

		$this->_name = $name ;
	}
	
	public function getType () {

		return $this->_type;
	}

	public function setType ($type) {

		$this->_type = $type ;
	}

	public function getModel () {

		return $this->_model;
	}

	public function setModel ($model) {

		$this->_model = $model ;
	}

	public function getManufacturer () {

		return $this->_manufacturer ;
	}

	public function setManufacturer ($manufacturer) {

		$this->_manufacturer = $manufacturer ;
	}

	public function getPartNumber () {

		return $this->_partNumber ;
	}

	public function setPartNumber ($partNumber) 
	{

		$this->_partNumber = $partNumber ;
	}

	public function getSerialNumber () {

		return $this->_serialNumber ;
	}

	public function setSerialNumber ($serialNumber) {

		$this->_serialNumber = $serialNumber ;
	}

	public function getWidth () {

		return $this->_width;
	}

	public function setWidth ($width) {
		$this->_width = $width ;
	}

	public function getHeight () {

		return $this->_height ;
	}

	public function setHeight ($height) {

		$this->_height = $height ;
	}

	public function getDepth () {

		return $this->_depth ;
	}

	public function setDepth ($depth) {

		$this->_depth = $depth ;
	}

	public function getWeight () {

		return $this->_weight ;
	}

	public function setWeight ($weight) {

		$this->_weight = $weight ;
	}

	public function getHeithEiaUnits () {

		return $this->_heightEiaUnits ;
	}

	public function setHeightEiaUnits ($heightEiaUnits) {

		$this->_heightEiaUnits = $heightEiaUnits;
	}

	public function isRackable () {

		return $this->_isRackable;
	}

	public function setAsRackable (boolean $isRackable) {

		$this->_isRackable = $isRackable;
	}

	public function getThermalOutput () {

		return $this->_thermalOutput ;
	}

	public function setThermalOutput ($thermalOutput) {

		$this->_thermalOutput = $thermalOutput ;
	}




}
?>