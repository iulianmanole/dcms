<?php

class forms_Manufacturer extends Inno_Dojo_Form 
{
	
	protected $_name  = ''; 
	
	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddProduct',
								'action'=>'/inventory/manufacturer/add',
								'method'=>'post'));
		
		$this->_name = new Zend_Form_Element_Text('name');
		$this->_name->setRequired(true)
			 		->setLabel('Manufacturer Name');
	

		$this->_submit = new Zend_Form_Element_Submit('addManufacturer', array('name'=>'Add Manufacturer'));
		
		$this->addElements(array($this->_name, $this->_type, $this->_manufacturer, $this->_submit));	
		
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
		
	}
}
?>