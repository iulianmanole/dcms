<?php
/** Include Inno_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';

class dcms_Equipment extends Inno_Db_Table_Abstract
{
	protected $_name = "dcms.equipment";

	public function __call($methodName, $args)
	{
		throw new Zend_Exception(sprintf('Method "%s" does not exist and was not trapped in __call()', $methodName), 500);
	}
}
?>