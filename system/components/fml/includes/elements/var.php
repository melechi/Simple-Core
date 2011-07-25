<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_var extends fml_element
{
	public $template=false;
	public $templateContents=false;
	
	public function initiate()
	{
		if (!isset($this->rawAttributes['key']))
		{
			$this->instance->error(0,0,'Parse error. Element <var> is missing required attribute "key".');
		}
		else
		{
			$this->instance->parent->setVar((string)$this->rawAttributes['key'],(string)$this->rawElement);
		}
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
		$this->setParent('include');
		$this->setParent('eachQueryResult');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('key',null,FML_DATATYPE_ANY);
		return true;
	}
	
	public function template()
	{
		return $this->templateContents;
	}
}
?>