<?php
class forms_Equipment_SetGroup extends Inno_Dojo_Form
{
	protected $_model;

	public function init()
	{
		$groups = $this->getModel()->getGroups();
		
		$this->addElement('multiselect', 'groups', array(
			'label' 		=> 'Groups',
			'required'		=> 'false',
			'MultiOptions'  => array('') + $groups,
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
