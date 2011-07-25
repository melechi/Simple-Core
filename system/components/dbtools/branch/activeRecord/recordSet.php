<?php
class dbtools_activeRecord_recordSet implements SeekableIterator
{
//	private $_count		=0;
	private $_pointer	=0;
	private $_records	=array();
	
	public $parent		=null;
	public $length		=0;
	
	public function __construct(dbtools_activeRecord_activeRecord $parent,$records=array())
//	public function __construct($records=array())
	{
		$this->parent	=$parent;
		
		for ($i=0,$j=count($records); $i<$j; $i++)
		{
			if (!$this->parent->isRecord($records[$i]))
			{
				$records[$i]=$this->parent->newRecord($records[$i]);
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
	
	public function push(dbtools_activeRecord_record $record)
	{
		array_push($this->_records,$record);
		return ++$this->length;
	}
	
	public function pop()
	{
		$this->length--;
		return array_pop($this->_records);
	}
	
	public function shift(dbtools_activeRecord_record $record)
	{
		array_shift($this->_records,$record);
		return ++$this->length;
	}
	
	public function unshift()
	{
		$this->length--;
		return array_shift($this->_records);
	}
	
	public function set($position,dbtools_activeRecord_record $record)
	{
		$this->_records[$position]=$record;
		return $this;
	}
	
	public function get($position)
	{
		return $this->_records[$position];
	}
	
	public function is_set($position)
	{
		return isset($this->_records[$position]);
	}
	
	public function un_set($position)
	{
		unset($this->_records[$position]);
	}
	
	public function filter()
	{
		
	}
}
?>