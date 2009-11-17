<?php

/** Include Inno_Db_Table_Abstract */
require_once 'Inno/Db/Table/Abstract.php';


class dcms_Locations extends Inno_Db_Table_Abstract
{
	protected $_name = "dcms.locations";

	public function pathToRoot($id)
	{
		$select = $this->select()->setIntegrityCheck(false)
			->from(array('node'=>$this->info('name')),
					array(/*'nodeName'=>'node.name', 'nodeId'=>'node.id'*/))
			->join(array('parent'=>$this->info('name')),
					'node.lft between parent.lft and parent.rgt',
					array('name'=>'parent.name', 'id'=>'parent.id'))
			->where('node.id = ?', $id)
			//->where('node.id != parent.id')
			->order('parent.lft ASC');
			
		return $select;		
	}
	
}
?>