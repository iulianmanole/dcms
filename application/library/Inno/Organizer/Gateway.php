<?php
/**
 * Basic class to provide logic Inno_Organizer domain model access. 
 * 
 *
 */
abstract class Inno_Organizer_Gateway extends Inno_Model_Gateway
{
	public function __construct ($config = array())
	{
		parent::__construct($config);
		
		//@TODO perform validation for lft, rgt, id primary table attributes. 
		//		Used table must have this attributes set.
	}
	

	/**
	 * Get attributes specified by $attrs for the given ID
	 * @return assoc array (attr=>val)
	 */
	public function fetchAttrs($id, array $attrs)
	{
		
		$select = $this->getPrimaryTable()->select()
					->setIntegrityCheck(false)
					->from($this->getPrimaryTable()->info('name'), $attrs)
					->where('id = ?', $id)
		;
			
		$stmt 	= $this->getPrimaryTable()->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();
		return $rowset[0];
	}
	
	/**
	 * Return a rowset that describe the path ( id => name)
	 * It has all references for the parent nodes to the root. 	
	 * 
	 * @param Inno_Organizer $organizer
	 * @return 	rowset order from root to organizer. (including organizer)
	 * 			The rowset will contain {parentName,parentId}
	 */
	public function fetchPath(Inno_Organizer $organizer)
	{
		/**
		 *  SELECT parent.id, parent.name
		 * 	FROM  equipment_class AS node,
		 * 		  equipment_class AS parent
		 * 	WHERE node.lft BETWEEN parent.lft AND parent.rgt
		 * 	AND   node.id = '10'
		 *  ORDER BY parent.lft;
		 *
		 * How it works :
		 *		1. We obtain all the parents of the given $organizer.
		 * 		 	The organizer is the node in this select
		 * 		2. We order the parents ascending by parent.lft attribute.
		 */		
		$select = $this->getPrimaryTable()->select()->setIntegrityCheck(false)
			->from(array('node'=>$this->getPrimaryTable()->info('name')),
					array(/*'nodeName'=>'node.name', 'nodeId'=>'node.id'*/))
			->join(array('parent'=>$this->getPrimaryTable()->info('name')),
					'node.lft between parent.lft and parent.rgt',
					array('name'=>'parent.name', 'id'=>'parent.id'))
			->where('node.id = ?', $organizer->getId())
			//->where('node.id != parent.id')
			->order('parent.lft ASC')
		;

		$stmt 	= $this->getPrimaryTable()->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();
		
		return $rowset;	
	}
	/**
	 * Fetch one organizer / all organizers paths as 
	 * pairs (id => path/to/organizer)
	 *
	 * @param null|integer (organizer ID)|Inno_Organizer $organizer
	 * @return array ( id => path/to/organizer)
	 */
	public function fetchOrganizersByPath($organizer = null)
	{
		$select = $this->selectOrganizersByPath($organizer);
		
		$stmt 	= $this->getPrimaryTable()->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();
		
		foreach ($rowset as $row){
			$result[$row['id']] = $row['path'];
		}
		
		return $result; 
	}
	
	
	
	
	/**
	 * Select to obtain all Subtree Childs (.
	 * It is used for pagination (refPk is enabled) 
	 * 
	 *@return  Zend_Db_Select $select statement 
	 */
	public function selectSubtreeChilds(Inno_Organizer $organizer)
	{
		$select = $this->_selectSubtreeChilds($organizer);
		$select->columns(array('refPk' => 'id'));
			
		return $select; 	
	}

	/**
	 * Fetch all subtree elements of a given Organizer.
	 * @param Inno_Organizer $organizer 
	 * @return childs from all levels, that are conected directly and 
	 * 	indirectly to the organizer 
	 */
	public function fetchSubtreeChilds (Inno_Organizer $organizer)
	{
		$select = $this->_selectSubtreeChilds($organizer);
			
		$stmt 	= $this->getPrimaryTable()->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();

		return $rowset;
	}
	
