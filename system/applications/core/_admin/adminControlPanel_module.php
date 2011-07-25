<?php
abstract class adminControlPanel_module extends overloader
{
	abstract function initiate();
	
	private $rootSectionTitle	='Undefined Title';
	private $sections			=array();
//	private $menu				=null;
	
	public function __construct($parent)
	{
		parent::__construct($parent);
		$this->my->dir=$this->parent->my->dir.'admin'._.'modules'._.$this->my->name._;
	}
	
	public function setRootSectionTitle($title)
	{
		$this->rootSectionTitle=$title;
		return $this;
	}
	
	public function getRootSectionTitle()
	{
		return $this->rootSectionTitle;
	}
	
//	public function addSection()
//	{
//		$numArgs=func_num_args();
//		$args	=func_get_args();
//		if ($numArgs)
//		{
//			for ($i=0; $i<$numArgs; $i++)
//			{
//				array_push($this->sections,$args[$i]);
//			}
//		}
//		return $this;
//	}
	
	public function addSection($section)
	{
		if (!empty($section['id']))
		{
			return $this->sections[$section['id']]=new adminControlPanel_section($this,$section);
		}
		else
		{
			$this->exception('Unable to add section. No ID parameter provided.');
		}
	}
	
	public function getSection($id)
	{
		if (isset($this->sections[$id]))
		{
			return $this->sections[$id];
		}
		else
		{
			$this->exception('Invalid section id. ID "'.$id.'" was not found.');
		}
	}
	
	public function getSections()
	{
		return $this->sections;
	}
}
?>