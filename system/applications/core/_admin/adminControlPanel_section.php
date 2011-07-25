<?php
class adminControlPanel_section extends overloader
{
	private $id			=null;
	private $title		=null;
	private $weight		=0;
	private $view		=null;
	private $controller	=null;
	
//	private $menu=null;
	
	private $children	=array();
	
	public function __construct($parent,$params)
	{
		parent::__construct($parent);
//		$this->menu=new adminControlPanel_menuItem();
		if (isset($params['id']))
		{
			$this->setID($params['id']);
		}
		else
		{
			$this->exception('Invalid section. Section MUST have an ID.');
		}
		if (isset($params['title']))
		{
			$this->setTitle($params['title']);
		}
		else
		{
			$this->exception('Section title must be defined and cannot be blank.');
		}
		if (isset($params['view']))
		{
			$this->setView($params['view']);
		}
		else
		{
			$this->exception('Item view must be defined and cannot be blank.');
		}
		if (isset($params['controller']))
		{
			$this->setController($params['controller']);
		}
		if (isset($params['weight']))
		{
			$this->setWeight($params['weight']);
		}
	}
	
	public function addChildSection($section)
	{
		if ($section instanceof adminControlPanel_section)
		{
			$this->children[]=$section;
		}
		elseif (is_array($section))
		{
			if (!isset($item[0]))
			{
				$this->children[]=new adminControlPanel_section($this,$section);
			}
			else
			{
				for ($i=0,$j=count($section); $i<$j; $i++)
				{
					$this->children[]=new adminControlPanel_section($this,$section[$i]);
				}
			}
		}
		else
		{
			$this->exception('Unable to add section. Invalid section parameter.');
		}
		return $this;
	}
	
	public function setID($id)
	{
		if (is_string($id))
		{
			$this->id=$id;
		}
		else
		{
			$this->exception('Section id must be a string.');
		}
		return $this;
	}
	
	public function getID()
	{
		return $this->id;
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
				$this->exception('Section title must be defined as a string.');
			}
		}
		else
		{
			$this->exception('Section title cannot be blank.');
		}
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setWeight($weight=0)
	{
		if (is_numeric($weight))
		{
			$this->weight=(int)$weight;
		}
		else
		{
			$this->exception('Section weight must be numeric.');
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
				$this->exception('Section view must be defined as a string.');
			}
		}
		else
		{
			$this->exception('Section view cannot be blank.');
		}
		return $this;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function setController($controller=null)
	{
		if (!empty($controller))
		{
			if (is_string($controller))
			{
				$this->controller=$controller;
			}
			else
			{
				$this->exception('Section controller must be defined as a string.');
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
			'id'		=>$this->id,
			'title'		=>$this->title,
			'weight'	=>$this->weight,
			'view'		=>$this->view,
			'controller'=>$this->controller,
			'children'	=>$children
		);
	}
}
?>