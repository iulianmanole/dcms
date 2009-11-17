<?php 

/** Load Zend_Controller_Action class */
require_once 'Zend/Controller/Action.php';

class InfoController extends Inno_Controller_Action 
{
	const ACTION_END_OK 	= 'Action Ended Successfully'; 
	const ACTION_END_NOK 	= 'Error ,please try again';
	
	
	public function phpinfoAction(){
		
		echo 'InfoController/phpinfoAction';
	}
	
	/**
	 * Pop the last message from flashMessenger.
	 * It is used to offer information about last executed action
	 * Because a executed action will redirect to this action will not be possible to duplicate executed action result.
	 */ 
	public function infoAction()
	{
		$this->view->msg = $this->_getParam('msg'); 	
	}
	
}
 ?>