<?php
/** Include Zend_Db_Table_Abstract */
require_once 'Zend/Db/Table/Abstract.php';

class dcms_HwProduct extends Inno_Db_Table_Abstract implements Inno_Db_Table_BaseFormInterface
{
	protected $_name = "dcms.hw_product";
	
	protected $_dependentTables = array ('Equipments'); 
	
	/**
	 * 
	 * @param array $manufacturerId
	 * @return associative array
	 */
	public function getProducts(array $manufacturersIds)
	{
		
		$listOfMfctrs = implode(',', $manufacturersIds);
		
		return $this->getAdapter()->fetchpairs("select id, name from hw_product where manufacturer_id in (".  $listOfMfctrs . " )");
		
		
	}
	
	/** 
	 * get a list of products, that is filtered based on manufacturer and HwType.
	 * both Manufacturer and Hardware Type can be null, and will not be considered.
	 * 
	 * @param $mfctr - manufacturer id
	 * @param $hwType - hardware type id
	 * @return associative array ('id' => 'hw product name')
	 */
	public function getHwProducts($mfctr, $hwType)
	{
		$sql = $this->getAdapter()->select();
		$sql->from(array('hwProd' => $this->info('name')));
		
		if ($mfctr !== null) {
			$sql->where('manufacturer_id = ?',(int)$mfctr);
		} 
		if ($hwType !== null){
			$sql->where('type_id = ?', (int)$hwType);
		}
		
		//Zend_Debug::dump($sql->__toString(), 'SQL getHwProducts');
		return $this->getAdapter()->fetchPairs($sql);
	}
	
	/**
	 * Save a model data.
	 * Data array must contain key => value pairs.
	 * "Key" must be a valid table attribute.
	 *
	 * @return inserted row id or null.
	 */
	public function saveModel(array $data)
	{
		try {
			$result = $this->insert($data);
		}
		catch (Exception $e) {
			//TODO add message to log in case of error
			//throw new Exception('Error when inserting Data:'.$e->getMessage());
			return null; 
		}
		//TODO add message to log in case of success
		return $result;

	}
	/**
	 * Select all models to be displayed
	 * @todo add $where parameter.
	 * @return Zend_Db_Select  
	 */
	public function selectProducts()
	{
		$select = new Zend_Db_Select($this->getAdapter()); 
		
		$select->from(array('prodHw'=>$this->_name),array('refPk'=>'id','Name'=>'name'))
				->join(	array('mf'=>'manufacturers'),
						'prodHw.manufacturer = mf.id', 
						array('Manufacturer'=>'name')		
					   )
				->join( array('tp'=>'types_hw'), 
						'prodHw.type = tp.id',
						array('Type' => 'type'))	   
					   ;
		
		return $select; 
	}
	
	/**
	 *  Inno_Db_Table_BaseFormInterface selectElements() implementation
	 */
	public function selectElements(array $criteria = null)
	{
		return $this->selectProducts();
	}	
}
?>