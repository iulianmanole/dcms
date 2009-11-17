<?php

class Inno_Equipment_Component_Adapter extends Inno_Equipment_Component {
	
	/**
	 * ?!?!? nu stiu daca e bine sa le fac constante, pentru ca apoi va trebui sa umblu in aplicatie pentru inca un adapter
	const TYPE_REMOTE_MANAGEMENT= 1;
	const TYPE_NETWORK_ETHERNET = 2;
	const TYPE_NETWORK_FIBER	= 3;   
	const TYPE_HBA				= 4;
	*/
	
	/**
	 * Number of ports available in this adapters
	 * 
	 * @var int
	 */
	protected $_portsCount ; 
	
	/**
	 * Port Speed 
	 * 
	 * @var int
	 */
	protected $_portSpeed ; 
	
	/**
	 *  Port Connector Type. 
	 * Ex: For An HBA it will be LC or SC, or any other fiber connector if it is allowed for that model.
	 * 
	 * @var string
	 */
	protected $_portConnector; 
	
	
	/**
	 * Adapter Slot Position is used for Identification
	 * 
	 * @var string
	 */
	protected $_slotPosition ;
	
}
?>