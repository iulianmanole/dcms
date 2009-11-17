<?php
class forms_PwrDistributionUnit_Add extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		$mfctrs = $this->getModel()->getManufacturers();
		$products = $this->getModel()->getHwProducts(array(1));
		
		$this->addElement('Text', 'name', array(
	 		'label'		=> 'Pwr Distribution Unit Name',
	 		'required'	=> 'true',		
	 	));
	
		$this->addElement('Select', 'manufacturer_id', array(
	 		'label'			=> 'Manufacturer',
	 		'required'		=> 'true',
			'multiOptions'	=> $mfctrs
	 	));
		
	 	$this->addElement('Select', 'type_id', array(
	 		'label'			=> 'Hardware Type',
	 		'required'		=> 'true',
	 		'disabled'		=> 'true',
			'multiOptions'	=> array('Equipment.PDU')
	 	));
	 	
	
	
	
	}
	 
	public function setModel($model)
	{
	 	$this->_model = $model; 
	 	return $this; 
	}
	/**
	 * 
	 * @return Model_PwrDistributionUnitGateway
	 */ 
	public function getModel()
	{
	 	if (null === $this->_model) {
	 		$this->setModel(new Model_PwrDistributionUnitGateway());
	 	}	
		return $this->_model;
	}
	
	
}
?>