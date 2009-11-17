<?php
/**
 * 
 * Base class to be used for Organizers 
 * 
 * @author Iulian
 *
 */
abstract class Inno_Organizer extends Inno_Model_Abstract 
{
	
	const ERROR_DELETE_ORGANIZER = 101;
	
	protected $_allowed = array (
		'id',
		'name',
		'lft',
		'rgt'
	);

	/**
	 * 
	 * @return Inno_Organizer_Gateway
	 */
	public function getGateway()
	{
		$gw = parent::getGateway();
		
		return $gw;
	}
	
	/**
	 * Constructor 
	 * @param integer $id   - Organizer Id, that will be used to pull data from DB
	 * @param string  $name - The name that will be used to create a new Organizer.  
	 */ 
	public function __construct(array $data, array $options = array())
	{	
		parent::__construct($data, $options);
		
		
		/** if id is set and != Null, we will pull data from database */
		if (($this->id != NULL)) {
			/** Prepare other attributes of this object.*/
			$attrs 		 = $this->getGateway()->fetchAttrs($this->id, array ('lft', 'rgt', 'name') );
			$this->lft  = $attrs['lft'];
			$this->rgt  = $attrs['rgt'];
			$this->name = $attrs['name'];
		}

	}
	
	public function getId()
	{
		return $this->id; 
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function getLft()
	{
		return $this->lft; 
	}
	
	public function getRgt()
	{
		return $this->rgt;
	}
	
	public function fetchPath()
	{
		return $this->getGateway()->fetchPath($this); 
	}
	
	/**
	 * Get all the childs of this organizer.
	 */
	public function fetchChilds()
	{
		/** return organizer childs from the database.*/
		return $this->getGateway()->fetchChilds($this);
	}
	
	/** 
	 * @deprecated Use instead OrganizerGateway->fetchOrganizersByPath()
	 * Fetch all organizers path as pairs (id => path/to/organizer)
	 * 
	 * @return array 
	 */
	public function fetchOrgaizersByPath()
	{
		trigger_error("this function is deprecated. Use instead OrganizerGateway->fetchOrganizersByPath()");
		$rowset = $this->getGateway()->fetchOrganizersByPath();
		foreach ($rowset as $row){
			$result[$row['id']] = $row['path'];
		}
		return $result;
	}
	
	/**
	 * Add a new Organizer under this Organizer
	 */
	public function manage_addChild(Inno_Organizer $child)
	{ 
		return $this->getGateway()->addChild($this, $child);
	}
	
	/**
	 * Delete a Organizer and all his childs. 
	 * It updates the tree lft and rgt values. 
	 * @return ERROR_DELETE_ORGANIZER | true 
	 */
	public function manage_deleteOrganizer()
	{	
		$result = $this->getGateway()->deleteOrganizer($this);
		
		if (!$result) {
			return self::ERROR_DELETE_ORGANIZER;
		}
		return true; 
	}
}

?>