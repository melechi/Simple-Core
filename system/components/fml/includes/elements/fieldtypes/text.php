<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_text extends fml_element_field_type
{
	public $template='<input id="{ID}" name="{NAME}" type="{TYPE}" class="{CLASS}" style="{STYLE}" value="{VALUE}" size="{SIZE}" maxlength="{MAXLENGTH}"{READONLY}{DISABLED} />';
	
	public function initiate()
	{
		//Set valid attributes.
		$this->parent->setAttribute('size',null,FML_DATATYPE_INT);
		$this->parent->setAttribute('maxlength',null,FML_DATATYPE_INT);
		return true;
	}
}
?>