	/**
	 * The select that is used to retrieve all the direct childs of an organizer
	 * if returnLeafs and returnSubOrganizers are false => default values will be used. 
	 *
	 * @param Inno_Organizer $organizer
	 * @param boolean $returnLeafs 
	 * @param boolean $returnSubOrganizers 
	 * @return Zend_Db_Select $select statement  
	 */
	public function selectChilds(Inno_Organizer $organizer, $returnLeafs = true, $returnSubOrganizers = true)
	{	
		//select all both leafs and subOrganizers
		/**
		 * SELECT node.name, parent.name, (COUNT(parent.name) -1) as depth
		 * FROM equipment_class as node,
		 * 		equipment_class as parent
		 * WHERE node.lft between parent.lft and parent.rgt
		 * 		 and parent.lft >= '2' and parent.rgt <= '15'   --> here must be Lft and rgt attributes of the given node ID.
		 * GROUP BY node.name
		 * HAVING depth =1
		 * ;
		 *
		 * How it works:
		 * 		1. we obtain for the depth of every child in the subtree that has ID node as root.
		 * 		2. we keep only those nodes that have depth 1 ( that means that they
		 * 				are connected directly to the given $organizer.
		 * 		3. the result will contain all the nodes
		 */		
		$select = $this->getPrimaryTable()->select()->setIntegrityCheck(false)
				->from(array('node'=>$this->getPrimaryTable()->info('name')),
						array('refPk'=>'node.id', 'nodeName'=>'node.name','nodeId'=>'node.id','nodeDepth'=>'COUNT(parent.id) -1'))
				->join(array('parent'=>$this->getPrimaryTable()->info('name')),'node.lft between parent.lft and parent.rgt',
						array('parentName'=>'parent.name'))
				->where('parent.lft >= ?', $organizer->getLft())
				->where ('parent.rgt <= ?', $organizer->getRgt());
		if ( !($returnLeafs && $returnSubOrganizers) ) {
			if ($returnLeafs) {
				//restrict select to return only Leafs
				$select->where('node.lft = node.rgt - 1');
			}
			elseif ($returnSubOrganizers) {
				//restrict select to return only SubOrganizers.
				$select->where('node.lft < node.rgt -1');
			}
			else {
				// both $returnLeafs and $returnSubOrganizers are false, so we'll return an empty select
				$select->where('node.lft = node.rgt'); 
			}
			
		}
		//echo "Inno_Organizer_Model:140:<br>".$select->__toString().'<br/><br/><br/>';
		
		$select	->order('node.name')
			   	->group(array('node.id'))
		  		->having('nodeDepth = 1');

		 //Zend_Debug::dump($select->__toString()); 		
		return $select; 
	}
	
	/**
	 * Fetch all direct childs of a given $organizer
	 *
	 * @return $rowset that contain all the childs of $organizer
	 * 			whith specific details { name,depth, parent,..}
	 */
	public function fetchChilds(Inno_Organizer $organizer)
	{

		/**$select = $this->getPrimaryTable()->select()->setIntegrityCheck(false)
		->from(array('node'=>'dcms.locations'),
		array('nodeName'=>'node.name','nodeId'=>'node.id','nodeDepth'=>'COUNT(parent.id) -1'))
		->join(	array('parent'=>'dcms.locations'),
				'node.lft between parent.lft and parent.rgt',
				array())
		->where('parent.lft >= ?', $organizer->getLft())
		->where ('parent.rgt <= ?', $organizer->getRgt())
		->order('node.name')
		->group(array('node.id'))
		->having('nodeDepth = 1')	
		;*/
		$select = $this->selectChilds($organizer);

		$stmt 	= $this->getAdapter()->query($select);
		$rowset = $stmt->fetchAll();

		return $rowset;
	}
 
	/**
	 * add a child to a given parent, with respect to the tree structure.
	 * The child will not have the ID set, because will be determined at insert time.
	 * The parent must be a valid Organizer stored in the database. 
	 * 
	 * @TODO add as a comment the SQL script.
	 *  
	 * @return boolean succes / failure  
	 */
	public function addChild(Inno_Organizer $parent, Inno_Organizer $child)
	{
		//Test that the parent organizer is already present in storage backend.
		if ($parent->id === NULL) {
			throw new Inno_Organizer_Exception('Parent Organizer is not yet saved to storage backend.'.
										'Cannot add child to a dummy parent Organizer');
		}
		
		$this->getPrimaryTable()->getAdapter()->beginTransaction();

		try {
			$rgt = $parent->getRgt();
				
			print_r("right:".$rgt."|");
			/**
			 * update lft and rgt values for all the nodes that are after parent node
			 * in hierarchy
			 */
			$val = new Zend_Db_Expr("rgt + 2");
			$this->getPrimaryTable()->update(array('rgt'=> $val),"rgt >= $rgt");
				
			$val = new Zend_Db_Expr("lft + 2");
			$this->getPrimaryTable()->update(array('lft'=> $val),"lft > $rgt");

			/**
			 * Insert the child.
			 */
			$insChild = array(	'name' => $child->getName(),
								'lft'  => $rgt , 
								'rgt'  => $rgt + 1);
				
			$a = $this->getPrimaryTable()->insert($insChild);
				
				
			/** Everything is OK. We commit the transaction */
			$this->getPrimaryTable()->getAdapter()->commit();
			
			print_r ("last insert: ".$a);
			return true; 
				
		} catch (Exception $e) {
				
			$this->getPrimaryTable()->getAdapter()->rollBack();
			print_r('ERROR - Transaction Rollback. Error message : '.$e->getMessage());
			return false; 
		}
	}

