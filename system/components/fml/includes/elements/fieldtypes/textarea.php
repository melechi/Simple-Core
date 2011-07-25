<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_textarea extends fml_element_field_type
{
	public $template='<textarea id="{ID}" name="{NAME}" class="{CLASS}" rows="{ROWS}" cols="{COLS}" style="{STYLE}"{READONLY}{DISABLED}>{VALUE}</textarea>';

	public function initiate()
	{
		//Set valid attributes.
		$this->parent->setAttribute('rows',null,FML_DATATYPE_INT);
		$this->parent->setAttribute('cols',null,FML_DATATYPE_INT);
		return true;
	}
}
?>
