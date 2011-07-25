<?php
class orm_recordSet implements SeekableIterator
{
//	private $_count		=0;
	private $_pointer	=0;
	private $_records	=array();
	
	public $parent		=null;
	public $length		=0;
	
	public function __construct(orm_activeRecord $parent,$records=array(),$mixedParents=array())
//	public function __construct($records=array())
	{
		$this->parent	=$parent;
		if (is_array($records))
		{
			if (!$mixedParents)
			{
				for ($i=0,$j=count($records); $i<$j; $i++)
				{
					if (!$this->parent->isRecord($records[$i]))
					{
						$records[$i]=$this->parent->newRecord($records[$i]);
						$records[$i]->flagOld();
					}
				}
			}
			else
			{
				//TODO: FREEKN' FIX MIXED RECORDS!!!
				$records=$this->parent->newRecord($records,$mixedParents);
			}
		}
		$this->_records	=$records;
		$this->length	=count($records);
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
	
	public function reset()
	{
		$this->_pointer=0;
	}
	
	public function push(orm_record $record)
	{
		array_push($this->_records,$record);
		return ++$this->length;
	}
	
	public function pop()
	{
		$this->length--;
		return array_pop($this->_records);
	}
	
	public function shift(orm_record $record)
	{
		array_shift($this->_records,$record);
		return ++$this->length;
	}
	
	public function unshift()
	{
		$this->length--;
		return array_shift($this->_records);
	}
	
	public function set($position,orm_record $record)
	{
		$this->_records[$position]=$record;
		return $this;
	}
	
//	public function get($position)
//	{
//		return $this->_records[$position];
//	}
	
//	public function is_set($id)
//	{
//		return $this->find($this->parent->getIDColumn(),$id);
//	}
	
//	public function un_set($position)
//	{
//		unset($this->_records[$position]);
//	}
	
	public function filter()
	{
		
	}
	
	public function find($key,$val)
	{
		for ($i=0,$j=count($this->_records); $i<$j; $i++)
		{
			if ($this->_records[$i]->{$key}==$val)
			{
				return $this->_records[$i]->{$key};
			}
		}
		return null;
	}
	
	public function remove($record)
	{
		for ($i=0,$j=count($this->_records); $i<$j; $i++)
		{
			if ($this->_records[$i]===$record)
			{
				unset($this->_records[$i]);
				sort($this->_records);
				$this->reset();
			}
		}
		return $this;
	}
}
?>