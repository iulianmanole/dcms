<?php

require_once 'Inno/Form.php';

class forms_Equipment extends Inno_Form 
{
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/*
		$this->setName('equipment')
			 ->setAction($formAction)
			 ->setMethod($formMethod);
		*/
			 
		$name = new Zend_Form_Element_Text('eqName');
		$name->setRequired(true);

		$locationList = new Zend_Form_Element_Select('locationList');
				
		/**
		 * Retrieve elements from the Database, $arrLocationList is for testing.
		 */
		$arrLocationList =array(
							'1'=> 'Datacenter Berceni/Productie', 
							'2'=> 'Datacenter Berceni/Testare');
		$locationList->addMultiOptions($arrLocationList);
		
		$newLocation 	= new Zend_Form_Element_Text('newLocation');
		
		$addNewLocation = new Zend_Form_Element_Button('addNewLocation');
		
		$submit = new Zend_Form_Element_Submit('addEquipment', array('name'=>'Add Equipment'));
		
		
		$this->addElements(array($name, $locationList, $newLocation, $addNewLocation, $submit));		
	}
}
		
?>