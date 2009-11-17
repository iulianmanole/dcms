<?php


/**
 * Mapp Equipment_Device Object to persistent storage(database)
 * This class define apropriate methods to load and save data to persistent storage.  
 * Must implement appropriate methods: load(), save().
 */
class Inno_DataMapper_Equipment_Device extends Inno_DataMapper
{
	/** the class for whitch the mapping is performed */
	protected $_mappedClass = 'Inno_Equipment_Device';
	
	/** array with the table classes that will be mapped to  */
	protected $_mappingTables = array();
	 
	function __construct()
	{
		parent::__construct();
	}
	/**
	 * Save an Inno_Equipment_Device object to the db.
	 */
	protected function save ($equipment) {
		/**
		 * 
		 */
		
		
	}
	
}
?>