<?php 
/**
 * Act as a presentation Layer for data that must be paginated. 
 * In this way we can apply presentation modification and customisation to paginated data. 
 * 
 * @TODO 
 * 	1. By default if no display attribute is set, one should display all itemAttributes.
 *  2. 
 * 
 * @author Iulian
 *
 */
class Inno_Paginator_Extended_View
{
	
	protected $_extendedPaginator; 

	/**
	 * List of available attributes for paginator items 
	 * 
	 * @var array
	 */
	protected $_itemAttributes;
	
	/**
	 * List of attributes that will be displayed. 
	 * Allow to customize paginator output. 
	 * Every display attribute can be formed using one, or many item attributes.
	 * 
	 * @var array ( name => value)
	 */
	protected $_displayAttributes;
	
	/**
	 * Actions that are attached to a displayed item attribute.
	 * params are used to send data to the selected action. 
	 * example: an action that displays the details of a certain equipment will require an id param. 
	 * 			In this case we will set params = array('id' => 'equipment_id')
	 * 			When paginator will be rendered, equipment_id will will be replaced with the actual value, in order to create the link correctly.
	 * 
	 * @var array ('name' => array( 'action', 'controller', 'module', 'params(array)')
	 * 
	 */
	protected $_detailActions; 
	
	/**
	 * Paginator title, that can be used in template.
	 */
	protected $_title = '...title is unset';
	
	
	protected $_view; 
	
	/**
	 * the view template that will be used
	 * @todo implement this in ExtendedPaginator.
	 */
	protected $_viewTemplate = 'displayDataGrid.phtml';

	protected $_viewTemplateEmptyData = 'displayDataGrid.phtml';

	protected $_viewTemplatePagianationDisabled = 'displayDataGrid.phtml';
	
	
//---------------geters and seters 	

	public function getTitle()
	{
		return $this->_title;
	}

	public function setTitle($title)
	{
		$this->_title = $title;
	}
	
	public function getViewTemplate()
	{
		return $this->_viewTemplate;
	}
	
	/**
     * Retrieves the view instance.  If none is registered, attempts to pull from ViewRenderer.
     * 
     * @return Zend_View_Interface|null
     */
    public function getView()
    {
        if ($this->_view === null) {
            /**
             * @see Zend_Controller_Action_HelperBroker
             */
            require_once 'Zend/Controller/Action/HelperBroker.php';
            
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if ($viewRenderer->view === null) {
                $viewRenderer->initView();
            }
            $this->_view = $viewRenderer->view;
        }

        return $this->_view;
    }
    
    /**
     * Sets the view object.
     * 
     * @param  Zend_View_Interface $view 
     * @return Zend_Paginator
     */
    public function setView(Zend_View_Interface $view = null)
    {
        $this->_view = $view;
        
        return $this;
    }
    
    /**
     * 
     * @return Inno_Paginator_Extended
     */
    public function getExtendedPaginator() 
    {
    	return $this->_extendedPaginator;
    }
    
    public function getItemAttributes()
    {
    	if (null === $this->_itemAttributes) {
    		$this->setItemAttributes();
    	}
    	//Zend_Debug::dump($this->_itemAttributes);
    	return $this->_itemAttributes; 
    }
    
    public function setItemAttributes()
    {
    	if  ($this->getExtendedPaginator()->isDataToDisplay()) {
    		$this->_itemAttributes = array_keys($this->getExtendedPaginator()->getPaginator()->getItem(0));
    	}
    	else {
    		$this->_itemAttributes = array();
    	}
    }
	 
    /**
     * Set one display attribute. This is used to aggregate more item attributes in one column. 
     * Use Case: name and surname can be showed together as one display attribute.
     *  
     * @param $name - display attribute name
     * @param $values - valid item attributes.
     * @return Inno_Paginator_Extended_View
     */
    public function setDisplayAttribute($name,array $values)
    {
    	if ($this->getExtendedPaginator()->isDataToDisplay()) {
    		//Select only the values that are valid item Attributes.
    		$validAttrs = array();
    		foreach ($values as $value) {
	    		if (in_array($value, $this->getItemAttributes())) {
    				$validAttrs[] = $value;
    			}
    		}
    		$this->_displayAttributes[$name] = $validAttrs;  
    	}
    	
    	return $this;
    }
    
    public function getDisplayAttributes()
    {
    	return $this->_displayAttributes; 
    }
    
	/**
	 *  get a DetailAction by name. 
	 *  @param string name  
	 */
	public function getDetailAction($name)
	{
		if (isset($this->_detailActions[$name])) {
			return $this->_detailActions[$name];
		}
		
		return null; 
	}

