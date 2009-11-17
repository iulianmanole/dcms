<?php
class Model_Location extends Inno_Organizer 
{
	protected $_defaultGatewayClass = 'Model_LocationGateway';
	
	public function __construct(array $data, array $options = array())
	{	
		parent::__construct($data, $options);
	}
}
?>