<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_hbox extends fml_element_container
{
	public function parents()
	{
		
		return true;
	}
	
	public function attributes()
	{
		
		return true;
	}
	
	public function template()
	{
		return <<<TEMPLATE
<div id="{ID}" class="{CLASS}" style="clear:both;{STYLE}">
	{CHILDREN}
</div>
TEMPLATE;
	}
}
?>