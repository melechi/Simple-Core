<?php
class application_sandbox_info
{
	public $name		='Sandbox';
	public $description	='A simple sandbox application.';
	public $version		='1.0.0';
	public $coreMin		='2.*.*';
	public $dependencies=array
	(
		'applications'	=>false,
		'components'	=>array('page')
	);
}
?>