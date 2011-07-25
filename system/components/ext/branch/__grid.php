<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class __ext_grid extends ext_grid_getSetOptions
{
	public $parent=false;
	
	private $render=false;
	private $renderTo=false;
	private $type='default';
	private $grouping=false;
	private $pagination=false;
	private $groupBy=false;
	private $selectionModel=false;
	private $columns=array();
	private $rowID=false;
	private $rows=array();
	
	private $blacklist=array('store','columns','colModel','cm','view','renderTo');
	
	private function __construct(){}
	static public function construct()
	{
		return new __ext_grid;
	}
	
	public function toJson()
	{
		$columns=array();
		while ($column=$this->eachColumn())
		{
			$columns[]=$column->toJson();
		}
		$selectionModel=($this->selectionModel)?$this->selectionModel->toJSON():false;
		$toolbar=false;
		if ($this->parent->toolbar->isValid('top'))
		{
			$thisToolbar=$this->getToolbar('top');
			$toolbar['top']=$thisToolbar->toJson();
			$toolbar['topOptions']=$thisToolbar->optionsToJson();
		}
		if ($this->parent->toolbar->isValid('bottom'))
		{
			$thisToolbar=$this->getToolbar('bottom');
			$toolbar['bottom']=$thisToolbar->toJson();
			$toolbar['bottomOptions']=$thisToolbar->optionsToJson();
		}
		return json_encode
		(
			array
			(
				'success'=>true,
				'render'=>$this->render,
				'renderTo'=>$this->renderTo,
				'type'=>$this->type,
				'grouping'=>$this->grouping,
				'pagination'=>$this->pagination,
				'groupBy'=>$this->groupBy,
				'selectionModel'=>$selectionModel,
				'plugins'=>$this->parent->plugin->toJson(),
				'toolbar'=>$toolbar,
				'options'=>$this->getOptions(),
				'columns'=>$columns,
				'rowID'=>$this->rowID,
				'rows'=>(count($this->rows)?$this->rows:false),
			)
		);
	}
	
	/******** COLUMNS ********/
	
	public function newColumn($columnName=null)
	{
		$return=false;
		if (empty($columnName))
		{
			//ERROR
		}
		else
		{
			$this->columns[$columnName]=$this->parent->column->newColumn($this,$columnName);
			$return=$this->columns[$columnName];
		}
		return $return;
	}
	
	public function getColumn($columnName=null)
	{
		$return=false;
		if (empty($columnName))
		{
			//ERROR
		}
		elseif (!isset($this->columns[$columnName]))
		{
			//ERROR
		}
		else
		{
			$return=$this->columns[$columnName];
		}
		return $return;
	}
	
	public function isColumn($columnName=null)
	{
		return isset($this->columns[$columnName]);
	}
	
	public function eachColumn()
	{
		list(,$return)=each($this->columns);
		if (!$return)reset($this->columns);
		return $return;
	}
	
	/******** SELECTION MODEL ********/
	
	public function setSelectionModel($type='row')
	{
		$this->selectionModel=$this->parent->selectionModel->newSelectionModel($this,$type);
		return $this->selectionModel;
	}
	
	public function getSelectionModel()
	{
		return $this->selectionModel;
	}
	
	/******** PLUGINS ********/
	
	public function usePlugin($type=null)
	{
		$return=false;
		if (empty($type))
		{
			//ERROR
		}
		else
		{
			$return=$this->parent->plugin->newPlugin($this,$type);
		}
		return $return;
	}
	
	public function getUsedPlugin($type=null)
	{
		return $this->parent->plugin->getPlugin($type);
	}
	
	/******** TOOLBARS ********/
	
	public function newToolbar($position='top')
	{
		$return=$this->parent->toolbar->newToolbar($this,$position);
		return $return;
	}
	
	public function getToolbar($position='top')
	{
		$return=false;
		if ($position!='top' && $position!='bottom')
		{
			//ERROR
		}
		else
		{
			$return=$this->parent->toolbar->getToolbar($position);
		}
		return $return;
	}
	
	/******** ROWS ********/
	
	public function setRowID($id=false)
	{
		$this->rowID=$id;
		return $this;
	}
	
	public function getRowID()
	{
		return $this->rowID;
	}
	
	public function addRow($row=array())
	{
		$index=count($this->rows);
		reset($row);
		while (list($key,$val)=each($row))
		{
			$this->rows[$index][$key]=$val;
		}
		return $this;
	}
	
	public function addRowSet($rowSet=array())
	{
		if (count($rowSet)===1)
		{
			$this->addRow($rowSet[0]);
		}
		else
		{
			for ($i=0,$j=count($rowSet); $i<$j; $i++)
			{
				$this->addRow($rowSet[$i]);
			}
		}
		return $this;
	}
	
	public function getRow($row=0)
	{
		return (isset($this->rows[$row]))?$this->rows[$row]:null;
	}
	
	public function deleteRow($row=null)
	{
		if (isset($this->rows[$row]))
		{
			unset($this->rows[$row]);
		}
		return $this;
	}
	
	/******** RENDER TO ********/
	
	public function setRenderTo($renderTo=false,$render=true)
	{
		$this->renderTo=$renderTo;
		$this->render=($renderTo)?$render:false;
		return $this;
	}
	
	public function getRenderTo()
	{
		return $this->renderTo;
	}
	
	public function willRender()
	{
		return $this->render;
	}
	
	/******** TYPE ********/
	
	public function setType($type='default')
	{
		if (!preg_match('@default|editable|property@',$type))
		{
			//ERROR.
		}
		else
		{
			$this->type=$type;
		}
		return $this;
	}
	
	public function getType()
	{
		return $this->editable;
	}
	
	/******** GROUPING ********/
	
	public function setGrouping($grouping=false,$groupBy=null)
	{
		$this->grouping=$grouping;
		if ($grouping)
		{
			if (empty($groupBy))
			{
				//ERROR
			}
			else
			{
				$this->groupBy=$groupBy;
			}
		}
		return $this;
	}
	
	public function getGrouping()
	{
		return array($this->grouping,$this->groupBy);
	}
	
	/******** PAGINATION ********/
	
//	public function enablePagination()
//	{
//		$this->pagination=array();
//		return $this;
//	}
//	
//	public function disablePagination()
//	{
//		$this->pagination=false;
//		return $this;
//	}
	
	public function bindProxy($address=null,$pageSize=10)
	{
		$this->pagination['proxy']=$address;
		$this->pagination['pageSize']=$pageSize;
		return $this;
	}
}
?>