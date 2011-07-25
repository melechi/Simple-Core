<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_control extends fml_element
{
	public function parents()
	{
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		return true;
	}
	
	public function attributes()
	{
//		$this->setRequiredAttribute('type','text',	FML_DATATYPE_ENUM,$this->fieldTypes);
		$this->setAttribute('label',	'&nbsp;',	FML_DATATYPE_STRING);
//		$this->setAttribute('prefix',	null,		FML_DATATYPE_STRING);
//		$this->setAttribute('suffix',	null,		FML_DATATYPE_STRING);
		$this->setAttribute('class',	null,		FML_DATATYPE_STRING);
		$this->setAttribute('style',	null,		FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		return <<<TEMPLATE
<ul>
	<li class="col_fullspan"><input type="submit" id="{ID}" value="{LABEL}" /></li>
</ul>
TEMPLATE;
	}
}
?>