<?php
class application_user extends application
{
	public $hasAuthentication	=true;
	public $useBreadcrumbs		=true;
	public $hasSettings			=true;
		
	function initiate()
	{
		$this->branch('user');
		return true;
	}
	
	public function eventmap()
	{
		$this->eventmap->event('register',array('object'=>'register'));
		$this->eventmap->event('list',array('object'=>'list'));
		return true;
	}
	
	public function sitemap()
	{
		$this->sitemap->page('|home');
		$this->sitemap->page('login');
		$this->sitemap->page('register',array('object'=>'register'));
		$this->sitemap->page('list',array('object'=>'list'));
		if (!$this->sitemap->outputComplete())$this->sitemap->forcePage('error/404');//Load 404 error page content.
		return true;
	}
}
?>