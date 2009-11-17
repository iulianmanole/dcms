<?php
/* Load Zend_Controller_Action class */
/**
 * PROOF OF CONCEPT FOR TABS.
 * 
 * 
 * 
 * 
 */
require_once 'Inno/Controller/Action.php';

class Inventory_TestController extends Inno_Controller_Action_BaseForm
{
	/**
	 * tabs will have this structure : 
	 * 	 array ( 'actionName'=>array ( 	'action'=>'action to execute', 
	 * 									'name'=> 'Name to display for this tab'),
	 * 			....
	 * 	 )
	 * Example: 
	 * For test action the tab will look like this : 
	 * 	array('test'=>array('action'=>'test','name'=>'Test Action')
	 * 
	 * Observation:There is no need to specify the module and controller, because the tabs will be used 
	 * 				for actions that are in this controller.
	 */
	protected $_tabs = array(	'test' => array('action' => '/inventory/test/test',
												'name' => 'Test 1st Action'), 
						   		'test2'=> array('action' => '/inventory/test/test2', 
						   						'name' => 'Test 2nd Action'),
								'test3'=> array('action' => '/inventory/test/test3', 
												'name' => 'Test 3rd Action')
						);
	

	public function init()
	{
		parent::init();
		
	}
	public function testAction()
	{	
		
		$this->setTabsToView();
	}
	
	public function test2Action()
	{
		$this->setTabsToView();
	}
	
	/**
	 * Create Tabs for this controller.
	 * Every action of this controller will display the Tabs
	 * Every action will display his own tab as selected.
	 */
	public function setTabsToView() 
	{
		$this->view->tabs = $this->_tabs;
		$this->view->selectedTab = $this->getRequest()->getActionName();
	}
	
}
?>