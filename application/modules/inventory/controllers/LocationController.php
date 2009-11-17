<?php
/**
 * 
 * Location Controller manipulates Location Organizers. 
 * @date 5 martie 2009
 * @author Iulian
 *
 */
class Inventory_LocationController extends Inno_Controller_Action
{	
	/** location that will be used for actions that require location to be set. */
	protected $_location = '';

	/** Model_Location used by this controller */
	protected $_model = ''; 
	
	/**
	 * Return the current location as it is set in this controller
	 * 
	 * @return string $location
	 */
	protected function _getLocation()
	{
		if ($this->_location === '') {
			$this->_setLocation($this->getRequest()->getParam('id','1'));
		}
		return $this->_location;
	}
	
	protected function _setLocation($location)
	{
		$this->_location = $location;
	}
	
	/**
	 * 
	 * @return Model_Location
	 */
	protected function _getModel()
	{
		if ($this->_model === '') {
			$this->_model = new Model_Location(array('id' => $this->_getLocation()));
		}
		
		return $this->_model;
	}
	
	function init()
	{
		parent::init();
		
		/**
		 * Instantiate Organizer Helper 
		 */
		$this->getOrganizerHelper()->setOrganizerClass('Model_Location');
		
		/**
		 * instantiate the default contentMenu for this controller. 
		 */
		$contentMenu = new Zend_Navigation();
		$contentMenu->addPage(
							array(	'label'=> 'subs', 
									'module' => 'inventory',
									'controller' => 'location',
									'action' => 'list',
									'params' => $this->getRequest()->getParams(),
									
							));
		$contentMenu->addPage(
							array(	'label'=> 'All Locations', 
									'module' => 'inventory',
									'controller' => 'location',
									'action' => 'list-all-locations', 
									'params' => $this->getRequest()->getParams()
							));
							
		$contentMenu->addPage(
							array(	'label'=> 'test', 
									'module' => 'inventory',
									'controller' => 'location',
									'action' => 'test',
									'params' => $this->getRequest()->getParams()
							));
		$contentMenu->addPage(
							array(	'label'=> 'Racks', 
									'module' => 'inventory',
									'controller' => 'location',
									'action' => 'list-racks',
									'params' => $this->getRequest()->getParams()
							));					
							
		
		$this->view->navigation($contentMenu);
		
		//init the organizerDiv for all actions of this controller. 
		$this->view->layout()->organizerDiv = $this->view->partial('partialOrganizerPathDiv.phtml', array(
													'organizerPath' => $this->_getModel()->fetchPath(),
													'url' 			=> $this->getActionAsString($this->getRequest()),
											  ));
		
	}
	
	public function indexAction ()
	{
		$this->getRedirector()->gotoSimple('list');	
	}
	
	public function addAction()
	{
		$returnUri = $this->getDataGridHelper()->getSavedReturnAction();
		$this->getOrganizerHelper()->add($returnUri);
		
	}
	
	public function listAction()
	{
		$model = $this->_getModel();
		$request = $this->getRequest(); 
		
		$paginator = new Inno_DataGrid('location', 
												$model->getGateway()->selectChilds($model), 
												array('paginationDisabled' => true));
											
		$paginator->setSubmitAction('Delete', 'delete', 'location', 'inventory')
 			 	->setSubmitAction('Add', 'add', 'location', 'inventory',
 			 						$this->getRequest()->getUserParams());
 			 						 						
		$paginator->getDataGridView()->setTitle('Location Organizer');

		$paginator->getDataGridView()
			->setDisplayAttribute('sub-location', array('nodeName'));
			
		$paginator->getDataGridView()
			->setDetailLink('sub-location','list','location','inventory',array('id' => 'nodeId'));

			
		//$paginator->run($request);
		$this->getHelper('DataGrid')->run($paginator);
		
		$this->view->assign( array(
						'paginator' => $paginator,
			));
	}

	/**
	 * List All Locations. 
	 * The name of the location will be the entire path to root.   
	 * 
	 * @return none
	 */	
	public function listAllLocationsAction()
	{
		$this->_setLocation('1');
		$model = new Model_Location(array('id' => $this->_getLocation()));
		
		Zend_Debug::dump($model->getGateway()->fetchOrganizersByPath());
	}
	
	/**
	 * Delete selected Locations
	 * This action is used only to delete data selected previously in Location DataGrid.
	 * 
	 * @return none
	 */
	public function deleteAction()
	{
		$organizerIds 	= $this->getDataGridHelper()->getSavedRefPks();
		$OrganizerRefUrl = $this->getDataGridHelper()->getSavedReturnAction();
		$this->getOrganizerHelper()->delete($organizerIds, $OrganizerRefUrl);
	}
	
	public function testAction()
	{ 
		$this->_setLocation('1');
		$model = new Model_Location(array('id' => $this->_getLocation()));
		Zend_Debug::dump($model->getGateway()->fetchOrganizersByPath());
	}
	
	/** 
	 * list racks for the current 
	 * @return none
	 */
	public function listRacksAction()
	{
		$this->getRequest()->setParam('location',$this->_getLocation());
		$this->_forward('list','rack','inventory');
	}
	
}
?>