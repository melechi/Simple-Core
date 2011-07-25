<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_include extends fml_element
{
	public function initiate()
	{
		$file=$this->instance->scope->my->dir.'forms'._.$this->rawAttributes['file'];
		$fml=new DOMDocument();
		$fml->loadXML($this->instance->parent->loadCachedFile($file));
		$xpath=new DOMXPath($fml);
		
		$include=new DOMDocument();
		$include->appendChild($include->importNode(dom_import_simplexml($this->rawElement),true));
		$includeXpath=new DOMXPath($include);
		
		if (!count($this->rawElement->children()))
		{
			//$this->destroyChildElements();
			$result=$xpath->query('fml/form');
			//If we have a number of results, then its a normal fml document that we're inserting.
			if ($result->length)
			{
				for ($i=0; $i<$result->item(0)->childNodes->length; $i++)
				{
					$include->firstChild->appendChild($include->importNode($result->item(0)->childNodes->item($i),true));
				}
			}
			//If we have no results, then it must be a specal fml document that's been specially setup for only inserting.
			else 
			{
				$result=$xpath->query('/fml');
				for ($i=0; $i<$result->item(0)->childNodes->length; $i++)
				{
					$include->firstChild->appendChild($include->importNode($result->item(0)->childNodes->item($i),true));
				}
			}
		}
		else
		{
			foreach ($this->rawElement->children() as $get)
			{
				if (!isset($get['id']))
				{
					$this->instance->error(0,0,'Parse error. Element <get> is missing required attribute "id".');
				}
				else
				{
					$result=$xpath->query('/fml//*[@id="'.$get['id'].'"]');
					if (!$result->length)
					{
						$this->instance->error(0,0,'Parse Error. Attempt to include element by id "'.$get['id'].'" from file "'.$file.'" failed.'
													.' The element could not be found.');
					}
					else
					{
						//Remove the get element.
						$removeResult=$includeXpath->query('//get[@id="'.$get['id'].'"]');
						$include->firstChild->removeChild($removeResult->item(0));
						//Append the new element.
						$include->firstChild->appendChild($include->importNode($result->item(0),true));
					}
				}
			}
		}
		//Override the raw element with the new one.
		$this->rawElement=simplexml_import_dom($include);
		return true;
	}
	
	public function parents()
	{
		$this->setParent('form');
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		$this->setParent('field');
		$this->setParent('rule');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		$this->setParent('eachQueryResult');
		return true;
	}
	
	public function attributes()
	{
		$this->setAttribute('file',null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		return '{CHILDREN}';
	}
}
?>