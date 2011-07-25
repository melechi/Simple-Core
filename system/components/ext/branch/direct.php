<?php
/*
 * Simple Core 2.1.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_direct extends branch
{
	public $xIncludeFolder='direct';
	
	public function initiate()
	{
		$this->xInclude('server');
	}
	
	public function initServer(application $application,$scope,$serverDefPath='',$namespace='Ext.app')
	{
		return new ext_direct_server($this,$application,$scope,$serverDefPath,$namespace);
	}
}
?>