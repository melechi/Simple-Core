<?php
define('ACTIVERECORD_OP_EQUALS',					'=');
define('ACTIVERECORD_OP_NOT_EQUALS',				'!=');
define('ACTIVERECORD_OP_GREATER_THAN',				'>');
define('ACTIVERECORD_OP_LESS_THAN',					'<');
define('ACTIVERECORD_OP_GREATER_THAN_EQUALS',		'>=');
define('ACTIVERECORD_OP_LESS_THAN_EQUALS',			'<=');
define('ACTIVERECORD_OP_LESS_THAN_GREATER_THAN',	'<>');
define('ACTIVERECORD_OP_BETWEEN',					' BETWEEN ');
define('ACTIVERECORD_OP_NOT_BETWEEN',				' NOT BETWEEN ');
class dbtools_activeRecord_selection extends overloader
{
	private $joins	=array();
	private $filters=array();
	
	public function __construct(dbtools_activeRecord_activeRecord $parent)
	{
		parent::__construct($parent);
	}
	
	public function join($table,$column1,$operator,$column2)
	{
		array_push
		(
			$this->joins,
			array
			(
				'table'		=>$table,
				'column1'	=>$column1,
				'operator'	=>$operator,
				'column2'	=>$column2
			)
		);
		return $this;
	}
	
	public function filter()
	{
		$args=func_get_args();
		if (is_array($args[0]))
		{
			for ($i=0,$j=count($args); $i<$j; $i++)
			{
				if (!isset($args[$i][0]))
				{
					$this->parent->exception('');
				}
				else if (!isset($args[$i][1]))
				{
					$this->parent->exception('');
				}
				else if (!isset($args[$i][2]))
				{
					$this->parent->exception('');
				}
				else
				{
					array_push($this->filters,$args[$i]);
				}
			}
		}
		else
		{
			if (!isset($args[0]))
			{
				$this->parent->exception('');
			}
			else if (!isset($args[1]))
			{
				$this->parent->exception('');
			}
			else if (!isset($args[2]))
			{
				$this->parent->exception('');
			}
			else
			{
				array_push($this->filters,$args);
			}
		}
		return $this;
	}
	
	public function execute()
	{
		$query='SELECT * FROM [PREFIX]'.$this->parent->getName();
		$numJoins=count($this->joins);
		if ($numJoins)
		{
			for ($i=0; $i<$numJoins; $i++)
			{
				$query	.=' LEFT JOIN [PREFIX]'.$this->joins[$i]['table'].' ON '
						.$this->joins[$i]['column1'].$this->joins[$i]['operator'].$this->joins[$i]['column2'];
			}
		}
		$numFilters=count($this->filters);
		if ($numFilters)
		{
			$filters=array();
			for ($i=0; $i<$numFilters; $i++)
			{
				if ($this->filters[$i][1]===ACTIVERECORD_OP_BETWEEN
				|| $this->filters[$i][1]===ACTIVERECORD_OP_NOT_BETWEEN)
				{
					$filters[]=$this->filters[$i][0].$this->filters[$i][1].'"'.$this->filters[$i][2].'" AND "'.$this->filters[$i][3].'"';
				}
				else
				{
					$filters[]=$this->filters[$i][0].$this->filters[$i][1].$this->filters[$i][2];
				}
			}
			$query.=' WHERE '.implode($filters,' AND ');
		}
		$this->component->database->c('core')->query($query.';');
		$results=$this->component->database->result();
		if (count($results))
		{
			return new dbtools_activeRecord_recordSet($this->parent,$results);
		}
		else
		{
			return new dbtools_activeRecord_recordSet($this->parent);
		}
	}
}
?>