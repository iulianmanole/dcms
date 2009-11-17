<?php
require_once 'Zend/Controller/Action.php';

abstract class Inno_Controller_Action extends Zend_Controller_Action
{
	protected $_flashMessenger = null;
	
	protected $_redirector = null;
	
	protected $_dataGridHelper = null; 
	
	protected $_xhr = null; 
	
	protected $_organizerHelper = null; 
	
	
	public function init()
	{
		//We must be sure that parent->init function sets the basic environment.
		parent::init();
		
		//Enable Dojo Helpers Location
		$this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
		
		//Add Controller Helpers path
		Zend_Controller_Action_HelperBroker::addPath('Inno/Controller/Action/Helper', 'Inno_Controller_Action_Helper');

		/**
		 * Get all messages from the previous action(s).
		 * These messages will be displayed in layoutscript in info div.
		 * 
		 */
		$this->view->messages = $this->getFlashMessenger()->getMessages(); 
		
	}
	
	/**
 	 * @return Zend_Controller_Action_Helper_Redirector
 	 */		
	public function getRedirector()
	{
		if ($this->_redirector === null) {
			//Enable Redirector Action Helper
			$this->_redirector = $this->_helper->getHelper('Redirector');
		}
		return $this->_redirector;
	}
	/**
 	 * @return Zend_Controller_Action_Helper_FlashMessenger
 	 */
	public function getFlashMessenger()
	{
		if ($this->_flashMessenger === null) {
			//Enable FlashMessenger Action Helper.
			$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		}
		return $this->_flashMessenger; 
	}

	/**
	 * 
	 * @return Inno_Controller_Action_Helper_DataGrid
	 */
	public function getDataGridHelper()
	{
		if ($this->_dataGridHelper === null) {
			//Enable dataGrid Action Helper.
			$this->_dataGridHelper = $this->getHelper('DataGrid');
		}
		
		return $this->_dataGridHelper; 
	}
		
	/**
	 * Instantiate organizer Helper
	 * 
	 * @return Inno_Controller_Action_Helper_Organizer
	 */
	public function getOrganizerHelper()
	{
		if ($this->_organizerHelper === null) {
			$this->_organizerHelper = $this->getHelper('Organizer');
		}
		return $this->_organizerHelper;
	}
	
	/**
	 * retrieve the current action as a string 	"/module/controller/action"
	 *					 					or 	"/controller/action"
	 * 										or 	"/action",
	 * based on $request object
	 * @param $request Object $request
	 *
	 * @return string
	 */
	public function getActionAsString(Zend_Controller_Request_Http $request)
	{
		$module = $request->getModuleName() !== null ? $request->getModuleName().'/' : '';
		$ctrl 	= $request->getControllerName() !== null ? $request->getControllerName().'/' : '';
		$action = $request->getActionName() !== null ? $request->getActionName() : '';

		$str = '/'.$module.$ctrl.$action;
		//Zend_Debug::dump($str);
		return $str;
	}
}
?>