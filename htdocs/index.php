<?php
/** Define the ROOT_DIR */
define('ROOT_DIR', dirname(dirname(__FILE__)));
/**
 * set error reporting
 */


/**
 *	Building the include path; 
 *	It must contain the path to application Library and Application modules
 */
$include_path = '.' 
	. PATH_SEPARATOR . 'd:/!Sandbox/Workspace/php/DCMS/'
	. PATH_SEPARATOR . 'd:/!Sandbox/Workspace/php/DCMS/application/library/'
	. PATH_SEPARATOR . '../application/library/'
	. PATH_SEPARATOR . '../application/'
	. PATH_SEPARATOR . '../application/models/'
	. PATH_SEPARATOR . '../application/organizers/'
	. PATH_SEPARATOR . get_include_path();

set_include_path($include_path);



/** Loading the needed classes. */
//require_once 'Zend/Dump.php';
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Db.php';
require_once 'Zend/Db/Table.php';
require_once 'Zend/Controller/Front.php' ;
require_once 'Zend/Loader/Autoloader.php';

/** Setting AutoLoading Option */
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Inno_');
$autoloader->registerNamespace('Model_');
$autoloader->registerNamespace('dcms_');
$autoloader->registerNamespace('forms_');
//Zend_Debug::dump($autoloader->getRegisteredNamespaces());

/** 
 * 	Configuring Layout directory 
 * 	By default will use layout.phtml 
 * 	 
 */
Zend_Layout::startMvc(array('layoutPath' => ROOT_DIR.'/application/modules/default/views/layouts'));

/** Setting the config */
$config = new Zend_Config_Ini('../application/config/config.ini', 'production');


/** Setting registry */
$registry = Zend_Registry::getInstance();
$registry->set('config', $config);


/** Initializing DB connection */
/** Create the db adapter */
$db = Zend_Db::factory($registry->get('config')->database->type, 
							$registry->get('config')->database->toArray());
//Improvement : Add a try catch to see if the connection to the db is successfull							
/** Set the default Db Adapter */
Zend_Db_Table::setDefaultAdapter($db);

/** Initializing Logger and make it available for global operations*/
/*
 * framework - errors related to applications functionality Inno_*
 * application - errors realted to  
 */
$logFile = '../application/data/log/default.log';
$writer = new Zend_Log_Writer_Stream($logFile);
$logger = new Zend_Log($writer);

Zend_Registry::set('logger',$logger);

/**
 * Init Session parameters.
 */
Zend_Session::start();

/**
 * Init Front Controller
 */
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(false); //do not throw exceptions 
$frontController->addModuleDirectory('../application/modules');

/**
 * 	Dispatch Controller Action
 */
//	$frontController->dispatch();


try {
	$frontController->dispatch();
}
catch (Exception $e ) {
	
	echo $e->getMessage().'<br/>Code:'.
		 $e->getCode().'<br/>file: '.$e->getFile().'<br/> line:'.$e->getLine();
	Zend_Debug::dump($e->getTrace());
} 


?>