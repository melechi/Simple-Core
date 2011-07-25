<?php
class application_blog extends application
{
	public $hasSettings=true;
	
	public function initiate()
	{
		
		return true;
	}
	
	public function eventmap()
	{
//		$this->eventmap->event('direct','API','?',array('folder'=>'direct','object'=>'API'));
		return true;
	} 
	
	public function sitemap()
	{
//		var_dump($this->makeURL('home'));
		if (!$this->global->get('html5'))
		{
			$this->sitemap->template['path'].='html4'._;
		}
		else
		{
			$this->sitemap->template['path'].='html5'._;
		}
		$this->sitemap->template['content']=$this->sitemap->template['path'].'content'._;
		
		$this->setTemplateVar('TITLE',(string)$this->settings->title);
		$this->setTemplateVar('BLURB',(string)$this->settings->blurb);
		
		$this->sitemap->page('|home',array('object'=>'home'));
//		$this->sitemap->page('test');
		if (preg_match('/20[0-9][0-9]/',$this->node(0)))
		{
			$this->sitemap->page('?','entriesList',array('object'=>'entry'));
		}
//		$this->sitemap->page('entry','?',array('object'=>'entry'));
		return true;
	}
}
?>