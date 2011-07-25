<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_checkbox extends fml_element_field_type_radio
{
	public function initiate()
	{
		parent::initiate();
		$this->parent->rawAttributes['name'].='[]';
		return true;
	}
}
?>