	/**
	 * Deletes the organizer and all his childs.
	 * 
	 * @param Inno_Organizer $organizer
	 * @return int numbers of organizers deleted or false.
	 */
	public function deleteOrganizer(Inno_Organizer $organizer)
	{
		$rgt 	= $organizer->getRgt();
		$lft 	= $organizer->getLft();
		$width 	= $rgt - $lft + 1;
			
		$this->getPrimaryTable()->getAdapter()->beginTransaction();
		try {

			/**
			 *  delete the node and all his childrens  
			 */
			$result = $this->getPrimaryTable()->delete("lft BETWEEN $lft and $rgt");

			/**
			 * Perform lft and rgt decrement for all the nodes that are after our node. 
			 * The decrement step ($width) is variable, 
			 * 	depends on the node's number of childs
			 */
			$val = new Zend_Db_Expr("rgt - $width");
			$this->getPrimaryTable()->update(array('rgt'=> $val),"rgt > $rgt");
				
			$val = new Zend_Db_Expr("lft - $width");
			$this->getPrimaryTable()->update(array('lft'=> $val),"lft > $rgt");

			/**
			 * if everything is ok, commit the transaction 
			 */
			$this->getPrimaryTable()->getAdapter()->commit();
		} catch (Exception $e) {
				
			$this->getPrimaryTable()->getAdapter()->rollBack();
			print_r('ERROR - Transaction Rollback. Error message : '.$e->getMessage());
			return FALSE; 
		}
		return $result;
	}
	
	
	/** 
	 * select all subtree childs of an organizer 
	 * 
	 * @param Inno_Organizer $organizer
	 * @return Zend_Db_Select $select
	 */
	protected function _selectSubtreeChilds(Inno_Organizer $organizer)
	{
		$select = $this->getPrimaryTable()->select()
			->setIntegrityCheck(false)
			->from(array('node'=>$this->getPrimaryTable()->info('name')),
					array('nodeName'=>'node.name','nodeId'=>'node.id'))
			->where('node.lft > ?', $organizer->getLft())
			->where ('node.rgt < ?', $organizer->getRgt());
			
		return $select;	
	}
	
	/**
	 * Select organizers by full path from the root. (id => path/to/organizer) 
	 * The path will include the referenced node.
	 * 
	 * This method implements the following select: 
	 * 		SELECT `node`.`id`, `node`.`name`, GROUP_CONCAT(`parent`.`name` ORDER BY `parent`.`id` SEPARATOR ' / ') 
	 * 		FROM `locations` AS `node`
	 * 		INNER JOIN `locations` AS `parent` ON node.lft between parent.lft and parent.rgt  
	 * 		GROUP BY `node`.`name` 
	 * 		ORDER BY `node`.`id` ASC
	 * GROUP_CONCAT requires group by clause.
	 * 
	 * @TODO Will accept organizer Object or Organizer Identifier (ID)
	 * 
	 * @param  integer | Inno_Organizer $organizer - used to restrict the select. 
	 * 			By default this select will return all organizers. 
	 * @return Zend_Db_Select
	 *  
	 */
	public function selectOrganizersByPath($organizer = null)
	{		
		$select = $this->getPrimaryTable()->select()->setIntegrityCheck(false)
			->from(	array('node'=>$this->getPrimaryTable()->info('name')),
					array(	'name'	=> 'node.name', 
							'id'	=> 'node.id',
							'path'=> new Zend_Db_Expr("GROUP_CONCAT(`parent`.`name` ORDER BY `parent`.`id` SEPARATOR '/')") 
						)
				   )
			->join(array('parent'=>$this->getPrimaryTable()->info('name')),
					'node.lft between parent.lft and parent.rgt',
					array())
			->group('node.name')
			->order('node.id ASC');

		//obtain the id that will be used in where clause
		$id = null; 
		if (is_int($organizer)) {	
			$id = $organizer;
		}elseif ($organizer instanceof Inno_Organizer) {
			$id = $organizer->getId();
		}		
		if ($id) {
			$select->where('node.id = ?',$id);	
		}
		
		return $select;	
	}
	
}  
?> 