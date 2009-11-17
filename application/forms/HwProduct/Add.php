<?php

/**
 * Form for HwProduct Add Action
 * It will be used as an input Validator when saving data to the model (HwProduct)
 * 
 * @todo Adauga restul de atribute pentru modelul HwProduct.
 * 
 */
class forms_HwProduct_Add extends Inno_Dojo_Form
{
	protected $_model;
	
	 public function init()
	 {
	 	$types  = $this->getModel()->getHwTypes();
	 	$mfctrs = $this->getModel()->getManufacturers();
	 	
	 	
	 	$this->addElement('Text', 'name', array(
	 		'label'		=> 'Product Name',
	 		'required'	=> 'true',
	 	));
	 	
		$this->addElement('Select', 'type_id', array(
	 		'label'			=> 'Hardware Type',
	 		'required'		=> 'true',
			'multiOptions'	=> array('')+$types,
			'validators'	=>array(	
								array('InArray',false,array(array_keys($types)))
			),
			'errorMessages' => array ('Please select a value from the drop-down list'),					
	 	));	
		
	 	$this->addElement('Select', 'manufacturer_id', array(
	 		'label'			=> 'Manufacturer',
	 		'required'		=> 'true',
			'multiOptions'	=> array('')+$mfctrs,
			'validators'	=>array(
								array('InArray',false,array(array_keys($types)))
			),		
			'errorMessages' => array ('Please select a value from the drop-down list'),
	 	));
		
		$this->addElement('Text', 'weight', array (
			'label' 	=> 'Weight (Kg)',
			'required' 	=> true,
			'validators'=> array ('Digits'),
			'value'	=> '0'
		));
		
		$this->addElement('text', 'width', array(
			'label'		=> 'Width (mm)',
			'validators'=> array ('Digits'),
			'value'		=> '0'
		));
		
		$this->addElement('text', 'height', array( 
			'label'		=> 'Height (mm)',
			'validators'=> array ('NotEmpty'),
			'required' 	=> true,
			'value'		=> '0'
		));
		
		$this->addElement('text', 'depth', array(
			'label'		=> 'depth (mm)',
			'validators'=> array ('Digits'),
			'required' 	=> true,
			'value'		=> '0'
		));
		
		$this->addElement('text', 'height_eia_units', array(
			'label'		=> 'Height (Rack Units)',
			'validators'=> array ('Digits'),
			'required' 	=> true,
			'value'	=> '0'
		));
		
		$this->addElement('text', 'air_flow', array(
			'label'		=> 'Air Flow (Cubic meters / min)',
			'validators'=> array ('Digits'),
			'required' 	=> true,
			'value'	=> '0'
		));
		
		$this->addElement('Radio', 'is_rackable', array(
			'Label' => 'Is Rackable?(can be racked in a standard cabinet)',
			'required' => true, 
			'multiOptions'=> array (true => 'Yes', false => 'No')
		));
		
		$this->addElement('Radio', 'is_standalone', array(
			'Label' => 'Is Standalone?(comes with his own rack)',
			'required' => true, 
			'multiOptions'=> array (true => 'Yes', false => 'No')
		));
		
		$this->addElement('Radio', 'require_grounding', array(
			'Label' => 'Requires Grounding?',
			'required' => true, 
			'multiOptions'=> array (true => 'Yes', false => 'No')
		));
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit	->setRequired(false)
			 	->setIgnore(true); //true --> ignore submit button when form is processed.

		$this->addElement($submit);	 
	 		
	 }
	 
	 
	 public function setModel($model)
	 {
	 	$this->_model = $model; 
	 	return $this; 
	 }
	 
	 /**
	  * 
	  * @return Model_HwProductGateway
	  */
	 public function getModel()
	 {
	 	if (null === $this->_model) {
	 		$this->setModel(new Model_HwProductGateway());
	 	}	
	 	return $this->_model;
	 }
}
?>
