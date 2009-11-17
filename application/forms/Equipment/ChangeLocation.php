<?php

class forms_Equipment_ChangeLocation extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		$locations 	= $this->getModel()->getLocations();
		
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
	  		'multiOptions'	=> array(),// these options will be completed via XHR at runtime.
	  								   // dojo logic is defined in the view rack/change-location.phtml
	 		'registerInArrayValidator' => false,
	 	));
 	
	 	$this->addElement('submit', 'submit', array(
	 		'required'		=> 'false',
	 		'ignore'		=> 'true',	
	 	));
	 	
	 	$this->wrapElements();
	}
	
	public function updateRackElement($formData)
	{	
		//default element will be sent if form data is not set. 
		$elem = $this->getElement('rack_id');
		$elem->setDisableLoadDefaultDecorators(true);
		
		if (isset($formData['hw_location_id'])) {
			
			$locationId = $formData['hw_location_id'];
			$racks 		= $this->getModel()->getRacksByLocation($locationId);
			$racks = array('') + $racks;
			
			//create the list element and insert it.
			$elem->setMultiOptions($racks);			
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