<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_hidden extends fml_element_field_type_text
{
	public function initiate()
	{
		$this->parent->template='{FIELD}';
		$this->template=str_replace('maxlength="{MAXLENGTH}"','',$this->template);
		return true;
	}
}
?>