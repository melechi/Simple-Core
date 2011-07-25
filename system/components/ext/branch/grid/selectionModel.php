<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid_selectionModel extends branch
{
	public function newSelectionModel($grid,$type)
	{
		$return=__ext_grid_selectionModel::construct($grid,$type);
		$return->parent=$this;
		return $return;
	}
}
class __ext_grid_selectionModel
{
	public $parent=false;
	private $grid=false;
	
	private $type='row';
	private $options=array();
	
	private $types=array('row','cell','checkbox');
	private $blacklist=array('type');
	
	private function __construct(__ext_grid $grid,$type='row')
	{
		$this->grid=$grid;
		if (!in_array($type,$this->types))
		{
			//ERROR
		}
		else
		{
			$this->type=$type;
		}
		return true;
	}
	
	public function construct($grid,$type)
	{
		return new __ext_grid_selectionModel($grid,$type);
	}
	
	public function toJson()
	{
		return array_merge
		(
			array('type'=>$this->type),
			$this->options
		);
	}
	
	/******** OPTIONS ********/
	
	public function setOption()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		if (!$numArgs)
		{
			//ERROR
		}
		elseif ($numArgs<2)
		{
			if (!is_array($args[0]))
			{
				//ERROR
			}
			else
			{
				reset($args[0]);
				while(list($key,$val)=each($args[0]))
				{
					if (in_array($key,$this->blacklist))
					{
						//ERROR
						break;
					}
					else
					{
						$this->options[$key]=$val;
					}
				}
			}
		}
		elseif ($numArgs==2)
		{
			if (in_array($args[0],$this->blacklist))
			{
				//ERROR
			}
			else
			{
				$this->options[$args[0]]=$args[1];
			}
		}
		return $this;
	}
	
	public function getOption($theOption=null)
	{
		$return=false;
		if (!isset($this->options[$theOption]))
		{
			//ERROR
		}
		else
		{
			$return=$this->options[$theOption];
		}
		return $return;
	}
	
	public function getOptions()
	{
		return $this->options;
	}
}
?>