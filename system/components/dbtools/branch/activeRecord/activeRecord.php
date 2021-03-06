<?php
class dbtools_activeRecord_activeRecord extends overloader implements SeekableIterator
{
	private $database				='';
	private $name					='';
	private $prefix					='';
	private $columns				=array();
	private $columnNames			=array();
	private $unprefixedColumnNames	=array();
	private $idColumn				=false;
	private $_records				=null;
	private $__pointer				=0;
	private $_gotLength				=false;
//	public $length					=0;
	
//	private $row=array();
	
	public function __construct(dbtools_activeRecord $parent)
	{
		parent::__construct($parent);
		$this->_records=new dbtools_activeRecord_recordSet($this);
	}
	
	public function __get($theVar=null)
	{
		//Check if defined.
		if ($theVar=='length')
		{
			if ($this->_gotLength)
			{
				return $this->length;
			}
			else
			{
				return $this->updateLength();
			}
		}
		else
		{
			return parent::__get($theVar);
		}
	}
	
	public function current()
	{
		
		return ($this->length)?$this->_records[$this->_pointer]:null;
	}
	
	public function key()
	{
		return $this->_pointer;
	}
	
	public function next()
	{
		++$this->_pointer;
		if ($this->valid())
		{
			return $this->current();
		}
		return null;
	}
	
	public function back()
	{
		if ($this->_pointer!=0)--$this->_pointer;
		return $this->current();
	}
	
	public function rewind()
	{
		$this->_pointer=0;
		return $this->current();
	}
	
	public function valid()
	{
		return $this->_pointer<count($this->_records);
	}
	
	public function seek($position)
	{
		if (isset($this->_records[$position]))
		{
			$this->_pointer=$position;
			return $this->current();
		}
		return null;
	}
	
	private function fillNewRecordValues($values=array())
	{
		foreach ($values as $key=>&$val)
		{
			if (!in_array($key,$this->columnNames) && in_array($key,$this->unprefixedColumnNames))
			{
				$tmp=$val;
				unset($values[$key]);
				$values[$this->prefix.'_'.$key]=$tmp;
			}
		}
		foreach ($this->columns as $name=>&$def)
		{
			if (!isset($values[$name]))
			{
				$values[$name]=null;
			}
		}
		return $values;
	}
	
	public function newRecord($values=array())
	{
		if (count($values))
		{
			return new dbtools_activeRecord_record($this,$this->fillNewRecordValues($values),true);
		}
		else
		{
			return new dbtools_activeRecord_record($this,$this->fillNewRecordValues(),true);
		}
	}
	
