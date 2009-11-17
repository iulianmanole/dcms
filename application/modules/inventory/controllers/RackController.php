<?php

/* Load Zend_Controller_Action class */
require_once 'Inno/Controller/Action.php';

class Inventory_RackController extends Inno_Controller_Action
{
	/**
	 * @var Model_Rack used by this controller.
	 */
	protected $_model ;

	/**
	 * get Model_Rack associated with this controller.
	 * @return Model_Rack
	 */
	public function getModel()
	{
		return $this->_model;
	}

	public function init ()
	{
		/** Init ajaxContext Helper */
		$ajaxContext = $this->_helper->getHelper('AjaxContext');

		$ajaxContext->addActionContext('update-rack-product-list', array ('html'))
					->initContext();
		 
		parent::init();	
	}
	
	/**
	 * predispatch operation
	 * @see application/library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	public function preDispatch()
	{
		//Instantiate an empty model.
		$this->_model 		= new Model_Rack(array(), array( 'gateway' => new Model_RackGateway()));
		$this->view->model 	= $this->_model;
	}
	
	/**
	 * Default index Action.
	 * 
	 * @return none
	 */
	public function indexAction()
	{
		$this->getRedirector()->gotoSimple('list');
	} 
	
	/**
	 * Add action will display add form via the view.
	 * @return none
	 */
	public function addAction()
	{
		//Zend_Debug::dump($this->_helper,'RackController->helpers');
	}
	
	/**
	 * delete selected racks
	 * @return none
	 */
	public function deleteAction()
	{
		echo "In Inventory/Rack/Delete";
		
		$modelGw 	 = new Model_RackGateway();
		$toDeleteIds = $this->getDataGridHelper()->getSavedRefPks();  
		 
		$deletedItems = $modelGw->delete($toDeleteIds);
		if ( $deletedItems == false) {
			$this->getFlashMessenger()->addMessage('The selected objects were not deleted.');
		}
		else {
			$this->getFlashMessenger()->addMessage('The selected('.$deletedItems.')'.' objects were deleted.');
		}
		
	    if (null !== $this->getDataGridHelper()->getSavedReturnAction()) {
	     	$returnAction =  $this->getDataGridHelper()->getSavedReturnAction();
	    	$this->getRedirector()->gotoUrl($returnAction);
	    }	
	    else {
	    	$this->getRedirector()->gotoSimple('list');    	
	    }
	}
	
	/**
	 * list action
	 * @return none
	 */
	public function listAction()
	{
		$modelGw 	= new Model_RackGateway();
		$location 	= $this->_getParam('location');
		$request 	= $this->getRequest();
		
		$criteria = array ();
		if ($location) {
			$criteria[" location_id"] = $location;
		}

		//Instantiate the paginator
		$selectRacks 	= $modelGw->selectRacks($criteria);
		$dataGrid 	= new Inno_DataGrid('Racks', $selectRacks);
		
		$dataGrid->setSubmitAction('Delete', 'delete', 'rack', 'inventory')
				->setSubmitAction('Add', 'add', 'rack', 'inventory');

		$dataGrid->getDataGridView()->setTitle('Racks');

		$dataGrid->getDataGridView()
				->setDisplayAttribute('Name', array ('name'))
				->setDisplayAttribute('Product', array ('manufacturer', 'hw_product'))
		//		->setDisplayAttribute('Height (RU)', array ('height_eia_units'))
		//		->setDisplayAttribute('Max Weight', array ('max_weight'))
				->setDisplayAttribute('Location', array('location'));
		
		$dataGrid->setOrder('name','ASC');		
		$dataGrid->getDataGridView()
				->setDetailLink('Name', 'view', 'rack', 'inventory', array('id'=>'id'));

		//$dataGrid->run($request);
		$dataGridHelper = $this->getDataGridHelper();
		$dataGridHelper->run($dataGrid);
		
		//Assign variables to View
		$this->view->assign( array(
						'dataGrid' => $dataGrid,
		));
	}
	
	/**
	 * view details for a rack.
	 * @param id(via _GET) - rack identifier. 
	 * 
	 * @return none
	 */
	public function viewAction()
	{
		//get Id param from request
		if (!$id = $this->_getParam('id')) {
			$this->getFlashMessenger()->addMessage('Rack ID was not specified');
			$this->getRedirector()->gotoSimple('list');
		}

		//fetch requested ProductID from DB
		if (!$this->_model->fetch($id)) {
			$this->render('not-found',null,true);
		} else {
			$this->view->model = $this->_model;
		}
	}
	
	/**
	 * process the add Form
	 * 
	 * @return none
	 */
	public function processAddAction()
	{
		//Zend_Debug::dump($this->getRequest(),'request');
		$request = $this->getRequest();
		if (!$request->isPost()) {
			return $this->getRedirector()->gotoSimple('list');
		}
		 
		$rack = $this->getModel();

		if (!$id = $rack->save($request->getPost())) {
			// failed
			return $this->render('add');
		}
		 
		//Use ExtendedPaginator Helper to retrieve data from the paginator.
		$extPaginatorHelper = $this->getDataGridHelper();

		//decide where to redirect.
		if (null !== $extPaginatorHelper->getSavedReturnAction()) {
			$returnAction = $extPaginatorHelper->getSavedReturnAction();
			$this->getRedirector()->gotoUrl($returnAction);
		}
		else {
			//This function was not executed by a paginator.
			$this->getRedirector()->gotoSimple('view','rack','inventory',array('id' => $id));
		}
	}
	
	/**
	 * must be called by an ajax XHR. 
	 * Will return the list of  rack products for a certain manufacturer 
	 * @return hw_product_id element 
	 */
	public function updateRackProductListAction()
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
}

?>