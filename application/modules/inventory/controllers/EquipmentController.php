<?php
/* Load Zend_Controller_Action class */
require_once 'Inno/Controller/Action.php';

class Inventory_EquipmentController extends Inno_Controller_Action
{
	/**
	 * @var Model_Equipment used by this controller.
	 */
	protected $_model ;

	/**
	 * get Model_Equipment associated with this controller.
	 * @return Model_Equipment
	 */
	public function getModel()
	{
		return $this->_model;
	}
	
	public function init ()
	{
		/** Init ajaxContext Helper */
		$ajaxContext = $this->_helper->getHelper('AjaxContext');

		$ajaxContext->addActionContext('update-product-list', array ('html'))
					->addActionContext('update-rack', array ('html'))
					->addActionContext('change-rack', array ('html'))
					->initContext();
		 
		parent::init();	
	}
	
	public function preDispatch()
	{
		//Instantiate an empty model.
		$this->_model 		= new Model_Equipment();
		$this->view->model 	= $this->_model;
	}
	
	
	public function indexAction()
	{
		$this->getRedirector()->gotoSimple('list');
	}
	
	public function addAction()
	{
		
	}
	
	public function processAddAction()
	{
		$request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->getRedirector()->gotoSimple('list');
        }
        //save equipment
        $model = $this->getModel();

		if (!$id = $model->save($request->getPost())) {
            // failed
            return $this->render('add');
        }
        
        //log the addition of the new equipment.
        Zend_Registry::get('logger')->info(__METHOD__. ' Equipment(id='.$id.') was added succesfully.');
        
