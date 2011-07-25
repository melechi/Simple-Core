<?php
class adminControlPanel_menuItem// extends overloader
{
	private $title		=null;
	private $weight		=0;
	private $view		=null;
	private $controller	=null;
	
	private $children	=array();
	
	public function __construct($item)//($parent,$item)
	{
//		parent::__construct($parent);
		if (isset($item['title']))
		{
			$this->setTitle($item['title']);
		}
		else
		{
			$this->exception('Item title must be defined and cannot be blank.');
		}
		if (isset($item['view']))
		{
			$this->setView($item['view']);
		}
		else
		{
			$this->exception('Item view must be defined and cannot be blank.');
		}
		if (isset($item['controller']))
		{
			$this->setController($item['controller']);
		}
		if (isset($item['weight']))
		{
			$this->setWeight($item['weight']);
		}
	}
	
	public function addChild($item)
	{
		if ($item instanceof adminControlPanel_menuItem)
		{
			$this->children[]=$item;
		}
		elseif (is_array($item))
		{
			if (!isset($item[0]))
			{
				$this->children[]=new adminControlPanel_menuItem($item);
			}
			else
			{
				for ($i=0,$j=count($item); $i<$j; $i++)
				{
					$this->children[]=new adminControlPanel_menuItem($item[$i]);
				}
			}
		}
		else
		{
			$this->exception('Unable to add menu item. Invalid menu item argument.');
		}
		return $this;
	}
	
	public function setTitle($title)
	{
		if (!empty($title))
		{
			if (is_string($title))
			{
				$this->title=$title;
			}
			else
			{
				$this->exception('Item title must be defined as a string.');
			}
		}
		else
		{
			$this->exception('Item title cannot be blank.');
		}
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setWeight($weight=0)
	{
		if (is_numeric($level))
		{
			$this->weight=(int)$weight;
		}
		else
		{
			$this->exception('Item weight must be numeric.');
		}
		return $this;
	}
	
	public function getWeight()
	{
		return $this->weight;
	}
	
	public function setView($view)
	{
		if (!empty($view))
		{
			if (is_string($view))
			{
				$this->view=$view;
			}
			else
			{
				$this->exception('Item view must be defined as a string.');
			}
		}
		else
		{
			$this->exception('Item view cannot be blank.');
		}
		return $this;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function setController($controller=null)
	{
		if (!empty($view))
		{
			if (is_string($view))
			{
				$this->controller=$view;
			}
			else
			{
				$this->exception('Item view must be defined as a string.');
			}
		}
		else
		{
			$this->controller=null;
		}
		return $this;
	}
	
	public function getController()
	{
		return $this->controller;
	}
	
	public function toArray()
	{
		$children=array();
		for ($i=0,$j=count($this->children); $i<$j; $i++)
		{
			$children[]=$this->children[$i]->toArray();
		}
		return array
		(
			'title'		=>$this->title,
			'weight'	=>$this->weight,
			'view'		=>$this->view,
			'controller'=>$this->controller,
			'children'	=>$children
		);
	}
}
?>