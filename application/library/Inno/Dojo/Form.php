<?php

/** Zend_Form */
require_once 'Zend/Dojo/Form.php';


class Inno_Dojo_Form extends Zend_Dojo_Form
{	

	/**
	 * Check form data against server side Data( stored in a session).
	 * Avoids data forgery for MultiOptions elements ( ex: Select)
	 */ 
	public function isValidData($data)
	{
		//@todo
	}
	/**
	 * Add div decorator to every Elem. 
	 * the div will wrap the element.
	 * It is used when we replace the elements, because Dojo is not aware of element#Replace
	 * 
	 */
	public function wrapElements()
	{
		$elems = $this->getElements();
		foreach ($elems as $elem)
		{	
			$divName = 'div_'.$elem->getId();
			//add divTag as a HtmlTag but with a different name because we don't want to overwrite dd decorator. 
			//@see documentation ZendForm/Decorators/Using Multiple Decorators of the Same Type
			$elem->addDecorator(array('divTag' => 'HtmlTag'), array('tag'=>'div', 'id'=>$divName)); 
			
			//debug
			//Zend_Debug::dump($elem->getDecorators(),$elem->getName());
		}
	}
}
?>