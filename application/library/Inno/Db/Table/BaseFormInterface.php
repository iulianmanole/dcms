<?php
/**
 * general interface that must be implemented by data model
 * to provide functionality to the controller that will use this model.	
 */ 

interface Inno_Db_Table_BaseFormInterface 
{
	/**
	 * returned select object that will be used to parse model data using BaseForm Controller.
	 * additional criteria can be used to restrict the select.
	 * @param $criteria - array on which the where clause will be build. 
	 * 					@todo - columns that are part of the criteria will be validated first.  
	 * 
	 * @return Zend_Db_Select 
	 */
	public function selectElements(array $criteria = null);	
}
?>