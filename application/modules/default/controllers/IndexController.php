<?php

/* Load Zend_Controller_Action class */
require_once 'Zend/Controller/Action.php';

class IndexController extends Inno_Controller_Action 
{
	function preDispatch()
	{
		
	}
	function indexAction()
	{
		echo " IndexController/IndexAction";
	}
	
	function testAction() {
		
		echo "indexController/testAction";
		
		/*****************Delete node Example ****************/
		$organizer = new Inno_Equipment_Class('11');
		$organizer->manage_deleteOrganizer();
		
		/***************Add Child Example *********
		$root = new Inno_Equipment_Class('27');
		
		$child = new Inno_Equipment_Class(NULL, 'testCh01'); 
		$root->manage_addChild($child);
		
		*/
		/***************findChild and FindPath to root Examples *********/
		//$root = new Inno_Equipment_Class('8');
		//Zend_Debug::dump($root->findChilds());
		//Zend_Debug::dump($root->findPath());
		
	}

	function test2Action() {
		print "INDEX --> TEST2 Action";
		
		$arr = array(
				'1' => array ( 'a'=>'xxx',
						'b'=>'yyy',
						'c'=>'zzz'
					   ),
				'2' => array ('a'=>'abc',
					   	'b' =>'def',
					   	'c' => 'ghi'
					   )	   
		);
		
		$this->view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

		
		$form = new forms_DojoTest();
		
		$this->view->form = $form; 
		
		
	}
	
	function test3Action() {
		print "TEST3 Action has been executed.";
		
		
	}
	
}

?>