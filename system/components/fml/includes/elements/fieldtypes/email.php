<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_email extends fml_element_field_type_text
{
	public function initiate()
	{
		parent::initiate();
		if ($this->parent->instance->mode==FML_MODE_VALIDATE)
		{
//			if (!preg_match('/^\w+([\.-\+]?\w+)*@(\w+([\.-]?\w+)*(\.\w{2,4})+)$/i',$this->parent->rawAttributes['value']))
//			{
//				$this->parent->instance->valid=false;
//				$this->template='<label class="error"><div class="error message">Invalid Email Address.</div>'.$this->originalTemplate.'</label>';
//			}
		}
		return true;
	}
}
?>