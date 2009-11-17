<?php

/**
 * Provides functionality to manipulate and display Inno_Organizers
 * It replaces Default/OrganizerController, which is deprecated.
 *
 * @author Iulian
 *
 */
class Inno_Controller_Action_Helper_Organizer extends Zend_Controller_Action_Helper_Abstract
{

	protected $_namespace;

	protected $_namespaceName = 'OrganizerHelper';

	/**
	 * 
	 * @var Organizer Class
	 */
	protected $_organizerClass;

	/**
	 * the id for the current organizer
	 * @var integer
	 */
	protected $_activeOrganizerID;

	/**
	 *  Instance of the current Organizer.
	 *  At any point, there will be only one organizerModel Object.
	 *
	 *  @var Inno_Organizer
	 */
	protected $_activeOrganizer;
	

	/**
	 * Get the Active Organizer for this Helper
	 *
	 * @return Inno_Organizer
	 */
	public function getActiveOrganizer()
	{
		if ($this->_activeOrganizer === null) {
			$this->_activeOrganizer = $this->_createOrganizer(array('id' => $this->getActiveOrganizerId()));
		}
		//Zend_Debug::dump($this->_activeOrganizer->id);
		return $this->_activeOrganizer;
	}

	/**
	 * Retrieve Active organizer ID from the current request
	 * Relays on data provided through _GET and must be sanitized.
	 * If the current organizer is not set it will return the root organizer (Id 1, by default).
	 *
	 * @return Organizer ID
	 */
	public function getActiveOrganizerId()
	{
		//Zend_Debug::dump($this->getActionController()->getRequest()->getParam('id','1'),'PARAM-ID');
		return $this->getActionController()->getRequest()->getParam('id','1');
	}

	public function getNamespace()
	{
		if ($this->_namespace === null) {
			$this->_namespace = new Zend_Session_Namespace($this->_namespaceName);
		}

		return $this->_namespace;
	}
	
	/**
	 * Return the organizer class if set 
	 * @throws error 
	 * @return string 
	 * 
	 */
	public function getOrganizerClass()
	{
		if ($this->_organizerClass!== null) {
			return $this->_organizerClass;
		}
		else {
			throw new Zend_Controller_Action_Exception("[Inno_Controller_Action_Helper_Organizer]"
											.'Unable to getOrganizerClass because is not set.'
											.'It must be first set via setOrganizerClass method');
		}
	}
	
	/**
	 * Set Organizer Class. This class must exist. 
	 * @param $class string, the full class name 
	 * @return $this for fluent interface
	 */
	public function setOrganizerClass($class)
	{
		if (class_exists($class)) {
			$this->_organizerClass = $class;
		}
		return $this;
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

	/**
	 * Proxy method to instantiate an Organizer
	 * It is used to return an Inno_Organizer object
	 * @param $data array; allowable keys are: "id", "name", etc. If "id" is set the rest will be ignored. 
	 * @return Inno_Organizer 
	 */
	protected function _createOrganizer(array $data)
	{
		$ogzClass = $this->getOrganizerClass();
		if (class_exists($ogzClass)) {
			$ogz = new $ogzClass($data);
			if(!($ogz instanceof Inno_Organizer)) {
				throw new Inno_Organizer_Exception ('Supplied Organizer Class"'
								.$ogzClass.'" is not an instance of Inno_Organizer.');
			}
		}
		else {
			//Supplied OrganizerClass doesn't exist throw error.
			throw new Inno_Organizer_Exception('Supplied OrganizerClass "'
								.$ogzClass.'" doesn\'t exist. [tip] Did You set the organizer class?');
		}

		return $ogz;
	}

	/**
	 * This method incapsulates all the logic for adding a new Organizer.
	 * In the ActionController just use this function: $helper->add(...);
	 * 
	 * @param $organizerRefUrl - reference to the action executed before the current one. 
	 * @return newly added organizerId
	 */
	public function add($organizerRefUrl)
	{
		//temporary variables set to check functionality 
		$organizerClass = $this->getOrganizerClass();
		$organizerId = $this->getActiveOrganizerId();
		
		//Init sesssion Namespace
		$namespace = $this->getNamespace();
		$namespace->setExpirationSeconds(60);

		//If this a forwarded action
		if (!$this->getRequest()->isPost()) {
			//save in session the following attributes.
			$namespace->organizerDetails = array (
											'id'	=> $this->getActiveOrganizerId(),
											'refUrl'=> $organizerRefUrl 
			);
			//render form addOrganizer
			$form = new forms_Organizer();
			$form->setAction($this->getActionAsString($this->getRequest()));
			$this->getActionController()->view->form = $form;

			$this->getActionController()->render('add');
		}
		else {
			//retrieve session variables
			if (isset($namespace->organizerDetails)){
				$parentDetails = $namespace->organizerDetails;
			}
			else {
				echo "namespace->organizerDetails is not set.";
				return;
			}

			//form validation & data retrieval
			$form = new forms_Organizer();
			if ($form->isValid($_POST)) {
				$childName = $form->getValue('organizer');

				//init child organizer and parent Organizer.
				$childOrganizer		= $this->_createOrganizer(array('name' => $childName));
				$parentOrganizer 	= $this->_createOrganizer(array('id' => $parentDetails['id']));

				//create parent->child relation.
				try {
					$opResult = $parentOrganizer->manage_addChild($childOrganizer);
					if ($opResult) {
						$this->getActionController()
							->getFlashMessenger()
							->addMessage('The organizer '. $childName.' was added succesfully.');
					}
					else {
						$this->getActionController()
							->getFlashMessenger()
							->addMessage('The organizer '. $childName.' was not added.');	
					}
					
				}
				catch (Exception $e) {
					$this->getActionController()
						->getFlashMessenger()
						->addMessage($e->getMessage());
				}				
			}
			else {
				//form is not valid
				$this->getActionController()
					->getFlashMessenger()
					->addMessage('Unable to add organizer.Provided data is not valid.');
			}
			
			//clear namespace
			$namespace->unsetAll();

			//redirect to parent refUrl.
			$this->getActionController()->getRedirector()->gotoUrl($parentDetails['refUrl']);
		}
	}

	/*
	 * Delete organizers and subchilds 
	 *
	 * @param array $ogzIds - the list of organizer ids that will be deleted. 
	 * @param string $ogzRefUrl - the reference URL.
	 *
	 *
	 */
	public function delete($ogzIds, $ogzRefUrl)
	{
		if (count($ogzIds)) {
			foreach ($ogzIds as $id) {
				$organizer = $this->_createOrganizer(array('id' => $id));
				$result = $organizer->manage_deleteOrganizer();
				if ($result !== true) {
					$this->getActionController()->getFlashMessenger()->addMessage('Organizer <<'.$organizer->getName().'>> was not deleted');
				}
				else {
					$this->getActionController()->getFlashMessenger()->addMessage('Organizer <<'.$organizer->getName().'>> was deleted');
				}
			}
		}
		else {
			// There was no organizer to delete.
			$this->getActionController()->getFlashMessenger()->addMessage('There was no organizer to delete.');
		}
		$this->getActionController()->getRedirector()->gotoUrl($ogzRefUrl);
	}

	/**
	 * --- not implemented  ---
	 */
	public function move()
	{
		echo "not yet implemented";
	}
}

?>