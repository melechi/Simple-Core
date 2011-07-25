<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class component_ext extends component
{
	const EXCEPTION_NAME='Ext Component Exception';
	
	public function initiate()
	{
		$this	->branch('json')
				->branch('direct');
//		$this->branch('grid');
		return true;
	}
	
	public function success()
	{
		$this->json->success=1;
		return $this;
	}
	
	public function failure()
	{
		$this->json->success=0;
		return $this;
	}
}
?>