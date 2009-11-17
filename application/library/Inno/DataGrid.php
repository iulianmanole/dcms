<?php
/**
 * Standard object to extract and manipulate data from a DataSource. 
 * 
 * 
 * 
 */
class Inno_DataGrid
{
	/**
	 * Paginator default Page
	 */
	const DEFAULT_PAGE = 1;

	/**
	 * Paginator default items count per page
	 */
	const COUNT_PER_PAGE = 10;

	/**
	 *  Action to excute 
	 */
	protected $_actionToExecute = null; 
	
	/**
	 * Unique Identifier for this DataGrid.
	 */
	protected $_name = '';
	
	/**
	 * Instance of Zend_Paginator to manipulate data. 
	 * @var Zend_Paginator
	 */
	protected $_paginator;

	/**
	 * Db Select used to retrieve data from model.
	 * ??should it be used ?? @todo  
	 */
	protected $_select;

	/**
	 * Zend_Form
	 * @todo specify an interface for this form
	 */
	protected $_form ;

	/**
	 * Form that will be used to render ExtendedPaginator.
	 */
	protected $_formName = 'Inno_DataGrid_Form_PaginatorMultiCheckBox';

	/**
	 * submited actions that will be executed for selected Paginator items.
	 */
	protected $_submitActions = array();

	/**
	 * DisplayData will tell if paginator contains items that must be render or not.
	 * If is false, 
	 * 		- render function will not render the view template and will only
	 * 				display a message
	 * 		- DataGridView will not be initialized 
	 *
	 */
	protected $_displayData = true;

	/**
	 * if disablePagination is true, all the items will be parsed and displayed.
	 */
	protected $_paginationDisabled = false;

	/**
	 * @desc Define the way the paginator will be rendered. 
	 * 
	 * @var Inno_Paginator_Extended_View 
	 */
	protected $_dataGridView;
	
	/**
	 * @desc Determine if pagination is disabled
	 * @return 	true  --> means that all items will be dispayed (page and count values are ignored)
	 * 			false --> means that items will be dispalyed paginated.
	 */
	public function isPaginationDisabled()
	{
		return $this->_paginationDisabled;
	}
	
	/**
	 * 
	 * @return boolean TRUE/FALSE
	 */
	public function isDataToDisplay()
	{
		return $this->_displayData;
	}
	
	/**
	 * @return Zend_Paginator
	 */
	public function getPaginator()
	{
		return $this->_paginator;
	}

	
	/** 
	 * get Paginator View Instance. 
	 * 
	 * @return Inno_DataGrid_View
	 */
	public function getDataGridView() 
	{
		if (null === $this->_dataGridView) {
			$this->_dataGridView = new Inno_DataGrid_View($this);
		}
		return $this->_dataGridView;
	}
	
	/**
	 * 
	 * @return Inno_DataGrid_View 
	 */
	public function setDataGridView(Inno_DataGrid_View $dataGridView)
	{
		$this->_dataGridView = $dataGridView; 
		
		return $this->_dataGridView;
	} 
	
	
	
	/**
	 * return form object
	 * @return forms_PaginatorMultiCheckBox
	 */
	public function getForm()
	{
		return $this->_form;
	}

