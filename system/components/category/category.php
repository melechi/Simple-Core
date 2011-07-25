<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class component_category extends component
{
	const EXCEPTION_NAME='Category Manager Exception';
	
	private $namespace=false;
	
	/**
	 * A method for presetting a namespace.
	 * 
	 * This method should be used before calling
	 * a category getter or setter.
	 * @access public
	 * @return object
	 */
	
	public function setNamespace($namespace=false)
	{
		$this->namespace=$namespace;
		return $this;
	}
	
	/**
	 * Alias of {@link setNamespace()}.
	 * @param 
	 * @access public
	 * @return object
	 */
	
	public function ns($namespace=false)
	{
		return $this->setNamespace($namespace);
	}
	
	/**
	 * Checks if a namespace has been set.
	 * @access public
	 * @return bool
	 */
	
	public function namespaceSet()
	{
		return $this->namespace?true:false;
	}
	
	/**
	 * Generic get category method.
	 * @return array,bool
	 */
	
	public function get($get='*',$key=1,$value=1,$from=0,$to=1,$order='',$extra='')
	{
		$return=false;
		$limit=($from==0 && $to==1)?'1':(string)$from.','.(string)$to;
		$query="SELECT {$get} FROM [PREFIX]category WHERE {$key}='{$value}' {$extra} LIMIT {$limit} {$order}";
		if ($this->component->database->c('core')->query($query))
		{
			$return=$this->component->database->result();
			if ($get!='*' && strpos($get,',')===false && $limit==1)
			{
				if (!is_scalar($return) && $return !==null)
				{
					if (is_array($return) && isset($return[$get]))
					{
						$return=$return[$get];
					}
					elseif (is_array($return) && isset($return[0][$get]))
					{
						$return=$return[0][$get];
					}
					else
					{
						$return=false;
					}
				}
			}
		}
		return $return;
	}
	
	public function getAll()
	{
		return $this->get('*',1,1,0,99999);
	}
	
	public function getTopLevelCategories()
	{
		$query=<<<SQL
		SELECT *
		FROM [PREFIX]category
		WHERE category_namespace='{$this->namespace}'
		AND category_parentid='0'
		AND category_status='1';
SQL;
		$this->component->database->c('core')->query($query);
		return $this->component->database->result();
	}
	
	/**
	 * Finds and returns child categories of given ID.
	 * 
	 * This method will only return the immidate children.
	 * It will not search recursively down the child tree.
	 * @access public
	 * @return array,bool
	 */
	
	public function getChildren($categoryID=null)
	{
		$return=false;
		if (!$this->namespaceSet())
		{
			$this->exception('Namespace not set! Use $this->component->category->ns($namespace)->getChildren()'
							.' to pre-define namespace.');
		}
		elseif (is_null($categoryID))
		{
			$this->exception('No ID given to '.__METHOD__.'(). You must provide a category ID.');
		}
		else
		{
			$query=<<<SQL
			SELECT *
			FROM [PREFIX]category
			WHERE category_namespace='{$this->namespace}'
			AND category_parentid='{$categoryID}'
			AND category_status='1';
SQL;
			$this->component->database->c('core')->query($query);
			$return=$this->component->database->result();
		}
		return $return;
	}
	
	public function hasChildren($categoryID=null)
	{
		$return=false;
		if (is_numeric($categoryID))
		{
			$query=<<<SQL
			SELECT COUNT(category_id)
			FROM [PREFIX]category
			WHERE category_namespace='{$this->namespace}'
			AND category_parentid='{$categoryID}'
			AND category_status='1'
			LIMIT 1;
SQL;
			$this->component->database->c('core')->query($query);
			$return=(bool)$this->component->database->result();
		}
		return $return;
	}
}