<?php
class application_blog_info
{
	public $name		='Blog';
	public $description	='Blogging software based on Simple Core 2.';
	public $version		='1.0.0';
	public $coreMin		='2.1.*';
	public $dependencies=array
	(
		'applications'	=>false,
		'components'	=>array
		(
			'page','template','feedback','session','authentication',
			'database','orm','breadcrumbs','category','emailtemplate',
			'phpMailer'
		)
	);
}
?>