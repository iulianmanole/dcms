<?php
/**
 * @TODO: Add validators for multiselect. See type element for a POC.
 * @author Iulian
 * @todo 
 * 	-- Adauga Hw_Product dinamic
 *  -- adauga Rack dinamic
 */
class forms_Equipment_Add extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		$hwTypes 	= $this->getModel()->getHwTypes('equipment');
		$mfctrs 	= $this->getModel()->getManufacturers();
		$locations  = $this->getModel()->getLocations(); 
		//Zend_Debug::dump($types);
		
		$this->addElement('text', 'name', array(
	 		'label'		=> 'Equipment Name',
	 		'required'	=> 'true',
			'size'		=> '30',	
			
	 	));
	
		$this->addElement('select', 'hw_type', array(
	 		'label'			=> 'Hardware Type',
	 		'required'		=> 'true',
			'multiOptions'	=> array('') + $hwTypes,
			'validators'	=>array(	
								array('InArray',false,array(array_keys($hwTypes)))
			),	
			'errorMessages' => array ('Please select a value from the drop-down list'),
				
	 	));
	 	
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
	 		'label'			=> 'Hardware Product',
	 		'required'		=> 'true',
			'multiOptions'	=> array(), // these options will be completed via XHR at runtime.
	  									// dojo logic is defined in the view rack/add.phtml
	  		'registerInArrayValidator' => false,							
	 	));

	 	
	 	$this->addElement('select', 'hw_location_id', array(
	 		'label'			=> 'Location',
	 		'required'		=> 'true',
			'multiOptions'	=> array('') + $locations,
	 		'validators'	=>array(	
								array('InArray',false,array(array_keys($locations)))
			),	
	 		'errorMessages' => array ('Please select a value from the drop-down list'),		 	
	 	));
	 	
	 	$this->addElement('select', 'rack_id',array(
	 		'label' => 'Rack Name',
	 		'registerInArrayValidator' => false,
	 	));
	 	/**
	 	$this->addElement('hidden', 'rack_name', array());
	 	*/ 		 	
	 	
	 	$this->addElement('submit', 'submit', array(
	 		'required'		=> 'false',
	 		'ignore'		=> 'true',	
	 	));
	 	/** 
	 	 * Wrap elements with div_{elementName} div.
	 	 */
	 	$this->wrapElements();
	}
	
	/**
	 * Updates hw_product_id list based on the formData.
	 * it requires formData['manufacturer_id'] and ['hw_type']
	 * 
	 * @param $formData
	 * @return Zend_Form_Element
	 */
	public function updateHwProductIdElement($formData)
	{	
		//Zend_Debug::dump($formData, 'FORM DATA');
		if (isset($formData['manufacturer']) && isset($formData['hw_type'])) {
			
			$manufacturerId = $formData['manufacturer'];
			$hwType = $formData['hw_type'];
			$products 	= $this->getModel()->getHwProducts($manufacturerId, $hwType);

			//add a '' element as the begining of the array
			$products 	= array('') + $products;				
			
			$elem = $this->getElement('hw_product_id');
			$elem->setMultiOptions($products)
				 ->setDisableLoadDefaultDecorators(true);
			
			return $elem;
		}
	}

	public function updateRackElement($formData)
	{	
		//default element will be sent if form data is not set. 
		$elem = $this->getElement('rack_id');
		$elem->setDisableLoadDefaultDecorators(true);
		
		if (isset($formData['hw_location_id']) && isset($formData['hw_product_id']) &&
			$formData['hw_location_id'] != 0 && $formData['hw_product_id'] != 0) {
			
			$locationId = $formData['hw_location_id'];
			$hwPrdId 	= $formData['hw_product_id'];
			$racks 		= $this->getModel()->getRacksByLocation($locationId);
			$racks = array('') + $racks;
			
			if (null ===  $racks) {
				// create the text element and insert it. 
				$elem = new Zend_Form_Element_Text('rack_name', array(
	 					'label'		=> 'Rack Name',
	 					'required'	=> 'true',
						'size'		=> '30',	
						));	 
				//we create the decorator as div_rack_id in order to executre XHR correctly.		
				$divName = 'div_rack_id';
				$elem->addDecorator(array('divTag' => 'HtmlTag'), array('tag'=>'div', 'id'=>$divName));
					 
			}
			else {
				//create the list element and insert it.
				$elem->setMultiOptions($racks);
			}			
		} 
		return $elem;
		
	}
	
	public function setModel($model)
	{
	 	$this->_model = $model; 
	 	return $this; 
	}
	/**
	 * 
	 * @return Model_EquipmentGateway
	 */ 
	public function getModel()
	{
	 	if (null === $this->_model) {
	 		$this->setModel(new Model_EquipmentGateway());
	 	}	
		return $this->_model;
	}
}
?>