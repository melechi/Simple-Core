<?php
abstract class adminControlPanel extends overloader
{
	abstract function initiate();
	
	private $modules=array();
	public $menu	=null;
	
	public function __construct($parent)
	{
		parent::__construct($parent);
		$this->my->dir=$this->parent->my->dir;
		foreach (new DirectoryIterator($this->my->dir.'_admin'._) as $iteration)
		{
			if ($iteration->isFile())
			{
				include_once($iteration->getPath()._.$iteration->getFilename());
			}
		}
//		$this->menu=new adminControlPanel_menu($this);
		$this->initiate();
	}
	
	public function __get($key)
	{
		if (in_array($key,array_keys($this->modules)))
		{
			return $this->getModule($key);
		}
		return parent::__get($key);
	}
	
	public function getModule($module,$initiate=true)
	{
		if (isset($this->modules[$module]))
		{
			if (is_string($this->modules[$module]))
			{
				include_once($this->modules[$module]);
				$className=$this->constructBranchName('module_'.$module);
				if (class_exists($className))
				{
					$this->modules[$module]=new $className($this);
					if ($this->modules[$module] instanceof adminControlPanel_module)
					{
						if ($initiate)$this->modules[$module]->initiate();
						return $this->modules[$module];
					}
					else
					{
						$this->exception('Unable to initate module "'.$module.'". A module must be an instance of "adminControlPanel_module".');
					}
				}
				else
				{
					$this->exception('Unable to initate module "'.$module.'" because the module\'s class name'
										.' does not conform to convention. The class name should be "'.$className.'"'
										.' and must be an instance of "adminControlPanel_module".');
				}
			}
			else
			{
				return $this->modules[$module];
			}
		}
		else
		{
			$this->exception('Unable to return module. Module "'.$module.'" is not registered.');
		}
	}
	
	public function connectModule($module)
	{
		if (is_dir($dir=$this->my->dir.'admin'._.'modules'._))
		{
			if (is_dir($dir.=$module._) && is_file($dir.$module.'.php'))
			{
				$this->modules[$module]=$dir.$module.'.php';
			}
			else
			{
				$this->exception('Invalid Module. Unable to find module "'.$module.'".');
			}
		}
		else
		{
			$this->exception('Unable to locate admin modules directory. Should be "<application>'._.'admin'._.'modules'._.'"');
		}
		return $this;
	}
	
	public function connectModules()
	{
		$args=func_get_args();
		if ($j=count($args))
		{
			for ($i=0; $i<$j; $i++)
			{
				$this->connectModule($args[$i]);
			}
		}
		return $this;
	}
	
	public function getModules()
	{
		$return=array();
		foreach ($this->modules as $key=>$module)
		{
			$sections=array();
			foreach ($this->{$key}->getSections() as $section)
			{
				$sections[]=$section->toArray();
			}
//			$sections=$this->sortSections($sections);
			$return[]=array
			(
				'id'		=>$this->{$key}->my->name,
				'title'		=>$this->{$key}->getRootSectionTitle(),
				'sections'	=>$sections
			);
		}
		return $return;
	}
	
	public function generateMenu()
	{
		$menu=array();
		foreach ($this->modules as $key=>$module)
		{
			$sections=$this->{$key}->getSections();
			$sections=$this->sortSections($sections);
			$menu[]=array
			(
				'title'		=>$this->{$key}->getRootSectionTitle(),
				'children'	=>$sections
			);
		}
		$this->menu=$menu;
	}
	
	public function getMenu()
	{
		return $this->menu;
	}
	
	private function sortSections(&$sections)
	{
		$currentLevel	=0;
		$levelGroups	=array();
		//Sort by level first.
		for ($i=0,$j=count($sections); $i<$j; $i++)
		{
			if (!isset($levelGroups[$sections[$i]['level']]))$levelGroups[$sections[$i]['level']]=array();
			$levelGroups[$sections[$i]['level']][]=$sections[$i];
		}
		ksort($levelGroups);
		//Sort by weight second.
		$weightedGroups=array();
		foreach ($levelGroups as $group)
		{
			$thisGroup=array();
			for ($i=0,$j=count($group); $i<$j; $i++)
			{
				if (!isset($thisGroup[$group[$i]['weight']]))
				{
					$thisGroup[$group[$i]['weight']]=array();
				}
				$thisGroup[$group[$i]['weight']][]=$group[$i];
			}
			ksort($thisGroup);
			$weightedGroups[]=$thisGroup;
		}
		//Prepare return.
		$return=array();
		for ($i=0,$j=count($weightedGroups); $i<$j; $i++)
		{
			foreach ($weightedGroups[$i] as $group)
			{
				$return=array_merge($return,$group);
			}
		}
		return $return;
	}
}
?>