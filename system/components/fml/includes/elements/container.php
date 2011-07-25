<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class fml_element_container extends fml_element
{
	public function initiate()
	{
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		$this->setAttribute('class',	null,FML_DATATYPE_STRING);
		$this->setAttribute('style',	null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		return <<<TEMPLATE
<div id="{ID}" class="{CLASS}" style="{STYLE}">
	{CHILDREN}
</div>
TEMPLATE;
	}
}
?>