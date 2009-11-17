<?php
/**
 * Class used to implement the functionality for the Extended Paginator
 *
 *
 * @author Iulian
 *
 */

class Inno_Controller_Action_Helper_DataGrid extends Zend_Controller_Action_Helper_Abstract
{
	
	/**
	 * Session Namespace.
	 * 
	 * @var string 
	 */
	protected $_namespace = 'DatagridHelper';
	
	/**
	 * session object. 
	 * 
	 * @var Zend_Session_Namespace 
	 */
	protected $_session = null;  
	
	protected $_executeAction =  null; 
	
	protected $_returnAction = null; 
	
	protected $_refPks = null; 
	
	/**
	 * Initialize the extended paginator in controller environment
	 * if the paginator wants to execute the action it will save in session refPks and returnAction
	 *
	 * @param Inno_DataGrid $dataGrid
	 * @return none
	 */
	public function run(Inno_DataGrid $dataGrid)
	{
		$request = $this->getRequest();
		//Zend_Debug::dump($request, 'Request|ExtendedPaginator');
		
		$dataGrid->init($request);

		if ($request->isPost()) {
			if ($dataGrid->getName() == $request->getPost('paginatorName')) {

				//if request is post .. paginator should execute the submited action
				//executing the action can be performed by telling Front Controller to dispatch another action.
				$requestedAction = $dataGrid->getActionToExecute($request);
				
				//save data (params) to session in order to be retrieved by the requested action
				$this->saveData($dataGrid);
				
				//redirect to the action to be executed
				$redirector = $this->getActionController()->getHelper('Redirector');
				$redirector->gotoSimple($requestedAction['action'],
										$requestedAction['controller'],
										$requestedAction['module'], 
										$requestedAction['params']
										);											
			}
		}
	}
	
	/**
	 * Save refPks and returnAction in session to be used by the requested action
	 * 
	 * @param $dataGrid
	 * @return none
	 */
	public function saveData(Inno_DataGrid $dataGrid)
	{
		$request = $this->getRequest();
		$session = $this->getSession();
		
		$session->data = array (
					'refPks' 		=> $dataGrid->getSelectedRefPks($request),
					'executeAction' => $dataGrid->getActionToExecute($request),
					'returnAction' 	=> $this->getActionController()->getHelper('Url')->url(),
				);		
	}
	
	/**
	 * 
	 * retrieves bulk data saved by the previous controller and 
	 *  cleans the session 
	 * 
	 * @param $namespace 
	 * 				- tries to retrieve data from a custom namespace.
	 * 				- by default it will retrieve data from default namespace 
	 * 
	 * @access protected | for public access use getSaved* methods.
	 *  
	 * @return associative array (refPks, executeAction, returnAction) 
	 * 
	 */
	protected function getSavedData($namespace = null)
	{
		
		$request = $this->getFrontController()->getRequest();
		
		if ($namespace !==null) {
			$this->setNamespace($namespace);
		}
		$session = $this->getSession(); 
		$data = $session->data;
		$this->_executeAction = $data['executeAction'];
		$this->_returnAction = $data['returnAction'];
		$this->_refPks = $data['refPks'];
		
		// validate that the current Action is the one from the session; 
		// this will not work if the executed action starts a chain of actions.   
		//if ($this->isAppropriateRequest($executeAction)) {
		$session->unsetAll(); 
		//trigger_error(Zend_Debug::dump($data));
		return $data;	
		//}
		//return null;
		
	}
	
	/**
	 * Retrieve saved refPks from the saved session data.
	 * @return array
	 */
	public function getSavedRefPks($namespace = null)
	{
		if ($this->_refPks === null) {
			$this->getSavedData($namespace);
			
		}
		
		return $this->_refPks; 
	}
	
	/**
	 * Retrieve the execute Action from the saved session data.
	 * @return associative array ExecuteAction 
	 */
	public function getSavedExecuteAction($namespace = null)
	{
		if ($this->_executeAction === null){
			$this->getSavedData($namespace);
		}
		
		return $this->_executeAction;
	}
	
	/**
	 * Retrieve the return action (URL) from the saved session data.
	 * @return string return URL
	 */
	public function getSavedReturnAction($namespace = null)
	{
		if ($this->_returnAction === null){
			$this->getSavedData($namespace);			
		}
		
		return $this->_returnAction;
	}
	
	/**
     * setNamespace() - change the namespace 
     *
     * @param  string $namespace
     * @return Zend_Controller_Action_Helper_ExtendedPaginator Provides a fluent interface
     */
    protected function setNamespace($namespace = null)
    {
    	if (namespace !== null) {
        	$this->_namespace = $namespace;
    	}
    	
        return $this;
    }
    
    /**
     * get the namespace 
     * 
     * @return string
     */
    protected function getNamespace ()
    {
    	return $this->_namespace;
    }
    
    protected function getSession()
    {
    	if ($this->_session === null) {
    		$this->_session = new Zend_Session_Namespace($this->getNamespace());
    	}
    	
    	return $this->_session;
    }

    /**
     * Will compare executeAction with request data from frontController Request object
     * If they match will return true. 
     * 
     * @param $executeAction -array(controller,action,module)
     * @return boolean
     */
    protected function isAppropriateRequest($executeAction)
    { 
    	Zend_Debug::dump($executeAction, 'executeAction1');
    	
    	
    	$request = $this->getFrontController()->getRequest(); 
    	$reqParams = array(
    					'module' => $request->getModuleName(),
    					'action' => $request->getActionName(),
    					'controller' => $request->getControllerName(),
    		);
    	$executeParams = array(
    					'module' => $executeAction['module'],
    					'action' => $executeAction['action'],
    					'controller' => $executeAction['controller']
    		);	
    		

    	if (count(array_intersect_assoc($reqParams, $executeParams)) == count($executeParams)) {
    		return true; 
    	}	
    	else {
    		return false;
    	}
    }
    
}

	?>