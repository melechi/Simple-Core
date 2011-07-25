<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_elseif extends fml_element_if
{
	public function initiate()
	{
		$if=reset($this->up()->down('if'));
		if ($if->result===true)
		{
			$this->destroyChildElements();
		}
		else
		{
			parent::initiate();
		}
		return true;
	}
}
?>