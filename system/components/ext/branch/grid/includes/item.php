<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid_toolbar_item extends ext_grid_getSetOptions
{
	private $type=false;
	
	public function __construct($type=null)
	{
		$this->type=$type;
		return true;
	}
	
	public function toJson()
	{
		return array
		(
			'type'=>$this->type,
			'options'=>$this->getOptions()
		);
	}
}
?>