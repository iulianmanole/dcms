<?php

/* Load Zend_Controller_Action class */
require_once 'Zend/Controller/Action.php';

class Inventory_IndexController extends Inno_Controller_Action 
{
	
	function preDispatch()
	{
		
	}
	function indexAction()
	{
		echo " Inventory --> IndexController/IndexAction";
	}
	
	function TestAction() {

		echo "Inventory -->  IndexController/testAction";
		$ss = print_r($this->getRequest(), true );
		$this->view->ss = $ss; 
	}
	
	public function NotFoundAction() {
		echo "the requested operation was not performed.";
	}
}

?>