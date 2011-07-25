<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid_toolbar extends branch
{
	private $top=false;
	private $bottom=false;
	
	public function initiate()
	{
		$this->xInclude('item');
		return true;
	}
	
	public function newToolbar($grid,$position='top')
	{
		$return=false;
		if ($position!='top' && $position!='bottom')
		{
			//ERROR
		}
		else
		{
			$this->{$position}=__ext_grid_toolbar::construct($grid,$position);
			$this->{$position}->parent=$this;
			$return=$this->{$position};
		}
		return $return;
	}
	
	public function getToolbar($position=null)
	{
		$return=false;
		if ($position!='top' && $position!='bottom')
		{
			//ERROR
		}
		else if (!$this->{$position})
		{
			//ERROR
		}
		else
		{
			$return=$this->{$position};
		}
		return $return;
	}
	
	public function toJson($position=null)
	{
		$return=false;
		if ($position!='top' && $position!='bottom')
		{
			//ERROR
		}
		else if (!$this->{$position})
		{
			//ERROR
		}
		else
		{
			$return=$this->{$position}->toJson();
		}
		return $return;
	}
	
	public function isValid($position=null)
	{
		$return=false;
		if (isset($this->{$position}) && ($this->{$position} instanceof __ext_grid_toolbar))
		{
			if (count($this->{$position}->getItems()))$return=true;
		}
		return $return;
	}
}
class __ext_grid_toolbar extends ext_grid_getSetOptions
{
	public $parent=false;
	private $grid=false;
	
	private $position='top';
	private $items=array();
	
	private function __construct(__ext_grid $grid,$position='top')
	{
		$this->grid=$grid;
		$this->position=$position;
		return true;
	}
	
	public function construct($grid,$position)
	{
		return new __ext_grid_toolbar($grid,$position);
	}
	
	public function toJSON()
	{
		$return=array();
		for ($i=0,$j=count($this->items); $i<$j; $i++)
		{
			if ($this->items[$i] instanceof ext_grid_toolbar_item)
			{
				$return[]=$this->items[$i]->toJson();
			}
			else
			{
				$return[]=$this->items[$i];
			}
		}
		return $return;
	}
	
	public function optionsToJson()
	{
		return $this->getOptions();
	}
	
	public function getItems()
	{
		return $this->items;
	}
	
	public function add($type=null)
	{
		$index=count($this->items);
		$this->items[$index]=new ext_grid_toolbar_item($type);
		return $this->items[$index];
	}
	
	public function addButton()
	{
		return $this->add('button');
	}
	
	public function addSplitButton()
	{
		return $this->add('splitButton');
	}
	
	public function addMenu()
	{
		return $this->add('menu');
	}
	
	public function addText($text='')
	{
		$this->items[]=$text;
		return $this;
	}
	
	public function addSeparator()
	{
		$this->items[]='-';
		return $this;
	}
	
	public function addSpacer($spacers=1)
	{
		for($i=0; $i<$spacers; $i++)$this->items[]=' ';
		return $this;
	}
	
	public function addFill()
	{
		$this->items[]='->';
		return $this;
	}
}
?>