        //Redirect to view the newly added hwProduct.
        $this->getRedirector()->gotoSimple('list','equipment','inventory',array('id' => $id));
	}
	
	public function listAction()
	{
		$modelGw 	= new Model_EquipmentGateway();
		$request 	= $this->getRequest();

		//Instantiate the paginator
		$select = $modelGw->selectEquipments();
		$dataGrid 	= new Inno_DataGrid('Equipments', $select);
		
		//set submit actions for paginator.
		$dataGrid->setSubmitAction('Add', 'add', 'equipment', 'inventory')
			->setSubmitAction('Delete', 'delete', 'equipment', 'inventory')
			->setSubmitAction('ChangeLocation','change-location','equipment', 'inventory')
			->setSubmitAction('SetGroup', 'set-group','equipment','inventory')
			->setSubmitAction('SetSystem', 'set-system','equipment','inventory');
				
		//set title and display attributes for the paginator.
		$dataGrid->getDataGridView()->setTitle('Equipments');
		$dataGrid->getDataGridView()
				->setDisplayAttribute('Name', array ('name'))
				->setDisplayAttribute('Hardware Product', array ('manufacturer', 'hw_product'))
			//	->setDisplayAttribute('Location ID', array('hw_location_id'))
				->setDisplayAttribute('Hardware Type', array('hw_type'))
				->setDisplayAttribute('Location', array('hw_location'))
				->setDisplayAttribute('Rack', array('rack'))
				;
		
		//set detailActions for paginator.		
		$dataGrid->getDataGridView()
				->setDetailLink('Name', 'view', 'equipment', 'inventory', array('id'=>'id'))
				->setDetailLink('Location','view','location','inventory', array('id' => 'hw_location_id'))
				->setDetailLink('Hardware Type','view','hw-type','inventory',array());
				;

		//Run the paginator.
		$this->getDataGridHelper()->run($dataGrid);
				
		//Assign variables to View
		$this->view->assign( array(
						'dataGrid' => $dataGrid,
		));
		
	}
	
	/**
	 * deletes selected equipments 
	 * Requires the list of equipments through DataGrid. 
	 *  
	 * @return none
	 */
	public function deleteAction()
	{	
		$modelGw 	 = $this->getModel()->getGateway();
		$toDeleteIds = $this->getDataGridHelper()->getSavedRefPks();
		
		$deletedItems = $modelGw->delete($toDeleteIds);
		if ( $deletedItems == false) {
			$this->getFlashMessenger()->addMessage('The selected objects were not deleted.');
			Zend_Registry::get('logger')->warn(__Method__.' Selected Equipments id("'.
											implode(',',$toDeleteIds).'")');
		}
		else {
			$this->getFlashMessenger()->addMessage('The selected('.$deletedItems.')'.' objects were deleted.');
			Zend_Registry::get('logger')->info(__Method__.' Deleted '.$deletedItems.' equipments with ids("'.
											implode(',',$toDeleteIds).'")');
		}
		
		$this->getRedirector()->gotoSimple('list');
	}
	
	/**
	 * view details for a Equipment.
	 * @param id(via _GET) - rack identifier. 
	 * 
	 * @return none
	 */
	public function viewAction()
	{
		//get Id param from request
		if (!$id = $this->_getParam('id')) {
			$this->getFlashMessenger()->addMessage('Equipment ID was not specified');
			$this->getRedirector()->gotoSimple('list');
		}

		//fetch requested ProductID from DB
		if (!$this->getModel()->fetch($id)) {
			$this->render('not-found',null,true);
		} else {
			$this->view->model = $this->_model;
		}
	}
	
	/**
	 * Change the location of the selected equipments.
	 * @return none
	 */
	public function changeLocationAction ()
	{
		//save refPks to session in order to preserve them across requests.
		if (!count($this->getDataGridHelper()->getSavedRefPks())) {
			$this->getFlashMessenger()->addMessage('No equipments were selected.');	
			$this->getRedirector()->gotoUrl($this->getDataGridHelper()->getSavedReturnAction());
		}
		$namespace 		= new Zend_Session_Namespace('equipment-change-location');
		$namespace->ids = $this->getDataGridHelper()->getSavedRefPks();
	}
	
	
	public function processChangeLocationAction()
	{
		$request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->getRedirector()->gotoSimple('list');
        }
       	Zend_Debug::dump($request->getPost());
        //validate form
        $inputFilter = $this->getModel()->getForm('changeLocation');
        if (!$inputFilter->isValid($this->getRequest()->getPost())) { 
			return $this->render('change-location');
		}
		
        //set location for selected equipments
        $locationId = $inputFilter->getValue('hw_location_id');
        $rackId 	= $inputFilter->getValue('rack_id');
        $namespace  = new Zend_Session_Namespace('equipment-change-location');
        if (isset($namespace->ids)) {
        	$ids = $namespace->ids;
        	unset($namespace->ids);

        	$modelGw = $this->getModel()->getGateway();
			if ($result = $modelGw->setLocation($ids, $locationId, $rackId)) {
    	        $this->getFlashMessenger()->addMessage($result.' equipments were moved to the new location');
        	}
        	else {
        		$this->getFlashMessenger()->addMessage('No equipments were moved.');
        	}
        }
        
        //Redirect to index
       	$this->getRedirector()->gotoSimple('list','equipment','inventory');
	}

	public function setGroupAction()
	{
		//save refPks to session in order to preserve them across requests.
		if (!count($this->getDataGridHelper()->getSavedRefPks())) {
			$this->getFlashMessenger()->addMessage('No equipments were selected.');	
			$this->getRedirector()->gotoUrl($this->getDataGridHelper()->getSavedReturnAction());
		}
		
		$namespace 		= new Zend_Session_Namespace('equipment-set-groups');
		$namespace->ids = $this->getDataGridHelper()->getSavedRefPks();
		$namespace->returnAction = $this->getDataGridHelper()->getSavedReturnAction();
	}
	
	public function processSetGroupAction()
	{
		$request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->getRedirector()->gotoSimple('list');
        }
       	Zend_Debug::dump($request->getPost());
        //validate form
        $inputFilter = $this->getModel()->getForm('setGroup');
        if (!$inputFilter->isValid($this->getRequest()->getPost())) { 
			return $this->render('set-group');
		}
		
		$selectedGroups = $inputFilter->getValue('groups');
		
		$namespace 		= new Zend_Session_Namespace('equipment-set-groups');
		$modelGw 		= $this->getModel()->getGateway();
		$ids = $namespace->ids;
		$returnAction = $namespace->returnAction;
		$namespace->unsetAll();
		
		if ($modelGw->setGroups($ids,$selectedGroups)) {
			if (count($ids) > 1 ) {
				$this->getFlashMessenger()->addMessage('The equipments were moved to the selected groups');	
			}else {
				$this->getFlashMessenger()->addMessage('The equipment was moved to the selected group');	
			}	
		}else {
			$this->getFlashMessenger()->addMessage('Operation Failed. Please see the log for details');	
		}
		
		$this->getRedirector()->gotoUrl($returnAction);
	} 
	
	/**
	 * Set equipments membership to Systems organizers 
	 * All previous memberships are overwriten. 
	 * @return none
	 */
	public function setSystemAction() 
	{
		//save refPks to session in order to preserve them across requests.
		if (!count($this->getDataGridHelper()->getSavedRefPks())) {
			$this->getFlashMessenger()->addMessage('No equipments were selected.');	
			$this->getRedirector()->gotoUrl($this->getDataGridHelper()->getSavedReturnAction());
		}
		
		$namespace 		= new Zend_Session_Namespace('equipment-set-systems');
		$namespace->ids = $this->getDataGridHelper()->getSavedRefPks();
		$namespace->returnAction = $this->getDataGridHelper()->getSavedReturnAction();
	}
	
	
	public function processSetSystemAction()
	{
		$request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->getRedirector()->gotoSimple('list');
        }
       	Zend_Debug::dump($request->getPost());
        //validate form
        $inputFilter = $this->getModel()->getForm('setSystem');
        if (!$inputFilter->isValid($this->getRequest()->getPost())) { 
			return $this->render('set-system');
		}
		
		$selectedSystems = $inputFilter->getValue('systems');
		
		$namespace 		= new Zend_Session_Namespace('equipment-set-systems');
		$modelGw 		= $this->getModel()->getGateway();
		$ids = $namespace->ids;
		$returnAction = $namespace->returnAction;
		$namespace->unsetAll();
		
		if ($modelGw->setSystems($ids, $selectedSystems)) {
			if (count($ids) > 1) {
				$this->getFlashMessenger()->addMessage('The equipments were moved to the selected groups');	
			}else {
				$this->getFlashMessenger()->addMessage('The equipment was moved to the selected group');	
			}	
		}else {
			$this->getFlashMessenger()->addMessage('Operation Failed. Please see the log for details');	
		}
		
		$this->getRedirector()->gotoUrl($returnAction);
	}
	
	
	/**  
	 * Action that updates the product list based on the Manufacturer
	 * @return none
	 */
	public function updateProductListAction()
	{	
		//Remove form attributes that were not set.
		$formRawData = $this->getRequest()->getPost();
		$formData = array();
		foreach ($formRawData as $key => $value) {
			if ($value != '') {
				$formData[$key] = $value;
			}
		}
		$form = $this->getModel()->getForm('add');
		
		$this->view->element = $form->updateHwProductIdElement($formData);
	}
	/**
	 * We'll add the rack list for the selected location or
	 * We'll allow the user to specify the name of the rack, for standalone servers.
	 * 
	 * @return none
	 */
	public function updateRackAction()
	{
		$formRawData = $this->getRequest()->getPost();
		$formData = array();
		foreach ($formRawData as $key => $value) {
			if ($value != '') {
				$formData[$key] = $value;
			}
		}
		$form = $this->getModel()->getForm('add');
		
		$this->view->element = $form->updateRackElement($formData);
	}
	
	public function changeRackAction()
	{
		$formRawData = $this->getRequest()->getPost();
		$formData = array();
		foreach ($formRawData as $key => $value) {
			if ($value != '') {
				$formData[$key] = $value;
			}
		}
		$form = $this->getModel()->getForm('changeLocation');
		
		$this->view->element = $form->updateRackElement($formData);
	}
	
	
}
?>