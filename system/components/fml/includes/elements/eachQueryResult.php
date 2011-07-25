<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_eachQueryResult extends fml_element
{
	public function initiate()
	{
		//Loop through each child element and get the string representation of it.
		$raw='';
		foreach ($this->rawElement->children() as $child)
		{
			$raw.=$child->asXML();
		}
		$newXML=array();
		//Do some simple Sub FML parsing on it based on the query results from the parent query element.
		foreach ($this->up('query')->result as $result)
		{
			$fNr=array();
			foreach ($result as $key=>$val)
			{
				$val=addslashes($val);
				$val=htmlentities($val);
				$fNr['{'.$this->rawAttributes['as'].'.'.$key.'}']=$val;
			}
			$newXML[]=str_replace(array_keys($fNr),array_values($fNr),$raw);
		}
		/*
		 * Now construct a new DOM Document and import the raw string element into it
		 * 
		 * We start by destroying all children of $this->rawElement.
		 * Then we create a new DOM Document.
		 * Then we import $this->rawElement and append it onto the new DOM Document.
		 * Then we loop through each $newXML element and append it to the <eachQueryResult> element.
		 * Finally we import the generated DOM back to Simple XML and replace $this->rawElement with it.
		 */
		$this->destroyChildElements();
		$replacementDOM=new DOMDocument();
		$replacementDOM->appendChild($replacementDOM->importNode(dom_import_simplexml($this->rawElement),true));
		foreach ($newXML as $importMe)
		{
			$importMe=simplexml_load_string($importMe);
			$importMe=dom_import_simplexml($importMe);
			$importMe=$replacementDOM->importNode($importMe,true);
			$replacementDOM->firstChild->appendChild($importMe);
		}
		$this->rawElement=simplexml_import_dom($replacementDOM);
		return true;
	}
	
	public function parents()
	{
		$this->setParent('query');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('as',null,FML_DATATYPE_ID);
		return true;
	}
	
	public function template()
	{
		return '{CHILDREN}';
	}
}
?>