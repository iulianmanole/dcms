<?php

require_once 'Inno/Form.php';

class forms_Location extends Inno_Form 
{
	
	function __construct($options = null)
	{
		parent::__construct($options);
		/*
		 $this->setName('Location')
		      ->setMethod('post')
			  ->setAction('/inventory/location/addLocation2');
		*/
		$newLocation = new Zend_Form_Element_Text('newLocation');
		$newLocation->addFilter('alnum',array(
											'allowWhiteSpace'=>true
										))
					->setRequired('true');					
		$submit = new Zend_Form_Element_Submit('submitNewLocation');
		$submit->setName('addLocation')
			   ->setValue('Add Location');			
			 
		$this->addElements(array( $newLocation, $submit));
	}
}

?>