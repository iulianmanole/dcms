<?php

class forms_RelationLocationPdu extends Inno_Dojo_Form 
{
	
	protected $_pdu  = '';

	protected $_location = '';
	
	
	protected $_submit = ''; 
	
	
	function __construct($options = null)
	{
		parent::__construct($options);
		
		/**
		 * Define form elements
		 */
		
		$this->addAttribs(array('name'	=>'AddRack',
								'action'=>'/inventory/relation-location-pdu/add',
								'method'=>'post'));
		
		/**
		 * Define form elements
		 */
		
		/** Location */
		$this->_location 	= new Zend_Form_Element_Select('location');
		$this->_location->setLabel('Location')
				 		->addMultiOption('',"");
					
		/** Pdu  */
		$this->_pdu 	= new Zend_Form_Element_Select('pdu');
		$this->_pdu->setLabel('Pdu')
				 		->addMultiOption('',"");
						 		

		$this->_submit = new Zend_Form_Element_Submit('add', array('label'=>'Add Relation'));
		
		$this->addElements(array($this->_location, $this->_pdu, $this->_submit));	
		
		/** set default values */
		$this->setDefaultValues();
		/** wrap elements with div_ElemName */		
		$this->wrapElements();
	}	
	
	/**
	 * set default values for elements
	 */
	public function setDefaultValues()
	{		
		$locations	= new dcms_Locations();
		$this->_location->addMultiOptions($locations->getLocations());	 
		
		$pdus = new dcms_PwrDistributionUnits();
		$this->_pdu->addMultiOptions($pdus->findByKey(array(),'id','name'));
			 
	}
}
?>