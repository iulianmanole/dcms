<?php
/**
 * @date 1 martie 2009
 * @author Iulian
 *
 */
class Inventory_HwProductController extends Inno_Controller_Action
{
	
	//protected $_defaultGatewayClass = 'Model_HwProductGateway';
	/**
	 * @var Model_HwProduct model used by this controller.
	 */
	protected $_model = null;

	/**
	 * @return Model_HwProduct
	 */
	public function getModel()
	{
		return $this->_model;
	}
	
	public function init ()
	{
		/**
		 * Instantiate Organizer Helper 
		 */
		$this->getOrganizerHelper()->setOrganizerClass('Model_HwProduct');
		
	}
	
	public function preDispatch()
	{
		//Instantiate an empty model.
		$this->_model 		= new Model_HwProduct(array(), array( 'gateway' => new Model_HwProductGateway()));
		$this->view->model 	= $this->_model;
	}
	
	/**
	 * @todo: REDO this list action,
	 * 	because the Paginator must be instantiated in controller and not in the view
	 * @param manufacturer 	- manufacturerID via _GET
	 * @param type 			- product type via _GET
	 *
	 *
	 */
	public function listAction ()
	{
		/*
		$manufacturer 	= $this->_getParam('manufacturer');
		$type 			= $this->_getParam('type');
		$modelGw 		= new Model_HwProductGateway();
		$request 		= $this->getRequest();

		//create the criteria to filter the list.
		//suported filters are manufacturer and type.
		$criteria = array ();
		if ($manufacturer) {
			$criteria[] = array ('manufacturer_id = ?', $manufacturer);
		}
		if ($type)	{
			$criteria[] = array ('type_id = ?', $type);
		}

		$selectAllProds = $modelGw->selectAllProducts($criteria);
		$extPaginator 	 = new Inno_DataGrid('HWProducts', $selectAllProds);


		$extPaginator->setSubmitAction('Delete', 'delete', 'hw-product', 'inventory')
		->setSubmitAction('Add', 'add', 'hw-product', 'inventory');

		$extPaginator->getDataGridView()->setTitle('Hardware Products');

		$extPaginator->getDataGridView()
			->setDisplayAttribute('Product Name', array ('manufacturer', 'name'))
			->setDisplayAttribute('Manufacturer', array ('manufacturer'))
			->setDisplayAttribute('Type', array('type'));

		$extPaginator->getDataGridView()
			->setDetailLink('Product Name', 'view', 'hw-product', 'inventory', array('id'=>'id'))
			->setDetailLink('Type', 'view', 'hw-type', 'inventory', array ('id' => 'type_id'));


		$this->getDataGridHelper()->run($extPaginator);

		//Assign variables to View
		$this->view->assign( array(
						'dataGrid' => $extPaginator,
		));
		*/
	}

	/**
	 * details of a given product ID
	 * @param id
	 *
	 */
	public function viewAction()
	{
		//get Id param from request
		if (!$id = $this->_getParam('id')) {
			$this->getFlashMessenger()->addMessage('Product ID was not specified');
			$this->getRedirector()->gotoSimple('list');
		}

		//fetch requested ProductID from DB
		if (!$this->_model->fetch($id)) {
			$this->render('not-found');
		} else {
			$this->view->model = $this->_model;
		}
	}

	/**
	 * Add a new product
	 */
	public function addAction()
	{

	}

	/**
	 * @TODO resolve key Constraint Issues. Unable to delete a product if he already has attached equipments.
	 */
	public function deleteAction()
	{
		$toDeleteIds 	 = $this->getRequest()->getUserParam('refPks');
		
		if (count($toDeleteIds)) {
			$deletedProducts = $this->getModel()->getGateway()->deleteProducts($toDeleteIds);
			if ($deletedProducts) {
				$objectName = $deletedProducts-1 ? 'objects' : 'object';
				$msg = 'You have deleted '.$deletedProducts.' '.$objectName;
			} else {
				$msg = 'Unable to delete Data, probably because it is referenced. See Error Log for further details.';
			}
		}
		else {
			$msg = 'No item was selected to be deleted.';
		}
		
		//redirect to the previous operation add info message
		$this->getFlashMessenger()->addMessage($msg);
		$this->getRedirector()->gotoUrl($this->getRequest()->getRequestUri());
	}

	/**
	 * Process AddAction Form.
	 *
	 * @return none
	 */
	public function processAddAction()
	{
		$request = $this->getRequest();
		if (!$request->isPost()) {
			return $this->getRedirector()->gotoSimple('list');
		}

		$product = $this->getModel();

		if (!$id = $product->save($request->getPost())) {
			// failed
			return $this->render('add');
		}
		//Redirect to view the newly added hwProduct.
		$this->getRedirector()->gotoSimple('list','hw-product','inventory',array('id' => $id));
	}

	public function testAction()
	{
		$manuf = $this->getModel()->getGateway()->getManufacturers();
		$hwTypes = $this->getModel()->getGateway()->getHwTypes();

		echo "Manufacturers";
		Zend_Debug::dump($manuf);

		echo "HwTypes<br>";
		Zend_Debug::dump($hwTypes);
	}
}
?>