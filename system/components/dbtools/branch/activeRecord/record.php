<?php
class dbtools_activeRecord_record// implements SeekableIterator// extends overloader
{
	private $__POINTER__=0;
	private $__LOOKUPTABLE__=array();
	private $__SIZE__=0;
	
	private $initialRow	=array();
	private $row		=array();
	private $newRecord	=false;
	
	public $parent		=null;
	
	public function __construct(dbtools_activeRecord_activeRecord $parent,$record=array(),$newRecord=false)
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
			return $this->row[$this->parent->getPrefix().'_'.$theVar];
		}
		else
		{
			return $this->row[$theVar];
		}
	}
	
	public function __set($theVar,$theValue)
	{
		if (!in_array($theVar,$this->parent->getColumnNames(true,true)))
		{
			$this->parent->exception('Invalid column. Column "'.$theVar.'" does not exist.');
		}
		else if (in_array($theVar,$this->parent->getColumnNames(false)))
		{
			$this->row[$this->parent->getPrefix().'_'.$theVar]=$theValue;
			return $this->row[$this->parent->getPrefix().'_'.$theVar];
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
			$this->values=$this->parent->fillNewRecordValues($values);
		}
		return $this;
	}
	
	public function getValues()
	{
		return $this->row;
	}
	
	public function commit()
	{
		if ($this->parent->commit($this))
		{
			$this->newRecord=false;
		}
		return $this;
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
}
?>