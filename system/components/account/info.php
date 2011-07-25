<?php
class component_account_info
{
	public $name		='Account';
	public $description	='Handles creation and management of users including application authentication.';
	public $version		='2.0.0';
	public $coreMin		='2.1.*';
	public $dependencies=array
	(
		'components'	=>array('session','database','foo')
	);
}
?>