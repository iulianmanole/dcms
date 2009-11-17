<?php

require_once 'Inno/Form.php';

class forms_EquipmentAdd extends Inno_Dojo_Form 
{
	protected $_name  = ''; 
	
	protected $_type = ''; 
	
	protected $_manufacturer ='';

	protected $_product='';
	
	protected $_sn = '';
	
	protected $_location = ''; 
	
	protected $_rack = ''; 
	
	protected $_submit = ''; 
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		$this->_name = new Zend_Form_Element_Text('name');
		$this->_name->setLabel('Equipment Name')
					->setRequired(true);
		
			 
		
		
		$this->_type = new Zend_Form_Element_Select('type');
		$this->_type->setLabel('Type')
			 		->addValidator('GreaterThan', 'true', array(-1))
			 		->addMultiOption('', "");
			 
		/**
		 *  Manufacturer details; will be used to determine the apropriate models
		 */
		
		$this->_manufacturer = new Zend_Form_Element_Select('manufacturer');
		$this->_manufacturer->setLabel('Manufacturer')
						 	->addValidator('GreaterThan', 'true', array(-1))
						 	->addMultiOption('', "");
						 	

		/**
		 *  Model select, based on the selected typeSelect and eqManufacturer
		 *  An event will be triggered when the typeselect and eqManufacturer will be modified 
		 * 	...and we will execute /inventory/equipment/update-template-select
		 * 
		 */
		$this->_product = new Zend_Form_Element_Select('product');
		$this->_product->setLabel('Product')
			  ->addMultiOption('',"");

		
		/** Serial Number */
		$this->_sn = new Zend_Form_Element_Text('sn');
		$this->_sn->setLabel('Serial Number');
				//->setRequired(true)	  	  
			  
			  
		/** Equipment Location */
		$this->_location 	= new Zend_Form_Element_Select('location');
		$this->_location->setLabel('Location')
				 		->addMultiOption('',"");
		
		/** 
		 * Rack, depends on the physical location.
		 * It will be populated later, based on the selection made by the user.
		 */ 		 
		$this->_rack = new Zend_Form_Element_Select('rack');
		$this->_rack->setLabel('Rack')
			 		->addMultiOption('',"");			 
			 	 
				 
		$this->_submit = new Zend_Form_Element_Submit('addEquipment', array('name'=>'Add Equipment'));
		
		/** Populate Form with default values */
		$this->setDefaultValues();		
		
		$this->addElements(array($this->_name, $this->_type, $this->_manufacturer, $this->_product, $this->_sn, $this->_location, $this->_rack, $this->_submit));	
		
				
		/** wrap elements with div_ElemName */		
		$this->wrapElements();
	}

	public function setDefaultValues()
	{	
		$types = new dcms_TypesHw();
		$this->_type->addMultiOptions($types->findByKey(array(), 'id', 'type'));
		
		$manufacturers = new dcms_Manufacturers();
		$this->_manufacturer->addMultiOptions($manufacturers->findByKey(array(), 'id', 'name'));	 		
		
		$locations	= new dcms_Locations();
		$this->_location->addMultiOptions($locations->getLocations());	 			
	}
	
}
?>