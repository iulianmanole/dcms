<?php

class Inventory_GroupController extends Inno_Controller_Action
{
	public function init()
	{
		parent::init();
		
		/**
		 * Instantiate Organizer Helper 
		 */
		$this->getOrganizerHelper()->setOrganizerClass('Model_Group');
		
		//init the organizerDiv for all actions of this controller.
		$organizer = $this->getOrganizerHelper()->getActiveOrganizer(); 
		$this->view->layout()->organizerDiv = $this->view->partial('partialOrganizerPathDiv.phtml', array(
													'organizerPath' => $organizer->fetchPath(),
													'url' 			=> $this->getActionAsString($this->getRequest()),
											  ));
	}
	
	public function listAction () 
	{
		$organizer = $this->getOrganizerHelper()->getActiveOrganizer();
		//Zend_Debug::dump($organizer->getGateway()->selectChilds($organizer)->__toString());
		
		//Instantiate the DataGrid 
		$dataGrid = new Inno_DataGrid('group', 
								$organizer->getGateway()->selectChilds($organizer), 
								array('paginationDisabled' => true));
		
		$dataGrid->setSubmitAction('Delete', 'delete', 'group', 'inventory')
 			 	->setSubmitAction('Add', 'add', 'group', 'inventory',
 			 						$this->getRequest()->getUserParams());
 		
 		$dataGrid->getDataGridView()->setTitle('Groups');

		$dataGrid->getDataGridView()
			->setDisplayAttribute('Sub Organizer', array('nodeName'));	

		$dataGrid->getDataGridView()
			->setDetailLink('Sub Organizer','list','group','inventory',array('id' => 'nodeId'));

		//Run the dataGrid 	
		$this->getDataGridHelper()->run($dataGrid);
		
		//Assign dataGrid object to the View
		$this->view->assign( array(
						'dataGrid' => $dataGrid,
			));	
	}
	
	public function addAction()
	{
		$returnUri = $this->getDataGridHelper()->getSavedReturnAction();
		$this->getOrganizerHelper()->add($returnUri);
		
	}
	
	public function deleteAction()
	{
		$organizerIds 	= $this->getDataGridHelper()->getSavedRefPks();
		$OrganizerRefUrl = $this->getDataGridHelper()->getSavedReturnAction();
		$this->getOrganizerHelper()->delete($organizerIds, $OrganizerRefUrl);
	}
}

?>