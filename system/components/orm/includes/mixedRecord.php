<?php
class orm_mixedRecord extends orm_record
{
	private $initialRow	=array();
	private $row		=array();
	private $newRecord	=false;
	private $numRecords	=0;
	
	public $parent		=array();
	
	public function __construct($parents=array(),$records=array())
	{
		if (is_array($parents))
		{
//			$this->numRecords=count($parents);
//			for ($i=0; $i<$this->numRecords; $i++)
			$this->numRecords=count($records);
			for ($i=0,$j=count($parents); $i<$j; $i++)
			{
				if ($parents[$i] instanceof orm_activeRecord)
				{
					$this->parent[]=$parents[$i];
				}
				else
				{
					throw new Exception('Invalid parent object. Parent object must be an instance of "orm_activeRecord".');
				}
			}
		}
		else
		{
			throw new Exception('Invalid argument. Parent argument must be an array of "orm_activeRecord" instances.');
		}
		if (is_array($records))
		{
//			if (count($records)==$this->numRecords)
//			{
				$this->initialRow	=$records;
				$this->row			=$records;
				for ($i=0; $i<$this->numRecords; $i++)
				{
					$this->initialRow[]	=$records[$i];
					$this->row[]		=$records[$i];
				}
//			}
//			else
//			{
//				throw new Exception('Invalid record count. Expected "'.$this->numRecords.'" records got "'.count($records).'" records.');
//			}
		}
		else
		{
			throw new Exception('Invalid argument. Record argument must be an array of records instances.');
		}
		$this->newRecord	=false;
	}
	
	public function __get($theVar=null)
	{
		$columnNames=$this->getColumnNames();
		//Check if defined.
		if (!in_array($theVar,$columnNames[0]))
		{
			$this->parent->exception('Invalid column. Column "'.$theVar.'" does not exist.');
		}
		else if (in_array($theVar,$columnNames[1]))
		{
			for ($i=0; $i<$this->numRecords; $i++)
			{
				if (isset($this->row[$i][$this->parent->getPrefix().'_'.$theVar]))
				{
					return $this->row[$i][$this->parent->getPrefix().'_'.$theVar];
				}
			}
		}
		else
		{
			for ($i=0; $i<$this->numRecords; $i++)
			{
				if (isset($this->row[$i][$theVar]))
				{
					return $this->row[$i][$theVar];
				}
			}
		}
	}
	
	public function __set($theVar,$theValue)
	{
		$columnNames=$this->getColumnNames();
		if (!in_array($theVar,$columnNames[0]))
		{
			$this->parent->exception('Invalid column. Column "'.$theVar.'" does not exist.');
		}
		else if (in_array($theVar,$columnNames[1]))
		{
			for ($i=0; $i<$this->numRecords; $i++)
			{
				if (isset($this->row[$i][$this->parent->getPrefix().'_'.$theVar]))
				{
					$this->row[$i][$this->parent->getPrefix().'_'.$theVar]=$theValue;
					return $this->row[$i][$this->parent->getPrefix().'_'.$theVar];
				}
			}
		}
		else
		{
			for ($i=0; $i<$this->numRecords; $i++)
			{
				if (isset($this->row[$i][$theVar]))
				{
					$this->row[$i][$theVar]=$theValue;
					return $this->row[$i][$theVar];
				}
			}
		}
	}
	
	private function getColumnNames()
	{
		$columnNames=array(array(),array());
		for ($i=0; $i<$this->numRecords; $i++)
		{
			$columnNames[0]=array_merge($columnNames[0],$this->parent[$i]->getColumnNames(true,true));
			$columnNames[1]=array_merge($columnNames[1],$this->parent[$i]->getColumnNames(false));
		}
		return $columnNames;
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
		$values=array();
		for ($i=0; $i<$this->numRecords; $i++)
		{
			$values=array_merge($values,$this->row[$i]);
		}
		return $values;
	}
	
	public function commit()
	{
		for ($i=0; $i<$this->numRecords; $i++)
		{
			$this->parent[$i]->commit($this);
		}
		return $this;
	}
	
	public function rollback()
	{
		for ($i=0; $i<$this->numRecords; $i++)
		{
			$this->row[$i]=$this->initialRow[$i];
		}
		
		return $this;
	}
	
	public function delete()
	{
		for ($i=0; $i<$this->numRecords; $i++)
		{
			$this->parent[$i]->delete($this);
		}
	}
	
	public function isNewRecord()
	{
		return $this->newRecord;
	}
}
?>