<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class sqlite_instance extends overloader
{
	public $connection=false;
	private $lastError=false;
	private $lastResult=false;
	private $lastQuery=array();
	
	public function __construct($database=null,$persistant=false)
	{
		parent::__construct($this->component->sqlite);
		if ($this->connect($database,$persistant))
		{
			$this->branch('transaction');
			$this->resetLastQueryParams();
		}
		return true;
	}
	
	public function setMyDir()
	{
		$this->my->dir=$this->parent->my->includeDir;
		return true;
	}
	
	public function __destruct()
	{
		$this->disconnect();
		return true;
	}
	
	public function connect($database=null,$persistant=false)
	{
		$return=false;
		if ($database)
		{
			if ($persistant)
			{
				$this->connection=new PDO('sqlite:'.$database,null,null,array(PDO::ATTR_PERSISTENT=>true));
			}
			else
			{
				$this->connection=new PDO('sqlite:'.$database);
			}
			if ($this->connection)$return=true;
		}
		return $return;
	}
	
	public function disconnect()
	{
		$this->connection=null;
		return true;
	}
	
	private function resetLastQueryParams()
	{
		$this->lastQuery=array
		(
			'statement'=>false,
			'params'=>false,
			'sql'=>false,
			'resultSet'=>false,
			'numResults'=>0,
			'affectedRows'=>0
		);
		return $this;
	}
	
	private function executeStatement($type='query',$args=array())
	{
		$return=false;
		if (isset($args[1]))
		{
			$this->lastQuery['sql']=$args[0];
			$this->lastQuery['statement']=$this->connection->prepare($args[0]);
			if (!is_array($args[1]))
			{
				for ($i=1,$j=count($args); $i<$j; $i++)
				{
					$this->lastQuery['params'][]=$args[$i];
					$this->lastQuery['statement']->bindParam($i,$args[$i]);
				}
			}
			else
			{
				$i=1;
				foreach ($args[1] as &$val)
				{
					$this->lastQuery['params'][]=$val;
					$this->lastQuery['statement']->bindParam($i,$val);
					$i++;
				}
			}
			if ($this->lastQuery['statement']->execute())
			{
				$this->lastQuery['resultSet']=$this->lastQuery['statement']->fetchAll();
				$this->lastQuery['numResults']=count($this->lastQuery['resultSet']);
				$this->lastQuery['affectedRows']=0;
				switch ($type)
				{
					case 'query':
					{
						$this->lastQuery['affectedRows']=$this->lastQuery['statement']->rowCount();
						if (stristr($this->lastQuery['sql'],'SELECT'))
						{
							$return=$this->lastQuery['numResults'];
						}
						else
						{
							$return=$this->lastQuery['affectedRows'];
						}
						break;
					}
					case 'select':
					{
						$return=$this->lastQuery['numResults'];
						break;
					}
					case 'insert':
					{
						$this->lastQuery['affectedRows']=$this->lastQuery['statement']->rowCount();
						$return=$this->lastQuery['affectedRows'];
						break;
					}
					case 'update':
					{
						$this->lastQuery['affectedRows']=$this->lastQuery['statement']->rowCount();
						$return=$this->lastQuery['affectedRows'];
						break;
					}
					case 'delete':
					{
						$this->lastQuery['affectedRows']=$this->lastQuery['statement']->rowCount();
						$return=$this->lastQuery['affectedRows'];
						break;
					}
				}
			}
		}
		return $return;
	}
	
	public function query()
	{
		$this->resetLastQueryParams();
		$return=false;
		$args=func_get_args();
		if (func_num_args()>1)
		{
			$return=$this->executeStatement('query',$args);
		}
		else
		{
			$this->lastQuery['statement']=		$this->connection->query($args[0]);
			if ($this->lastQuery['statement'])
			{
				$this->lastQuery['sql']=			$args[0];
				$this->lastQuery['resultSet']=		$this->lastQuery['statement']->fetchAll();
				$this->lastQuery['numResults']=		count($this->lastQuery['resultSet']);
				$this->lastQuery['affectedRows']=	$this->lastQuery['statement']->rowCount();
				$return=$this->lastQuery['statement'];
			}
		}
		return $return;
	}
	
	public function insert()
	{
		$this->resetLastQueryParams();
		$return=false;
		$args=func_get_args();
		if (func_num_args()>1)
		{
			$return=$this->executeStatement('query',$args);
		}
		else
		{
			$this->lastQuery['affectedRows']=		$this->connection->exec($args[0]);
			$this->lastQuery['sql']=				$args[0];
			$return=								$this->lastQuery['affectedRows'];
		}
		return $return;
	}
	
	public function select()
	{
		$this->resetLastQueryParams();
		$return=false;
		$args=func_get_args();
		if (func_num_args()>1)
		{
			$return=$this->executeStatement('query',$args);
		}
		else
		{
			$this->lastQuery['statement']=		$this->connection->query($args[0]);
			$this->lastQuery['resultSet']=		$this->lastQuery['statement']->fetchAll();
			$this->lastQuery['numResults']=		count($this->lastQuery['resultSet']);
			$this->lastQuery['sql']=			$args[0];
			$return=							$this->lastQuery['numResults'];
		}
		return $return;
	}
	
	public function update()
	{
		$this->resetLastQueryParams();
		$return=false;
		$args=func_get_args();
		if (func_num_args()>1)
		{
			$return=$this->executeStatement('query',$args);
		}
		else
		{
			$this->lastQuery['affectedRows']=		$this->connection->exec($args[0]);
			$this->lastQuery['sql']=				$args[0];
			$return=								$this->lastQuery['affectedRows'];
		}
		return $return;
	}
	
	public function delete()
	{
		$this->resetLastQueryParams();
		$return=false;
		$args=func_get_args();
		if (func_num_args()>1)
		{
			$return=$this->executeStatement('query',$args);
		}
		else
		{
			$this->lastQuery['affectedRows']=		$this->connection->exec($args[0]);
			$this->lastQuery['sql']=				$args[0];
			$return=								$this->lastQuery['affectedRows'];
		}
		return $return;
	}
	
	public function tableExists($theTable=null)
	{
		$return=false;
		if ($this->connection && $theTable)
		{
			$query=<<<SQL
			SELECT name
			FROM sqlite_master
			WHERE type='table'
			AND name='{$theTable}';
SQL;
			$result=$this->connection->query($this->connection,$query);
			if ($result && count($result->fetch(PDO::FETCH_NUM)))$return=true;
		}
		return $return;
	}
	
	public function getLastError()
	{
		return $this->lastError;
	}
	
	public function getAffectedRows()
	{
		return $this->lastQuery['affectedRows'];
	}
	
	public function getNumResults()
	{
		return $this->lastQuery['numResults'];
	}
	
	public function getLastStatement()
	{
		return $this->lastQuery['statement'];
	}
	
	public function getLastQuery()
	{
		return $this->lastQuery['sql'];
	}
	
	public function getLastQueryObject()
	{
		return $this->lastQuery;
	}
	
	public function result()
	{
		$return=false;
		if ($this->lastQuery['resultSet']===false)
		{
			if ($this->lastQuery['statement'] instanceof PDOStatement)
			{
				$return=$this->lastQuery['statement']->fetchAll();
			}
		}
		else
		{
			$return=$this->lastQuery['resultSet'];
		}
		return $return;
	}
}
?>