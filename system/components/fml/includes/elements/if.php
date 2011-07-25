<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_if extends fml_element_condition
{
	public function attributes()
	{
		$this->setRequiredAttribute('condition',null,FML_DATATYPE_STRING);
		return true;
	}
}
?>