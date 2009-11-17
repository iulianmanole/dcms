<?php
class Model_LocationGateway extends Inno_Organizer_Gateway
{
	//define location types constants. 
	const TYPE_OFFICE = 'office'; 
	const TYPE_SERVERROOM = 'server room';
	const TYPE_WAREHOUSE = 'warehouse';
	const TYPE_BUILDING  = 'building';
	
	protected $_locationTypes = array (
					self::TYPE_OFFICE,
					self::TYPE_SERVERROOM,
					self::TYPE_WAREHOUSE 
	); 
	
	protected $_primaryTable ='dcms_Locations';
	
	
	
} 
?>