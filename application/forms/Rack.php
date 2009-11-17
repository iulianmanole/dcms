<?php

class forms_Rack extends Inno_Dojo_Form 
{
	
	protected $_name  = '';

	protected $_location = '';
	
	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddRack',
								'action'=>'/inventory/rack/add',
								'method'=>'post'));
		
		/**
		 * Define form elements
		 */
		$this->_name = new Zend_Form_Element_Text('name');
		$this->_name->setLabel('Name')
					->setRequired(true);
		
		/** Equipment Location */
		$this->_location 	= new Zend_Form_Element_Select('location');
		$this->_location->setLabel('Location')
				 		->addMultiOption('',"");
					

		$this->_submit = new Zend_Form_Element_Submit('addRack', array('label'=>'Add Rack'));
		
		$this->addElements(array($this->_name, $this->_location, $this->_submit));	
		
		/** set default values */
		$this->setDefaultValues();
		/** wrap elements with div_ElemName */		
		$this->wrapElements();
	}	
	
	/**
	 * set default values for elements
	 */
	public function setDefaultValues()
	{		
		$locations	= new dcms_Locations();
		$this->_location->addMultiOptions($locations->getLocations());	 
	}
}
?>