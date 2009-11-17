<?php

/**
 * Form for Manufacturer Add Action
 * It will be used as an input Validator when saving data to the model (Manufacturer)
 * 
 * @todo Adauga restul de atribute pentru modelul HwProduct.
 * 
 */
class forms_Manufacturer_Add extends Inno_Dojo_Form
{	
	 public function init()
	 {
	 	
	 	$this->addElement('Text', 'name', array(
	 		'label'		=> 'Manufacturer Name',
	 		'required'	=> 'true',
	 	));
	 	
	 	$this->addElement('submit', 'submit', array(
	 		'required'		=> 'false',
	 		'ignore'		=> 'true',	
	 	));
	 	
	 	/** 
	 	 * Wrap elements with div_elementName div.
	 	 */
	 	$this->wrapElements();	 
	 }
	 
}
?>
