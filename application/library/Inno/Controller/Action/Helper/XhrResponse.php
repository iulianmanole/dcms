<?php


class Inno_Controller_Action_Helper_XhrResponse extends Zend_Controller_Action_Helper_Abstract
{

	/**
	 * Response Object. 
	 */	
		
	public function getResp($form, $db, $formDbMap, $dbClauses, $respAttr, $respElem, $formDataRaw)
	{
		// Phase 0 - Basic Checking 
		if (!is_array($formDataRaw)) {
			//throw new Exception('Error parsing form Data',0);
		}
		// Phase 1 - Prepare form data 
		$form = new $form(); 
		
		//Init respElem to '' in order to be removed from the formDataRaw, 
		//or else it will cause a crush to the form update
		$formDataRaw["$respElem"] = '';
		
		/** 
		 * remove all form elements that aren't set
		 * @todo keep in form data only the required formelements that are specified in $formDbMap  
		 */
		foreach ($formDataRaw as $key => $value) {
			if ($value !== ''){
				$formData[$key] = $value;
			}
		}

		/**
		 * In order to populate the returned element, we need some input from the user 
		 * If all the required values are not set, we'll return a blank element
		 */
		$populateElem = TRUE;
		if ((is_array($formData)) and ($form->isValidPartial($formData))) {  
			
			/** retrieve formDbMap values from the form */
			foreach ( $formDbMap as $formKey=>$dbKey) {
				if (array_key_exists($formKey, $formData)) {
					/** Extend dbClauses with the values from the form */
					$dbClauses[$dbKey] = $formData[$formKey];  
				}
				else {
					$populateElem = FALSE; 
					break;  
				}
			}
		}
		
		// Init element 
		$elem = $form->getElement($respElem);
		
		//Init elemValues
		$elemValues = array();
		
		/** get element data if appropriate */
		if ($populateElem) {	
			$model 	= new $db();
			//Zend_Debug::dump($dbClauses); 
		 	$elemValues = $model->findByKey($dbClauses, $respAttr['keyAttr'], $respAttr['valueAttr']);
		 	
		 	$elem->addMultiOptions($elemValues);
		}

		/** Remove default Decorators: HtmlTag and Label because they alter the view.*/ 
		$elem->removeDecorator('HtmlTag');
		$elem->removeDecorator('Label');
		
		return $elem->__toString(); 
	}

}



?>