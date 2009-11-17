<?php
/**
 * Form used to render DataGrid
 * 
 * 
 */


class Inno_DataGrid_Form_PaginatorMultiCheckBox extends Inno_Dojo_Form
{
	/**
	 * Namespace where form values will be temporary saved.
	 */
	protected $_namespace; 
	
	public function __construct($options = null)
	{
		parent::__construct($options);
	
		
		$this->setMethod('post');

		//Create subforms 
		$actions = 	new Zend_Form_SubForm();
		$actions->setElementsBelongTo('actions');
		$this->addSubForm($actions,'actions');
		
		$checkboxes = new Zend_Form_SubForm();
		$this->addSubForm($checkboxes, 'checkboxes'); 	
		
		// Initialize the namespace
		$this->setNamespace();
		
		//Create Hidden Field PaginatorName
	}
	
	/**
	 * Get default namespace used to save formData.
	 * @return Zend_Session_Namespace
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}
	
	/**
	 * Create default unique namespace, with the same name as the form
	 */
	public function setNamespace()
	{
		$this->_namespace = new Zend_Session_Namespace($this->getName());
	}
	
	/**
	 * create a hidden field with the form name.
	 * This field will be used to determine which paginator was executed.
	 */
	public function createHiddenPaginatorName($name)
	{
		$paginatorName = new Zend_Form_Element_Hidden(array('name'=>'paginatorName'));
		
		$paginatorName->setValue($name)
					  ->removeDecorator('HtmlTag')
				 	  ->removeDecorator('Label');
		;
		
		$this->addElement($paginatorName); 
	}
	/**
	 * Create empty checkboxes
	 */
	public function createCheckBoxes ($count)
	{
		for($id = 1; $id <= $count; $id++) {
			
			$elem = new Zend_Form_Element_Checkbox(''.$id);
			//$elem->setBelongsTo('checkboxes'); 
			
			$elem->removeDecorator('HtmlTag')
				 ->removeDecorator('Label');
			
			$this->getSubForm('checkboxes')->addElement($elem);
		}
	}
	
	public function createSubmitActions( $submitActions ) 
	{
		foreach ($submitActions as $actionName=>$actionValue) {
			$elem = new Zend_Form_Element_Submit($actionName, array ('disableLoadDefaultDecorators'=>true)); 
		
			$elem->addDecorator('ViewHelper')
				 ->setBelongsTo('actions');
				 
			$this->getSubForm('actions')->addElement($elem);
		}
	}
	/**
	 * Save default checkboxes to Session
	 * 
	 * 
	 */
	protected function persistDefaultCheckboxes ($defaultValues)
	{
		$this->getNamespace()->defCheckboxes = $defaultValues; 
	}
	
	/**
	 * Get Default checkBoxes from Session
	 * 
	 * @return array checkbox ElementId => checked value
	 */
	public function retrieveDefaultCheckboxes ()
	{
		return $this->getNamespace()->defCheckboxes; 
	}

	/**
	 * Populate Checkboxes with default values that are stored in Session
	 */
	public function populateCheckboxesDefaultValues() 
	{		
		$defaultValues = $this->retrieveDefaultCheckboxes();
		$this->populateCheckboxes($defaultValues); 
	}
	
	/**
	 * Populate form checkboxes and persist them in session if $persist is true.
	 * 
	 */
	public function populateCheckboxes($checkboxesValues, $persist = false)
	{	
		foreach ($checkboxesValues as $cbx => $value) {
			$elem = $this->getSubForm('checkboxes')->getElement(''.$cbx);
			$elem->setCheckedValue($value);
		}
		
		if ($persist) 
		{
			//we'll save default values for the checkboxes
			$this->persistDefaultCheckboxes($checkboxesValues);	
		}
	}
	
	/**
	 *  Retrieve data from FORM, and return an array ('refPks'=>'''', 'action'=>), 
	 *  with action that must be executed and the PK that will be used for that action.
	 * 
	 * @return null|array('refPks'=>'''', 'action'=>'')
	 * 
	 */
	public function getFormData(Zend_Controller_Request_Http $request)
	{
		//get validated checkboxes and action
		if ($this->isValid($request->getPost())){
			$values = $this->getValues();
			
			//get validated refPks
			$cbxs = $values['checkboxes'];
			$refPks = array();
			foreach ($cbxs as $cbx){
				if ($cbx != '0'){
					$refPks[]=$cbx;  
				}
			}
			//get validated action.
			$acts = $values['actions'];
			foreach ($acts as $act) {
				if ($act !== null ) {
					$action = $act;
					//we break execution because there is only one action. 
					break; 
				}
			}
			//Unset used parameters that were retrieved
			//unset($_POST['checkboxes']);
			//unset($_POST['actions']);
			
			return array(
						'refPks'=>$refPks, 
						 'action'=>$action
				   );
		}
		else {
			return null; 
		}
	}	
}
?>