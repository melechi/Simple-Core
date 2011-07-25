<?php
class application_core extends application
{
	public $hasAuthentication	=false;
	public $useBreadcrumbs		=false;
		
	function initiate()
	{
//		die('123');
		return true;
	}
	
	public function eventmap()
	{
		$this->eventmap->event('direct','API','?',array('folder'=>'direct','object'=>'API'));
		$this->eventmap->event('generate_activerecord','?',array('object'=>'generate_activerecord'));
		return true;
	}
	
	public function sitemap()
	{
		if (strstr($this->lastNode(),'function.'))
		{
			$this->forward('http://www.php.net/'.$this->lastNode());
		}
		if ($this->node(0)=='sandbox')
		{
			$this->bindApplication('sandbox',array('sandbox'));
			exit();
		}
		//THIS IS FOR DEBUGGING WHILE BUILDING THE ADMIN STUFF AND SHOULD BE REMOVED LATER!
		else if ($this->node(0)=='admin')
		{
			$admin=$this->activateAdministration();
			$admin->generateMenu();
		}
		else if ($this->node(0))
		{
			$this->bindApplication($this->node(0),$this->node());
			exit();
		}
		$this->sitemap->page('|home');
		$this->exception();
		return true;
	}
	
	public function exception()
	{
		if (!$this->sitemap instanceof sitemap)
		{
			$this->sitemap=new sitemap($this);
			$this->sitemap->bindToAddress($this->node());
		}
		$this->sitemap->page('exception',array('shell'=>'exception'));
	}
}
?>