	public function setName($name)
	{
		$this->name=$name;
		return $this;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setDatabase($database)
	{
		$this->database=$database;
		return $this;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function setPrefix($prefix)
	{
		$this->prefix=$prefix;
		return $this;
	}
	
	public function getPrefix()
	{
		return $this->prefix;
	}
	
	public function registerColumn($name,$type,$length=0,$primaryKey=false,$id=false)
	{
		$this->columns[$name]=array
		(
			'name'		=>$name,
			'type'		=>$type,
			'length'	=>$length,
			'primaryKey'=>$primaryKey,
			'id'		=>$id
		);
		array_push($this->columnNames,$name);
		array_push($this->unprefixedColumnNames,str_replace($this->prefix.'_','',$name));
		if ($id)
		{
			$this->idColumn=$this->columns[$name];
		}
		return $this;
	}
	
	public function id($id)
	{
		if (!$this->idColumn)
		{
			return new dbtools_activeRecord_recordSet($this);
		}
		else
		{
			$result=array();
			if (!is_array($id))
			{
				if (isset($this->_records[$id]))
				{
					return $this->_records[$id];
				}
				else
				{
					$this->component->database->c('core')
								->query('SELECT * FROM [PREFIX]'.$this->name.' WHERE '.$this->idColumn['name'].'='.$id.' LIMIT 1;');
					$thisResult=$this->component->database->result();
					if (count($thisResult))
					{
						array_push($result,$thisResult);
					}
					else
					{
						array_push($result,$thisResult);
					}
				}
			}
			else
			{
				$this->component->database->c('core');
				for ($i=0,$j=count($id); $i<$j; $i++)
				{
					if ($this->_records->is_set($id[$i]))
					{
						array_push($result,$this->_records->get($id[$i]));
					}
					else
					{
						$this->component->database->query('SELECT * FROM [PREFIX]'.$this->name.' WHERE '.$this->idColumn['name'].'='.$id[$i].' LIMIT 1;');
						$thisResult=$this->component->database->result();
						if (count($thisResult))
						{
							array_push($result,$thisResult);
						}
						else
						{
							array_push($result,$thisResult);
						}
					}
				}
			}
			$recordSet=new dbtools_activeRecord_recordSet($this);
			for ($i=0,$j=count($result); $i<$j; $i++)
			{
//				if (is_null($result[$i]))
//				{
//					array_push($return,null);
//					continue;
//				}
				if (!$this->isRecord($result[$i]))
				{
					$thisRecord=new dbtools_activeRecord_record($this,$result[$i]);
					$this->_records->set($result[$i][$this->idColumn['name']],$thisRecord);
					$recordSet->push($thisRecord);
				}
				else
				{
					$recordSet->push($result[$i]);
				}
			}
			return $recordSet;
		}
	}
	
//	public function select($keys='*',$whereKey=false,$whereOperator=false,$value=false)
//	{
//		$query=new dbtools_activeRecord_selection($this);
//		
//	}
	
	public function quickSelect()
	{
		$numArgs=func_num_args();
		if (!$numArgs)
		{
			$this->component->database->query('SELECT * FROM [PREFIX]'.$this->name.';');
			$result=$this->component->database->result();
			$recordSet=new dbtools_activeRecord_recordSet($this);
			for ($i=0,$j=count($result); $i<$j; $i++)
			{
				if (!$this->isRecord($result[$i]))
				{
					$thisRecord=new dbtools_activeRecord_record($this,$result[$i]);
					$this->_records->set($result[$i][$this->idColumn['name']],$thisRecord);
					$recordSet->push($thisRecord);
				}
				else
				{
					$recordSet->push($result[$i]);
				}
			}
		}
		else
		{
			$selection=$this->newSelection();
			$args=func_get_args();
			for ($i=0; $i<$numArgs; $i++)
			{
				$selection->filter($args[$i]);
			}
			$recordSet=$selection->execute();
		}
		return $recordSet;
	}
	
	public function commit(dbtools_activeRecord_record $record)
	{
		if (!$record->isNewRecord())
		{
			$update=array();
			$query='UPDATE [PREFIX]'.$this->name.' SET ';
			foreach ($record->getValues() as $key=>$val)
			{
				if ($key==$this->idColumn['name'])continue;
				$update[].="$key='$val'";
			}
			$query.=@implode(', ',$update).' WHERE '.$this->idColumn['name'].'='.$record->{$this->idColumn['name']}.';';
			if ($this->component->database->c('core')->query($query))
			{
				return true;
			}
			else
			{
				//Error
			}
		}
		else
		{
			$values=$record->getValues();
			$query='INSERT INTO [PREFIX]'.$this->name.' ('
					.implode(array_keys($values),',')
					.')VALUES('
					.'"'.implode(array_values($values),'","').'");';
			if ($this->component->database->c('core')->query($query))
			{
				$id=$this->component->database->lastInsertID();
				$record->{$this->idColumn['name']}=$id;
				$this->_records[$id]=$record;
				return true;
			}
			else
			{
				//Error
			}
		}
	}
	
	public function delete(dbtools_activeRecord_record $record)
	{
		if (!$record->isNewRecord())
		{
			$this->component->database->c('core')->query('DELETE FROM [PREFIX]'.$this->name.' WHERE '.$this->idColumn['name'].'='.$record->{$this->idColumn['name']}.' LIMIT 1;');
			unset($this->_records[$record->{$this->idColumn['name']}]);
		}
		return true;
	}
	
//	public function forget(dbtools_activeRecord_record $record)
//	{
//		unset($record);
//	}
	
	public function isRecord($object)
	{
		return ($object instanceof dbtools_activeRecord_record);
	}
	
	public function getIDColumn()
	{
		return $this->idColumn;
	}
	
	public function getColumnNames($prefixed=true,$both=false)
	{
		if ($prefixed)
		{
			if (!$both)
			{
				return $this->columnNames;
			}
			else
			{
				return array_merge($this->columnNames,$this->unprefixedColumnNames);
			}
		}
		else
		{
			return $this->unprefixedColumnNames;
		}
	}
	
	public function getCachedRecords()
	{
		return $this->_records;
	}
	
	private function updateLength()
	{
		$this->component->database->c('core')->query('SELECT COUNT(*) as length FROM [PREFIX]'.$this->name.' LIMIT 1;');
		$this->length=$this->component->database->result('length');
		return $this->length;
	}
	
	public function newSelection()
	{
		return new dbtools_activeRecord_selection($this);
	}
}
?>