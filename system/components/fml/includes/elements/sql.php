<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_sql extends fml_element
{
	public function initiate()
	{
		$this->instance->registerQuery($this->id(),(string)$this->rawElement);
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
		$this->setParent('field');
		return true;
	}
	
	public function attributes()
	{
		return true;
	}
	
	public function template(){}
}
?>