	/**
	 * set form details
	 * @return nothing
	 */
	public function setForm()
	{
		$formName = $this->_formName;
		$this->_form = new $formName();

		//the form will have the same name as paginator.
		$this->getForm()->setName($this->getName());
		$this->getForm()->createHiddenPaginatorName($this->getName());
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setName($name)
	{
		if ($name !== null) {
			$this->_name = $name;
		}
		else {
			throw new Inno_Datagrid_Exception('DataGrid name can\'t be null. Please specify pagiantor\'s name.');
		}
	}

	public function setOptions (array $options)
	{
		if (isset($options['paginationDisabled'])) {
			$this->_paginationDisabled = $options['paginationDisabled'];
		}
	}

	/**
	 * $select 	- is used to retrieve data. Must have a refPk column in order to add logic.
	 * @todo add possibility to use all Paginator's adapters.
	 */
	public function __construct($name, Zend_Db_Select $select = null, array $options = array())
	{
		//Set paginator's Name
		$this->setName($name);

		if ( $select !== null){
			$this->_select = $select;
			$this->_paginator = new Zend_Paginator(new Inno_Paginator_Adapter_DbSelect($select));
		}

		/** If there is no data to display we set displayData flag to false.
		 *  Paginator_View will not init all the view properties.
		 */
		if ($this->getPaginator()->getCurrentItemCount() == 0) {
			$this->_displayData = false; 
		}
		
		//Instantiate Form
		$this->setForm();

		$this->setOptions($options);
	}

	/**
	 * Perform Initialization based on the $request object
	 */
	public function init(Zend_Controller_Request_Http  $request)
	{
		if (!$this->isPaginationDisabled()) {
			$page  = $request->getParam('page', self::DEFAULT_PAGE);
			$count = $request->getParam('count', self::COUNT_PER_PAGE );
				
			//$count must not be bigger than total Paginator items
			$count = min($count, $this->getPaginator()->getTotalItemCount());
		}
		else {
			$page = self::DEFAULT_PAGE;
			$count = $this->getPaginator()->getTotalItemCount();
		}
		
		$this->getPaginator()->setCurrentPageNumber($page)
							->setItemCountPerPage($count);

		if ($this->getPaginator()->getCurrentItemCount() != 0 ) {
			//Create all checkboxes and submit actions for current paginator page.
			$this->getForm()->createCheckBoxes($this->getPaginator()->getItemCountPerPage());
			$this->getForm()->createSubmitActions($this->_submitActions);

			//Set Form Action to the current Controller Action that is executed
			$this->getForm()->setAction($request->getPathInfo());

				
			if ($request->isPost()) {
				//if checkboxes Default Values are not set, the form will fail validation.
				//if a forged request was made and session values doesn't exist the form will fail validation
				$this->getForm()->populateCheckboxesDefaultValues();

				//Paginator will not rendered
				$this->_displayData = false;
			}
			else {
				//Populate Checkboxes with refPks
				$this->getForm()->populateCheckboxes($this->getCurrentItemsRefPks(),true);
			}
		}
		else {
			//create submit actions form elements.
			$this->getForm()->createSubmitActions($this->_submitActions);
		}
	}

	/**
	 * Get a submit Action.
	 *
	 * @return submitAction or null
	 */
	public function getSubmitAction($submitAction)
	{
		return (array_key_exists($submitAction, $this->getSubmitActions()) ? $this->_submitActions[$submitAction] : null);
	}

	/**
	 * Get all SubmitActions Names.
	 */
	public function getSubmitActions()
	{
		return $this->_submitActions;
	}

	/**
	 * Set a new submit action. 
	 * If an action with the same name exists, it will be updated.
	 * 
	 * @param $name
	 * @param $action
	 * @param $controller
	 * @param $module
	 * @param $params 
	 * @return $this for a fluent interface 
	 */
	public function setSubmitAction($name, $action, $controller=null, $module=null, $params = array())
	{
		$this->_submitActions[$name] = array('action' 	 => $action,
											'controller' => $controller, 
											'module' 	 => $module, 
											'params'	 => $params);
		
		return $this;	
	}
	
	/** 
	 * Remove a previously setted submit action
	 * @param $name
	 * @return $this to provide a continuous interface 
	 */
	public function removeSubmitAction ($name)
	{
		unset($this->_submitActions[$name]); 
		return $this;
	}
	
	
	/**
	 * Get Reference Primary Keys from Current items (N)
	 *   
	 * @return array (idx => refPk), 
	 * 			where idx if from 1 to count(current items)  
	 */
	protected function getCurrentItemsRefPks()
	{
		if ($this->getPaginator()->getCurrentItemCount() == 0 ) {
			//there are no items to display.
		}
		$items = $this->getPaginator()->getCurrentItems();

		if (!array_key_exists('refPk',$items[0]) ) {
			//refPk key doesn't exist in the array, so we will be unable to add custom logic
			throw new Inno_Paginator_Extended_Exception('refPk was not set in paginator select',1);
		}

		$idx = 1;
		$refPks = array();

		foreach ($items as $item ) {
			$refPks[$idx] = $item['refPk'];
			$idx ++;
		}

		return $refPks;

	}
	
	
	/**
	 * return action that must be executed, and the refPks as an associative array
	 *
	 * @return array 
	 */
	public function getActionToExecute (Zend_Controller_Request_Abstract  $request)
	{
		if (null === $this->_actionToExecute) {
			//retrieve validated form data.
			$formValues =  $this->getForm()->getFormData($request);
			//retrieve submit action details 
			$submitAction = $this->getSubmitAction($formValues['action']);
			//retrieve referenced Primary Keys 
			$refPks = $formValues['refPks'];
	
			//set actionToExecute array with action/controller/module and refPks
			$this->_actionToExecute = array (
						'action' => isset($submitAction['action']) ? $submitAction['action']: '',
					  	'controller' => isset($submitAction['controller']) ? $submitAction['controller'] : '', 
					  	'module' => isset($submitAction['module']) ? $submitAction['module'] : '', 
						'params' => isset($submitAction['params']) ? $submitAction['params'] : array(),
					  	'refPks' => $refPks); 
		}
		return $this->_actionToExecute;
	}
	
	
	/**
	 * return selected Reference PKs
	 * 
	 * @param Zend_Controller_Request_Abstract $request 
	 * @return null | array()
	 */
	public function getSelectedRefPks(Zend_Controller_Request_Abstract  $request)
	{
		$formValues 	=  $this->getForm()->getFormData($request);
		$selectedRefPks = $formValues['refPks']; 
		
		return $selectedRefPks; 
	}
	

	/**
	 * Set Order By clause for the select object
	 * It is used to allow for easy ordering
	 *   
	 * @param $column that will be used for sorting the result
	 * @param $order (ASC / DSC); ASC is default
	 * @return unknown_type
	 */
	public function setOrder($column = null, $order = 'ASC') 
	{
		$orderList = array ('ASC', 'DESC');
		$select = $this->_select;
		if (!in_array($order, $orderList)) {
			throw new Zend_Exception('order'. $order. 'is not in the orderList'. print_r($orderList));
		} 
		//Zend_Debug::dump($select->__toString());
		if ($select instanceof Zend_Db_Select) {
			//insert the sort column and order 
			$select->order($column. ' '. $order);
		}
		
		//Zend_Debug::dump($select->__toString());
	}
	
	
	public function render()
	{
		return $this->getDataGridView()->render();
	}
	
	/**
	 * Serialize as string
	 * Proxies to {@link render()}.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try {
			$return = $this->render();
			return $return;
		} catch (Exception $e) {
			//trigger_error($e->getMessage(), E_USER_WARNING);
			Zend_Debug::dump($e);
		}
		return '';
	}
	
	/**
	 * @deprecated Use Zend_Controller_Action_Helper_PaginatorExtended
	 * @param $request
	 * @return none
	 */
	public function run(Zend_Controller_Request_Http $request)
	{
		trigger_error('extPaginator->run() is deprecated and will be removed. 
						Use Zend_Controller_Action_Helper_PaginatorExtended');
		/*
		$this->init($request); 	
		//	Zend_Debug::dump($request,'request');
		if ($request->isPost()) {
			if ($this->getName() == $request->getPost('paginatorName')) {
				
				//if request is post .. paginator should execute the submited action
				//executing the action can be performed by telling Front COntroller to dispatch another action.
				$requestedAction = $this->getActionToExecute($request);
				
				Zend_Controller_Front::getInstance()->getRequest()
					->setActionName($requestedAction['action'])
					->setControllerName($requestedAction['controller'])
					->setModuleName($requestedAction['module'])
					->setParams(array ('refPks' => $requestedAction['refPks']))
					->setDispatched(false);
			
			}
		}
		*/
	}
}
?>