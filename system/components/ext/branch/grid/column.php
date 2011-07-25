<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid_column extends branch
{
	public function newColumn($grid,$columnName)
	{
		$return=__ext_grid_column::construct($grid,$columnName);
		$return->parent=$this;
		return $return;
	}
}
class __ext_grid_column extends ext_grid_getSetOptions
{
	public $parent=false;
	private $grid=false;
	
	private $name=false;
	private $type='auto';
	private $header='';
	private $editor=false;
	
	private $types=array('auto','string','int','float','boolean','date','datetime');
	private $editorTypes=array('text','combo','check','date','html','number','radio','textarea','time');
	private $blacklist=array('name','type','header','editor','renderer');
	
	private function __construct(__ext_grid $grid,$columnName=null)
	{
		$this->grid=$grid;
		if (empty($columnName))
		{
			//ERROR
		}
		else
		{
			$this->name=$columnName;
		}
		return true;
	}
	
	public function construct($grid,$columnName)
	{
		return new __ext_grid_column($grid,$columnName);
	}
	
	public function toJson()
	{
		return array_merge
		(
			array
			(
				'name'=>$this->name,
				'type'=>$this->type,
				'header'=>$this->header,
				'editor'=>$this->editor
			),
			$this->getOptions()
		);
	}
	
	/******** TYPE ********/
	
	public function setType($type=null)
	{
		if (empty($type))
		{
			//ERROR
		}
		elseif (!in_array($type,$this->types))
		{
			//ERROR
		}
		else
		{
			$this->type=$type;
		}
		return $this;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	/******** HEADER ********/
	
	public function setHeader($header='')
	{
		$this->header=$header;
		return $this;
	}
	
	public function getHeader()
	{
		return $this->header;
	}
	
	/******** EDITOR ********/
	
	public function setEditor($type='text',$options=array(),$specialParams=false)
	{
		if (!in_array($type,$this->editorTypes))
		{
			//ERROR
		}
		else
		{
			$this->editor=array('type'=>$type);
			if (count($options))$this->editor['options']=$options;
			//TODO: finish.
			if ($type=='combo' && is_array($specialParams))
			{
				$keyVals=array();
				reset($specialParams);
				while(list($key,$val)=each($specialParams))
				{
					$keyVals[]=array('key'=>$key,'value'=>$val);
				}
				$this->editor['keyvals']=$keyVals;
			}
		}
		return $this;
	}
	
	public function getEditor()
	{
		return $this->editor;
	}
}
?>