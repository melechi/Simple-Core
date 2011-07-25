<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Query Builder component.
 * 
 * This is still in development and has only been tested with SQLite.
 * 
 * @author Timothy Chandler
 * @version 1.0
 * @copyright Simple Site Solutions 05/12/2007
 */
class component_querybuilder extends component
{
	const EXCEPTION_NAME='Query Builder Exception';
	
	public function createTable()
	{
		return new querybuilder_createTable;
	}
	
	public function insert()
	{
		return new querybuilder_insert;
	}
	
	public function select()
	{
		return new querybuilder_select;
	}
}
class querybuilder_select
{
	private $table=false;
	private $column=array();
	private $condition=array();
	private $orderBy='';
	private $limit=false;
	
	public function table($theTable=null)
	{
		if ($theTable)
		{
			$this->table=$theTable;
		}
		return $this;
	}
	
	public function column()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		if ($numArgs)
		{
			for ($i=0; $i<$numArgs; $i++)
			{
				$this->column[]=$args[$i];
			}
		}
		else
		{
			$this->column=true;
		}
		return $this;
	}
	
	public function condition($theCondition=null,$theConnector=null)
	{
		if ($theCondition)
		{
			if (!count($this->condition))
			{
				$this->condition[]='WHERE '.$theCondition;
			}
			else
			{
				if (!@is_null($theConnector))
				{
					if (@preg_match('/^AND|OR$/',$theConnector))
					{
						$this->condition[]=$theConnector.' '.$theCondition;
					}
					else
					{
						$this->condition[]='AND '.$theCondition;
					}
				}
				else
				{
					$this->condition[]='AND '.$theCondition;
				}
			}
		}
		return $this;
	}
	
	public function create()
	{
		$return=false;
		if (!empty($this->table) && count($this->column))
		{
			$return='SELECT ';
			if ($this->column===true)
			{
				$return.='*';
			}
			else
			{
				$return.=implode(',',$this->column);
			}
			$return.=' FROM '.$this->table;
			if (count($this->condition))
			{
				$return.=' '.$this->condition[0];
				if (isset($this->condition[1]))
				{
					for ($i=1,$j=count($this->condition); $i<$j; $i++)
					{
						$return.=' '.$this->condition[$i];
					}
				}
			}
			$return.=' '.$this->orderBy;
			if ($this->limit)
			{
				$return.=' '.$this->limit;
			}
		}
		return $return;
	}
	
	public function orderBy($orderBy='')
	{
		$this->orderBy=$orderBy;
		return $this;
	}
	
	public function limit($from=false,$limit=false)
	{
		if ($from!==false)
		{
			$this->limit='LIMIT '.$from;
			if ($limit)$this->limit.=','.$limit;
		}
		return $this;
	}
	
	public function __toString()
	{
		return $this->create();
	}
}
class querybuilder_insert
{
	private $table=false;
	private $data=array();
	//private $condition=array();
	
	public function table($theTable=null)
	{
		if ($theTable)
		{
			$this->table=$theTable;
		}
		return $this;
	}
	
	public function data($theData=null,$value=null)
	{
		if (is_array($theData))
		{
			foreach ($theData as $column=>$value)
			{
				$this->data[$column]=$value;
			}
		}
		elseif (!empty($theData) && !empty($value))
		{
			$this->data[$theData]=$value;
		}
		return $this;
	}
	
	public function create()
	{
		$return=false;
		if (!empty($this->table) && count($this->data))
		{
			$return='INSERT INTO '.$this->table.' (';
			$columns=array();
			$data=array();
			reset($this->data);
			foreach ($this->data as $column=>$value)
			{
				$columns[]=$column;
				$data[]=($value!='?')?"'$value'":$value;
			}
			$return.=implode(',',$columns).')VALUES('.implode(',',$data).');';
		}
		return $return;
	}
	
	public function __toString()
	{
		return $this->create();
	}
}
class querybuilder_createTable
{
	public $name=false;
	public $column=array();
	
	public function name($theName=null)
	{
		if ($theName)
		{
			$this->name=$theName;
		}
		return $this;
	}
	
	public function column($name=null,$type=null,$length=null,$null=null,$primary=false)
	{
		if ($name && $type)
		{
			$i=count($this->column);
			$this->column[$i]['name']=$name;
			$this->column[$i]['type']=$type;
			$this->column[$i]['length']=$length;
			$this->column[$i]['null']=$null;
			$this->column[$i]['primary']=$primary;
		}
		return $this;
	}
	
	public function create()
	{
		$return=false;
		if (!empty($this->name) && count($this->column))
		{
			$return='CREATE TABLE '.$this->name.'(';
			$columns=array();
			for ($i=0,$j=count($this->column); $i<$j; $i++)
			{
				$index=count($columns);
				$columns[$index]=$this->column[$i]['name'].' '.$this->column[$i]['type'];
				if (!is_null($this->column[$i]['length']))$columns[$index].='('.$this->column[$i]['length'].')';
				$columns[$index].=(is_null($this->column[$i]['null']))?' NULL ':' NOT NULL ';
				$columns[$index].=($this->column[$i]['primary']?'PRIMARY KEY':'');
			}
			$return.=@implode(',',$columns).')';
		}
		return $return;
	}
	
	public function __toString()
	{
		return $this->create();
	}
}
?>