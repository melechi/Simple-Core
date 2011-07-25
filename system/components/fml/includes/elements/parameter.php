<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_parameter extends fml_element
{
	public function initiate()
	{
		
		return true;
	}
	
	public function parents()
	{
		$this->setParent('field');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('key',null,FML_DATATYPE_ANY);
		$this->setRequiredAttribute('value',null,FML_DATATYPE_ANY);
		return true;
	}
	
	public function template(){}
}
?>