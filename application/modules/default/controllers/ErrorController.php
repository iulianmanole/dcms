<?php

/* Load Zend_Controller_Action class */
require_once 'Zend/Controller/Action.php';

class ErrorController extends Zend_Controller_Action 
{
	function indexAction()
	{
		echo " ErrorController/IndexAction";
	}
	
	function errorAction ()
	{ 
		
		
		$this->view->errorDetails = $this->_response;	
	}
}

?>