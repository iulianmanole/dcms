<?php
/** Include Inno_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';

class dcms_Rack extends Inno_Db_Table_Abstract 
{
	protected $_name = "dcms.rack";

	public function getRacksByLocation($locationId)
	{
		return $this->getAdapter()->fetchPairs("select id, name from dcms.rack where location_id = '". (int)$locationId."'");
	}
	
}
?>