<?php
class application_core_info
{
	public $name		='Core';
	public $description	='Simple Core\'s core application. Handles management and exceptions.';
	public $version		='1.1.0';
	public $coreMin		='2.1.*';
	public $dependencies=array
	(
		'applications'	=>false,
		'components'	=>array('page','template','ext')
	);
}
?>