<?php

class forms_TypeHw extends Inno_Dojo_Form 
{
	
	protected $_type  = ''; 
	
	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddType',
								'action'=>'/inventory/type-hw/add',
								'method'=>'post'));
		
		$this->_type = new Zend_Form_Element_Text('type');
		$this->_type->setRequired(true)
			 		->setLabel('Hardware Type ');
	

		$this->_submit = new Zend_Form_Element_Submit('addType', array('name'=>'Add Type'));
		
		$this->addElements(array($this->_type, $this->_submit));	
		
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