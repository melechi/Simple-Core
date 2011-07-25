<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_fieldset extends fml_element_container
{
	public function parents()
	{
		$this->setParent('form');
		$this->setParent('conditional');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		$this->setParent('eachQueryResult');
		$this->setParent('fieldset');
		$this->setParent('include');
		return true;
	}
	
	public function attributes()
	{
		$this->setAttribute('legend',null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		if (!empty($this->rawAttributes['legend']))
		{
			$legend='<legend>{LEGEND}</legend>';
		}
		else
		{
			$legend='';
		}
		return <<<TEMPLATE
<fieldset id="{ID}" class="{CLASS}" style="{STYLE}">
	{$legend}
	{CHILDREN}
</fieldset>
TEMPLATE;
	}
}
?>