<?php
/*
 * Simple Core 2
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class dbtools_activeRecord extends branch
{
	public $xIncludeFolder='activeRecord';
	
	private $definitions=array();
	private $records	=array();
	
	public function initiate()
	{
		$this->xInclude(array('activeRecord','record','recordSet','selection'));
	}
	
	public function __get($theVar=null)
	{
		//Check if defined.
		if (!isset($this->definitions[$theVar]))
		{
			return parent::__get($theVar);
		}
		//Check if activated.
		elseif (!isset($this->records[$theVar]))
		{
			return $this->activate($theVar);
		}
		else
		{
			return $this->records[$theVar];
		}
	}
	
	public function register($name,$path)
	{
		if (isset($this->definitions[$name]))
		{
			$this->exception('Unable to register active record. The active record "'.$name.'" has already been defined from "'.$path.'".');
		}
		else if (!is_file($path))
		{
			$this->exception('Unable to register active record. Path to definition file "'.$path.'" is incorrect.');
		}
		else
		{
			$this->definitions[$name]=$path;
		}
		return true;
	}
	
	public function activate($name)
	{
		if (!isset($this->definitions[$name]))
		{
			$this->exception('Unable to activate active record. The active record "'.$name.'" has not been defined.');
		}
		elseif (!isset($this->records[$name]))
		{
			$this->records[$name]=$this->parseDefinition($name);
		}
		return $this->records[$name];
	}
	
	private function parseDefinition($name)
	{
		if (!isset($this->definitions[$name]))
		{
			$this->exception('Unable to parse active record definition. The active record "'.$name.'" has not been defined.');
		}
		else
		{
			$xml=simplexml_load_file($this->definitions[$name]);
			$this->records[$name]=new dbtools_activeRecord_activeRecord($this);
			$this->records[$name]	->setName((string)$xml->name)
									->setPrefix((string)$xml->prefix)
									->setDatabase((string)$xml->database);
			foreach ($xml->schema->column as $column)
			{
				$this->records[$name]->registerColumn
				(
					(string)$column['name'],
					(string)$column['type'],
					(string)$column['length'],
					(isset($column['primaryKey']))?(bool)(string)$column['primaryKey']:false,
					(isset($column['id']))?(bool)(string)$column['id']:false
				);
			}
			return $this->records[$name];
		}
	}
	
//	public function initServer(application $application,$scope,$serverDefPath='',$namespace='Ext.app')
//	{
//		return new ext_direct_server($this,$application,$scope,$serverDefPath,$namespace);
//	}
}
?>