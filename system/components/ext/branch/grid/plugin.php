<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid_plugin extends branch
{
	private $plugins=array();
	
	public function newPlugin($grid,$type)
	{
		$return=false;
		$className='ext_grid_plugin_'.$type;
		if (!class_exists($className))
		{
			//ERROR
		}
		else
		{
			$this->plugins[$type]=new $className($grid);
			$this->plugins[$type]->parent=$this;
			$return=$this->plugins[$type];
		}
		return $return;
	}
	
	public function getPlugin($type=null)
	{
		$return=false;
		if (!isset($this->plugins[$type]))
		{
			//ERROR
		}
		else
		{
			$return=$this->plugins[$type];
		}
		return $return;
	}
	
	public function toJson()
	{
		$return=false;
		if (count($this->plugins))
		{
			$return=array();
			reset($this->plugins);
			while(list(,$plugin)=each($this->plugins))
			{
				$return[]=$plugin->toJson();
			}
		}
		return $return;
	}
}
interface ext_grid_plugin_interface
{
	public function __construct(__ext_grid $grid);
	public function toJson();
}
class ext_grid_plugin_rowExpander implements ext_grid_plugin_interface
{
	public $parent=false;
	private $grid=false;
	
	private $type='rowExpander';
	private $template='';
	
	public function __construct(__ext_grid $grid)
	{
		$this->grid=$grid;
		return true;
	}
	
	public function toJson()
	{
		return array
		(
			'type'=>$this->type,
			'tpl'=>$this->template
		);
	}
	
	public function setTemplate()
	{
		$this->template=implode(func_get_args());
		return $this;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}
}
?>