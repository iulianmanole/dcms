<?php
require_once 'Inno/Dojo/Form.php';

class forms_ProductHw extends Inno_Dojo_Form 
{
	
	protected $_name  = ''; 
	
	protected $_type = ''; 
	
	public $_manufacturer ='';

	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddProduct',
								'action'=>'/inventory/product-hw/add',
								'method'=>'post'));
		
		$this->_name = new Zend_Form_Element_Text('name');
		$this->_name->setRequired(true)
			 		->setLabel('Product Name');
	
		/**
		 * Type, it is used for classification   
		 */			
		$this->_type = new Zend_Form_Element_Select('type');
		$this->_type->setLabel('Type');

		/**
		 *  Manufacturer details
		 */
		$this->_manufacturer = new Zend_Form_Element_Select('manufacturer');
		$this->_manufacturer->setLabel('Manufacturer');
		
		$this->_submit = new Zend_Form_Element_Submit('addProduct', array('name'=>'Add Product'));
		
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
		
		$types = new dcms_TypesHw();
		$this->_type->addMultiOption('', "")
			 		->addMultiOptions($types->findByKey(array(), 'id', 'type'));
		
		
		$manufacturers = new dcms_Manufacturers();
		$this->_manufacturer->addMultiOption('', "")
					 		->addMultiOptions($manufacturers->findByKey(array(), 'id', 'name'));
					 		
		
	}
}
?>