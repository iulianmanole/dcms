<?php

abstract class Inno_DataMapper 
{	
	/**
	 * 	The name of the mapped class; 
	 * 	example : $_mappedClass = 'Inno_Equipment_Device'
	 * 
	 * 
	 */
	protected $_mappedClass ;
	
	/**
	 *  Mapped Tables that contain mapped Class atributes
	 *  Must be at least one table  
	 */
	protected $_mappingTables ;
	
	
	/**
	 *  Atributes mapping 
	 * 	An Associative array atribute to table.column 
	 *  
	 */
	protected $_atributesMapping;
	
	
	function __construct()
	{
		 
	}
	
}

?>