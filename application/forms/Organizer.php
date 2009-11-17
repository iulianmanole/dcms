<?php
require_once 'Inno/Form.php';

class forms_Organizer extends Inno_Form 
{
	
	function __construct($options = null)
	{
		parent::__construct($options);
	
		 $this->setName('Organizer')
		      ->setMethod('post');
			  
		
		$organizer = new Zend_Form_Element_Text('organizer');
		$organizer->addFilter('alnum',array(
											'allowWhiteSpace' => true
										))
					->setRequired('true')
					->setLabel('Organizer');					
		
		$submit = new Zend_Form_Element_Submit('add');
		$submit->setValue('Add Organizer');			
			 
		$this->addElements(array( $organizer, $submit));
	}
}

?>