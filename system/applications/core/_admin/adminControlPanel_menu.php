<?php
class adminControlPanel_menu extends overloader
{
	private $items=array();
	
	public function __construct($parent)
	{
		parent::__construct($parent);
		
	}
	
	public function addMenuItem($item)
	{
		if ($item instanceof adminControlPanel_menuItem)
		{
			$this->items[]=$item;
		}
		elseif (is_array($item))
		{
			if (!isset($item[0]))
			{
				$this->items[]=new adminControlPanel_menuItem($this,$item);
			}
			else
			{
				for ($i=0,$j=count($item); $i<$j; $i++)
				{
					$this->items[]=new adminControlPanel_menuItem($this,$item[$i]);
				}
			}
		}
		else
		{
			$this->exception('Unable to add menu item. Invalid menu item argument.');
		}
		return $this;
	}
	
	public function toArray()
	{
		$return=array();
		for ($i=0,$j=count($this->items); $i<$j; $i++)
		{
			$return[]=$this->items[$i]->toArray();
		}
		return $return;
	}
}
?>