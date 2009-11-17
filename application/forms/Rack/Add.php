<?php
/**
 * @TODO: Add validators for multiselect. See type element for a POC.
 * @author Iulian
 *
 */
class forms_Rack_Add extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		//$types 		= $this->getModel()->getTypes();
		$location 	= $this->getModel()->getLocations();
		$mfctrs 	= $this->getModel()->getManufacturers();
	
		//Zend_Debug::dump($types);
		
		$this->addElement('text', 'name', array(
	 		'label'		=> 'Rack Name',
	 		'required'	=> 'true',	
			
	 	));
	/*
		$this->addElement('select', 'type', array(
	 		'label'			=> 'Rack Type',
	 		'required'		=> 'true',
			'multiOptions'	=> array('') + $types,
			'validators'	=>array(	
								array('InArray',false,array(array_keys($types)))
			),	
			'errorMessages' => array ('Please select a value from the drop-down list'),	
	 	));
	 */	
	 	$this->addElement('select', 'manufacturer', array(
	 		'label'			=> 'Manufacturer',
	 		'required'		=> 'true',
			'multiOptions'	=> array('') + $mfctrs,
	 		'validators'	=>array(	
								array('InArray',false,array(array_keys($mfctrs)))
			),	
	 		'errorMessages' => array ('Please select a value from the drop-down list'),	
	 	));
		
	  	$this->addElement('select', 'hw_product_id', array(
	 		'label'			=> 'Product Name',
	 		'required'		=> 'true',
			'multiOptions'	=> array(), // these options will be completed via XHR at runtime.
	  									// dojo logic is defined in the view rack/add.phtml
	  		'registerInArrayValidator' => false,							
	 	));
	 	
	 	$this->addElement('select', 'location_id', array(
	 		'label'			=> 'Location',
	 		'required'		=> 'true',
			'multiOptions'	=> array('') + $location,
	 		'validators'	=>array(	
								array('InArray',false,array(array_keys($location)))
			),	
	 		'errorMessages' => array ('Please select a value from the drop-down list'),		 	
	 	));

	 	$this->addElement('submit', 'submit', array(
	 		'required'		=> 'false',
	 		'ignore'		=> 'true',	
	 	));
	 	/** 
	 	 * Wrap elements with div_elementName div.
	 	 */
	 	$this->wrapElements();
	}
	
	/**
	 * Updates hw_product_id list based on the formData.
	 * it requires formData['manufacturer_id']
	 * 
	 * @param $formData
	 * @return Zend_Form_Element
	 */
	public function updateHwProductIdElement($formData)
	{	
		if (isset($formData['manufacturer'])) {
			$manufacturerId = $formData['manufacturer'];
			$rackProducts 	= $this->getModel()->getRackProducts($manufacturerId);
			//add a '' element as the begining of the array
			$rackProducts 	= array('') + $rackProducts;				
		
			$elem = $this->getElement('hw_product_id');
			$elem->setMultiOptions($rackProducts)
				 ->setDisableLoadDefaultDecorators(true);
			
			return $elem;
		}
	}
	
	public function setModel($model)
	{
	 	$this->_model = $model; 
	 	return $this; 
	}
	/**
	 * 
	 * @return Model_RackGateway
	 */ 
	public function getModel()
	{
	 	if (null === $this->_model) {
	 		$this->setModel(new Model_RackGateway());
	 	}	
		return $this->_model;
	}
}
?>