	/**
	 * return detailAction[$name] as a string "/module/controller/action"
	 * 								or 	"/controller/action"
	 * 								or 	"/action"
	 *
	 * @return string
	 */
	public function getDetailActionAsString($name)
	{
		if (isset($this->_detailActions[$name])) {
			$module = $this->_detailActions[$name]['module'] !== null ? $this->_detailActions[$name]['module'].'/' : '';
			$ctrl 	= $this->_detailActions[$name]['controller'] !== null ? $this->_detailActions[$name]['controller'].'/' : '';
			$action = $this->_detailActions[$name]['action'] !== null ? $this->_detailActions[$name]['action'] : '';

			$str = '/'.$module.$ctrl.$action;
			
			//Zend_Debug::dump($str);
			return $str;
		}
		
		return null; 
	}
	
	/**
	 * set a detail action.
	 * Performs validation for parameters. Any parameter that fails validation will not be added.
	 * @param $name - The name of the display Attribute to which we will atach the link. 
	 * 					It is case sensitive and white space sensitive. 
	 * @param $action
	 * @param $controller
	 * @param $module
	 * @param $params array key => item attribute that will be used to retrieve values.
	 * 				 	params are used to correctly construct detail link for every item attribute.  
	 * 					Example: we want a link that will end with an parameter /path/to/action/id/<id value>.
	 * 
	 * 				 
	 * @return Inno_Paginator_Extended_View ($this)
	 */
	public function setDetailAction($name, $action, $controller=null, $module=null, $params = array())
	{
		if (!$this->getExtendedPaginator()->isDataToDisplay()) {
			//no data to display, we return reference to allow for chaining.
			return $this;
		}
		
		$this->_detailActions[$name] = array(	'action' 	=> $action,
												'controller'=> $controller, 
												'module'	=> $module,
											);
		
		// Add Parameters to the detail action, with validation
		if (($params !== null) && (!is_array($params))) {
			throw new Exception(get_class($this) . ':params attribute must be an array key => attribute which will be used to retrieve value.');
		}
		else {
			$validParams = array (); 
			foreach ($params as $param => $itemAttribute) {
				if (in_array($itemAttribute, $this->getItemAttributes())) {
					$validParams[$param] = $itemAttribute;
				}else {
					//@TODO replace exception with error message in log.
					throw new Exception(get_class($this) . ': The parameter'. $itemAttribute. 'doesn\'t exist in item attributes');
				}	
			}
			$this->_detailActions[$name]['params'] = $validParams;
		}
		return $this;
	}
    
	/**
	 * 
	 */
	public function setOptions() 
	{
		if (isset($options['detailsAction'])) {
			$detailsAction = $options['detailsAction']; 
			
			if (array_key_exists('action', $detailsAction) &&
				array_key_exists('controller', $detailsAction) &&
				array_key_exists('module', $detailsAction)
			) {
				$this->setDetailsAction($detailsAction['action'],
				$detailsAction['controller'],
				$detailsAction['module']);
			}
			else {
				throw new Zend_Exception('detailsActions doesn\'t have all keys set(module,controller,action)'.
										'detailsAction is:'.Zend_Debug::dump($detailsAction));
			}
		}
	}
//-----------------------------------------------------	
	
	public function __construct($extendedPaginator = null)
	{		
		$this->_extendedPaginator = $extendedPaginator; 	
	}
		
	/**
	 * render the output of this object. It is called from __toString()
	 * @return partial view to be displayed.
	 */
	public function render(Zend_View_Interface $view = null)
	{
		//Zend_Debug::dump($this->getExtendedPaginator()->getPaginator()->getItemsByPage(1));
		//Zend_Debug::dump(array_keys($this->getExtendedPaginator()->getPaginator()->getItem(1)));
		//Zend_Debug::dump($this->getItemAttributes());
		//Zend_Debug::dump($this->getDisplayAttributes());
		
		if (null !== $view) {
			$this->setView($view);
		}

		$view = $this->getView();

		if ($this->_extendedPaginator->isPaginationDisabled()) {
			//Pagination is disabled, so we'll display a simplified table.
			return $view->partial($this->_viewTemplatePagianationDisabled, 'default',array('paginatorView'=> $this));
		}
		
		if (!$this->_extendedPaginator->isDataToDisplay()) {
			//there is no data to display, so we'll display
			return $view->partial($this->_viewTemplateEmptyData, 'default',array('paginatorView'=> $this));
			//return '';
		}

		//display default paginator (with pagination control)
		return $view->partial($this->_viewTemplate, 'default',array('paginatorView'=> $this));
	}
		
}
?>