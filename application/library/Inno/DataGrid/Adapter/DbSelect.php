<?php
/**
 * Thist Class it is used to overwrite the behaviour of Zend_Paginator_Adapter_Db_Select::count() function, 
 * because in 1.7 it was not possible to count all the total number of rows for the  
 * 
 */
/**
 * @see Zend_Paginator_Adapter_DbSelect
 */
require_once 'Zend/Paginator/Adapter/DbSelect.php';

class Inno_DataGrid_Adapter_DbSelect extends Zend_Paginator_Adapter_DbSelect
{
    public function count()
    {
        if ($this->_rowCount === null) {
            //$rowCount = clone $this->_select; 
        	$rowCount = new Zend_Db_Select($this->_select->getAdapter());
            /**
             * The DISTINCT and GROUP BY queries only work when selecting one column.
             * The question is whether any RDBMS supports DISTINCT for multiple columns, without workarounds.
             */
            if (true === $rowCount->getPart(Zend_Db_Select::DISTINCT)) {
                $columnParts = $rowCount->getPart(Zend_Db_Select::COLUMNS);

                $columns = array();

                foreach ($columnParts as $part) {
                    if ($part[1] == '*' || $part[1] instanceof Zend_Db_Expr) {
                        $columns[] = $part[1];
                    } else {
                        $columns[] = $rowCount->getAdapter()->quoteIdentifier($part[1], true);
                    }
                }

                if (count($columns) == 1 && $columns[0] == '*') {
                    $groupPart = null;
                } else {
                    $groupPart = implode(',', $columns);
                }
            } else {
                $groupParts = $rowCount->getPart(Zend_Db_Select::GROUP);

                foreach ($groupParts as &$part) {
                    if (!($part == '*' || $part instanceof Zend_Db_Expr)) {
                        $part = $rowCount->getAdapter()->quoteIdentifier($part, true);
                    }
                }

                $groupPart = implode(',', $groupParts);
            }

            $countPart  = empty($groupPart) ? 'COUNT(*)' : 'COUNT(DISTINCT ' . $groupPart . ')';
            $expression = new Zend_Db_Expr($countPart . ' AS ' . $rowCount->getAdapter()->quoteIdentifier(self::ROW_COUNT_COLUMN));

            $rowCount->__toString(); // Workaround for ZF-3719 and related

            /** pass paginator select as from expression, because we want to count all the rows */
            //echo "<br/>Inno_Paginator_Adapter_DbSelect:59:<br/>".new Zend_Db_Expr("(".$this->_select->__toString().")",''); 
            $rowCount->from(new Zend_Db_Expr("(".$this->_select->__toString().")",''));


            
			$rowCount->reset(Zend_Db_Select::COLUMNS)
					 ->columns($expression);
					 	 
			//echo "<br/>Inno_Paginator_Adapter_DbSelect:67:<br/>".$rowCount->__toString();
					
            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }
}
