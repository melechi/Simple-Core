<?php
class orm_record// implements SeekableIterator// extends overloader
{
	private $__POINTER__=0;
	private $__LOOKUPTABLE__=array();
	private $__SIZE__=0;
	
	private $initialRow	=array();
	private $row		=array();
	private $newRecord	=false;
	
	public $parent		=null;
	
	public function __construct(orm_activeRecord $parent,$record=array(),$newRecord=false)
	{
//		parent::__construct($parent);
		$this->parent		=$parent;
		$this->initialRow	=$record;
		$this->row			=$record;
		$this->newRecord	=$newRecord;
	}
	
	public function __get($theVar=null)
	{
		//Check if defined.
		if (!in_array($theVar,$this->parent->getColumnNames(true,true)))
		{
//			return parent::__get($theVar);
			$this->parent->exception('Invalid column. Column "'.$theVar.'" does not exist.');
		}
		else if (in_array($theVar,$this->parent->getColumnNames(false)))
		{
			if ($this->parent->hasPrefix())
			{
				$theVar=$this->parent->getPrefix().'_'.$theVar;
			}
			return $this->row[$theVar];
		}
		else
		{
			return $this->row[$theVar];
		}
	}
	
	public function __set($theVar,$theValue)
	{
		if (empty($theVar))
		{
			$this->parent->exception('Unable to set value on blank column name.');
		}
		if (!in_array($theVar,$this->parent->getColumnNames(true,true)))
		{
			$this->parent->exception('Invalid column. Column "'.$theVar.'" does not exist.');
		}
		else if (in_array($theVar,$this->parent->getColumnNames(false)))
		{
			if ($this->parent->hasPrefix())
			{
				$theVar=$this->parent->getPrefix().'_'.$theVar;
			}
			$this->row[$theVar]=$theValue;
			return $this->row[$theVar];
		}
		else
		{
			$this->row[$theVar]=$theValue;
			return $this->row[$theVar];
		}
	}
	
	public function setValues($values=array())
	{
		if (!is_array($values))
		{
			$this->exception('Unable to set values on active record, $values must be an array.');
		}
		else
		{
			foreach ($values as $key=>$val)
			{
				$this->{$key}=$val;
			}
			$this->newRecord=false;
		}
		return $this;
	}
	
	public function getValues()
	{
		return $this->row;
	}
	
	public function commit()
	{
		if ($return=$this->parent->commit($this))
		{
			$this->newRecord=false;
		}
		return $return;
	}
	
	public function rollback()
	{
		$this->row=$this->initialRow;
		return $this;
	}
	
	public function delete()
	{
		return $this->parent->delete($this);
	}
	
	public function isNewRecord()
	{
		return $this->newRecord;
	}
	
	public function flagNew()
	{
		$this->newRecord=true;
	}
	
	public function flagOld()
	{
		$this->newRecord=false;
	}
}
?>