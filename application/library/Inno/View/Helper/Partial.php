<?php

/** Zend_View_Helper_Abstract.php */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper for rendering a template fragment in its own variable scope.
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Inno_View_Helper_ContentMenu extends Zend_View_Helper_Abstract
{
    /**
     * Variable to which object will be assigned
     * @var string
     */
    protected $_actions = array (); 
	
    public function ContentMenu($actions = null, $selectedAction = null)
    {
    	
    }
}
