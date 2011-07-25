<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class component_dbtools extends component
{
	const EXCEPTION_NAME='DB Tools Exception';
	
	public function initiate()
	{
		$this	->branch('activeRecord');
//				->branch('direct');
////		$this->branch('grid');
		return true;
	}
	
	
}
?>