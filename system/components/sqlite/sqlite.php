<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * A Simple SQLite database handler.
 * 
 * This SQLite handler has been thrown togeather relatively quickly.
 * It doesn't support anything overly advanced and actually moved away from
 * the Simple Core design pattern by implementing a factory pattern.
 * 
 * @author Timothy Chandler
 * @version 1.0
 * @copyright Simple Site Solutions 05/12/2007
 */
class component_sqlite extends component
{
	const EXCEPTION_NAME='SQLite Exception';
	
	private $connections=array();
	
	public function initiate()
	{
		$this->xInclude('instance');
		return true;
	}
	
	public function __get($theVar)
	{
		$return=false;
		if ($theVar=='component' || $theVar=='config')
		{
			$return=parent::__get($theVar);
		}
		elseif (isset($this->connections[$theVar]))
		{
			$return=$this->connections[$theVar];
		}
		else
		{
			$this->exception('Unable to return SQLite connection. Connection "'.$theVar.'" does not exist.');
		}
		return $return;
	}
	
	public function newConnection($database=null,$persistant=false)
	{
		$return=false;
		if (empty($database))
		{
			$this->exception('Unable to create a new SQLite connection. First parameter was empty.');
		}
		elseif (isset($this->connections[$database]))
		{
			$return=$this->connections[$database];
		}
		else
		{
			$this->connections[$database]=new sqlite_instance($database,$persistant);
			$return=$this->connections[$database];
		}
		return $return;
	}
}
?>