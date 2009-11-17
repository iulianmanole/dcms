<?php
class Inventory_ManufacturerController extends Inno_Controller_Action
{
	/** 
	 * Controller's Model
	 * 
	 * @var Model_Manufacturer
	 */
	protected $_model; 
	
	/** 
	 * @see application/library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	public function preDispatch()
	{
		//Instantiate an empty model.
		$this->_model 		= new Model_Manufacturer(array(), array( 'gateway' => new Model_ManufacturerGateway()));
		$this->view->model 	= $this->_model;
	}
	
	public function addAction()
	{
		
	}
	
	public function deleteAction()
	{
		$modelGw 	 = $this->_getModel()->getGateway();
		$toDeleteIds = $this->getRequest()->getParam('refPks');
		
		$deletedItems = $modelGw->delete($toDeleteIds);
		if ( $deletedItems == false) {
			$this->getFlashMessenger()->addMessage('The selected objects were not deleted.');
		}
		else {
			$this->getFlashMessenger()->addMessage('The selected('.$deletedItems.')'.' objects were deleted.');
		}
		
		$this->getRedirector()->gotoSimple('list');
	}
	
	/**
	 * list Manufacturers
	 * @return none
	 */
	public function listAction()
	{
		$modelGw 	= $this->_getModel()->getGateway();
		$request 	= $this->getRequest();
		
		$extPaginator 	= new Inno_Paginator_Extended('mfctrs', $modelGw->selectManufacturers());
		
		$extPaginator->setSubmitAction('Delete', 'delete', 'manufacturer', 'inventory')
				->setSubmitAction('Add', 'add', 'manufacturer', 'inventory');

		$extPaginator->getPaginatorView()->setTitle('Manufacturers');
		
		$extPaginator->getPaginatorView()
				->setDisplayAttribute('Name', array ('name'));
				
		$extPaginator->run($request);
		
		//Assign variables to View
		$this->view->assign( array(
						'extPaginator' => $extPaginator,
		));		
	}
	
	public function viewAction()
	{
		//@TODO
		echo "NOT Implemented."; 
	}
	
	
	
	/**
	 * process Add Action
	 * @return none
	 */
	public function processAddAction()
	{
		$request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->getRedirector()->gotoSimple('list');
        }
        
        $rack = $this->_getModel();

		if (!$id = $rack->save($request->getPost())) {
            // failed
            return $this->render('add');
        }
        //Redirect to view the newly added manufacturer
        $this->getRedirector()->gotoSimple('list','manufacturer','inventory',array('id' => $id));
	}
	
	/**
	 * get Model_Manufacturer associated with this controller.
	 * @return Model_Manufacturer
	 */	
	protected function _getModel()
	{
		return $this->_model;
	}
}
?>