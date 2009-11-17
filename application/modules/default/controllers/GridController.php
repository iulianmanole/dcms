<?php

/* Load Zend_Controller_Action class */
require_once 'Zend/Controller/Action.php';

/**
 *  Simple Grid Display Controller
 * 
 */


class GridController extends Zend_Controller_Action 
{
	function preDispatch()
	{
		
	}
	function indexAction()
	{
		echo " GridController/IndexAction";
	}
	
	/**
	 * 	Will prepare and display a list of elements based on the 
	 * retrieved params
	 *  
	 * @param Request->UserParam data - object instance of Inno_DataGrid
	 * 
	 *
	 */

	
	function showAction() 
	{	
		$data = $this->getRequest()->getUserParam('data');
		$this->view->data = $data; 
	}
	
}

?>