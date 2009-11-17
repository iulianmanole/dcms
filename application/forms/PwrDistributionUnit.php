<?php

class forms_PwrDistributionUnit extends Inno_Dojo_Form 
{
	
	protected $_name  = '';
	
	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddPdu',
								'action'=>'/inventory/pwr-distribution-unit/add',
								'method'=>'post'));
		
		/**
		 * Define form elements
		 */
		$this->_name = new Zend_Form_Element_Text('name');
		$this->_name->setLabel('Name')
					->setRequired(true);
		

		$this->_submit = new Zend_Form_Element_Submit('add', array('label'=>'Add'));
		
		$this->addElements(array($this->_name, $this->_submit));	
		
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