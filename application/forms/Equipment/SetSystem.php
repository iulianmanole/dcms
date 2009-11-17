<?php
class forms_Equipment_SetSystem extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		$systems = $this->getModel()->getSystems();
		
		$this->addElement('multiselect', 'systems', array(
			'label' 		=> 'Systems',
			'required'		=> 'false',
			'MultiOptions'  => array('') + $systems,
			'size'			=> 10,
		));
		
	 	$this->addElement('submit', 'submit', array(
	 		'required'		=> 'false',
	 		'ignore'		=> 'true',	
	 	));
	 	
	 	$this->wrapElements();
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
