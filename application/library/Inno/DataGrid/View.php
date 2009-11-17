<?php 
/**
 * Act as a presentation Layer for data that must be displayed. 
 * In this way we can apply presentation modification and customisation to paginated data. 
 * 
 * @TODO 
 * 	1. By default if no display attribute is set, one should display all itemAttributes.
 *  2. 
 * 
 * @author Iulian
 *
 */
class Inno_DataGrid_View
{
	/**
	 * Referenced DataGrid object 
	 * @var Inno_DataGrid 
	 */
	protected $_dataGrid; 

	/**
	 * List of available attributes for paginator items 
	 * 
	 * @var array
	 */
	protected $_itemAttributes;
	
	/**
	 * List of attributes that will be rendered by the DataGrid  
	 * Every display attribute can be formed using one, or multiple item attributes.
	 * 
	 * @var array ( name => value)
	 */
	protected $_displayAttributes;
	
	/**
	 * Links that are attached to certain displayAttribute.
	 * params are used to construct data dependent link. 
	 * example: an link that displays the details of a certain equipment will require an id param. 
	 * 		In this case we will set params = array('id' => 'equipment_id')
	 * 		When dataGrid will be rendered, equipment_id will will be replaced with 
	 * 		 the actual value, in order to create the link correctly.
	 * 
	 * @var array ('name' => array( 'action', 'controller', 'module', 'params(array)')
	 * 
	 */
	protected $_detailLinks; 
	
	/**
	 * Paginator title, that can be used in template.
	 */
	protected $_title = '...title is unset';
	
	/** 
	 * MVC View object 
	 * @var Zend_View_Interface
	 */
	protected $_view; 
	
	/**
	 * the view templates that will be used
	 * 
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
     * @return Inno_DataGrid
     */
    public function getDataGrid() 
    {
    	return $this->_dataGrid;
    }
    
    /**
     * get Item attributes
     * If no attributes exist, the function will return an empty array.
     * @return array
     */
    public function getItemAttributes()
    {
    	if (null === $this->_itemAttributes) {
    		$this->setItemAttributes();
    	}
    	//Zend_Debug::dump($this->_itemAttributes);
    	return $this->_itemAttributes; 
    }
    
    /**
     * Set Item Attributes from DataGrid.
     * 
     * @return $this for fluent interface 
     */
    public function setItemAttributes()
    {
    	if  ($this->getDataGrid()->isDataToDisplay()) {
    		$this->_itemAttributes = array_keys($this->getDataGrid()->getPaginator()->getItem(0));
    	}
    	else {
    		$this->_itemAttributes = array();
    	}
    	return $this; 
    }
	 
    /**
     * Bound one display attribute to one or more item attributes. 
     * One display attribute can aggregate more item attributes 
     * Use Case: name and surname can be showed together as one display attribute.
     *  
     * @param $name - display attribute name
     * @param $values - array of valid item attributes.
     *
     * @return $this for fluent interface 
     */
    public function setDisplayAttribute($name,array $values)
    {
    	if ($this->getDataGrid()->isDataToDisplay()) {
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
    
    /**
     * Get all the display attributes 
     * 
     * @return array
     */
    public function getDisplayAttributes()
    {
    	return $this->_displayAttributes; 
    }
    
	/**
	 *  get a DetailLink by name. 
	 *  @param string name  
	 */
	public function getDetailLink($name)
	{
		if (isset($this->_detailLinks[$name])) {
			return $this->_detailLinks[$name];
		}
		
		return null; 
	}

	/**
	 * return DetailLink[$name] as a string "/module/controller/action"
	 * 								or 	"/controller/action"
	 * 								or 	"/action"
	 *
	 * @return string
	 */
	public function getDetailLinkAsString($name)
	{
		if (isset($this->_detailLinks[$name])) {
			$module = $this->_detailLinks[$name]['module'] !== null ? $this->_detailLinks[$name]['module'].'/' : '';
			$ctrl 	= $this->_detailLinks[$name]['controller'] !== null ? $this->_detailLinks[$name]['controller'].'/' : '';
			$action = $this->_detailLinks[$name]['action'] !== null ? $this->_detailLinks[$name]['action'] : '';

			$str = '/'.$module.$ctrl.$action;
			
			//Zend_Debug::dump($str);
			return $str;
		}
		
		return null; 
	}
	
	/**
	 * set a detail link. 
	 * Performs validation for parameters. Any parameter that fails validation will not be added.
	 * @param $name - The name of the display Attribute to which we will atach the link. 
	 * 					It is case sensitive and white space sensitive. 
	 * @param $action
	 * @param $controller
	 * @param $module
	 * @param $params array key => item attribute that will be used to retrieve values.
	 * 				 	params are used to correctly construct detail link for every item attribute.  
	 * 
	 * Example: we want a link that will end with an parameter /path/to/action/id/<id value>.
	 * 				 
	 * @return Inno_Paginator_Extended_View ($this)
	 */
	public function setDetailLink($name, $action, $controller=null, $module=null, $params = array())
	{
		if (!$this->getDataGrid()->isDataToDisplay()) {
			//no data to display, we return reference to allow for chaining.
			return $this;
		}
		
		$this->_detailLinks[$name] = array(	'action' 	=> $action,
											'controller'=> $controller, 
											'module'	=> $module,
										);
		
		// Add Parameters to the detail link, with validation
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
			$this->_detailLinks[$name]['params'] = $validParams;
		}
		return $this;
	}
    
	/**
	 * @needs to be revised and validated 
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

//-------- end of getters and setters -----
	
	public function __construct($dataGrid = null)
	{		
		$this->_dataGrid = $dataGrid; 	
	}
		
	/**
	 * render the output of this object. It is called from __toString()
	 * @return partial view to be displayed.
	 */
	public function render(Zend_View_Interface $view = null)
	{
		//Zend_Debug::dump($this->getDataGrid()->getPaginator()->getItemsByPage(1));
		//Zend_Debug::dump(array_keys($this->getDataGrid()->getPaginator()->getItem(1)));
		//Zend_Debug::dump($this->getItemAttributes());
		//Zend_Debug::dump($this->getDisplayAttributes());
		
		if (null !== $view) {
			$this->setView($view);
		}

		$view = $this->getView();

		if ($this->getDataGrid()->isPaginationDisabled()) {
			//Pagination is disabled, so we'll display a simplified table.
			return $view->partial($this->_viewTemplatePagianationDisabled, 'default',array('dataGridView'=> $this));
		}
		
		if (!$this->getDataGrid()->isDataToDisplay()) {
			//there is no data to display, so we'll display
			return $view->partial($this->_viewTemplateEmptyData, 'default',array('dataGridView'=> $this));
		}

		//display default paginator (with pagination control)
		return $view->partial($this->_viewTemplate, 'default',array('dataGridView'=> $this));
	}
		
}
?>