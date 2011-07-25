<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
//TODO: parseAll(); - This will be dynamic and could even be part of the core template class..
class component_template extends component
{
	const EXCEPTION_NAME='Template Handler Exception';
	
	private $_registry=array();
	public $_scope=null;
	public $_parser=null;
	//public $config=false;
	
	public function initiate()
	{
		//Include required template classes.
		include_once(@dirname(__FILE__)._.'error.php');
		include_once(@dirname(__FILE__)._.'scope.php');
		include_once(@dirname(__FILE__)._.'parser.php');
		
		//Set the parser and scope containers as objects.
		$this->_scope=new stdClass;
		$this->_parser=new stdClass;
		
		//Create a default scope for template variables to sit in.
		$this->newScope('global');
		
		//Create scopes for PHP Globals.
		$this->newScope('SERVER');
		$this->newScope('POST');
		$this->newScope('GET');
		$this->newScope('REQUEST');
		$this->newScope('FILES');
		foreach($this->parent->global->server()		as $key=>$val)	$this->SERVER->{$key}=$val;
		foreach($this->parent->global->post()		as $key=>$val)	$this->POST->{$key}=$val;
		foreach($this->parent->global->get()		as $key=>$val)	$this->GET->{$key}=$val;
		foreach($this->parent->global->request()	as $key=>$val)	$this->REQUEST->{$key}=$val;
//		foreach($_FILES		as $key=>$val)	$this->FILES->{$key}=$val;
		
		//Bind all the parsers.
		foreach (new DirectoryIterator(@dirname(__FILE__)._.'parsers'._) as $iteration)
		{
			if ($iteration->isFile())
			{
				include_once($iteration->getPath()._.$iteration->getFilename());
				$parserName=@str_replace('.php','',$iteration->getFilename());
				$className='template_parser_'.$parserName;
				$this->_parser->{$parserName}=new $className($this);
			}
		}
		//Map config.	-TODO: fix scope issue without the use of the global...
		global ${CORE};
		$this->_scope->global->config=${CORE}->config;
		return true;
	}
	
	public function registerParser($groupName=null,$callbackName=null,$numArgs=0)
	{
		$return=false;
		if (!empty($groupName) && !empty($callbackName) && @is_numeric($numArgs))
		{
			if (!isset($this->_registry[$groupName]))$this->_registry[$groupName]=array();
			$this->_registry[$groupName][$callbackName]=$numArgs;
		}
		return $return;
	}
	
	public function __call($theMethod,$args)
	{
		$return=false;
		if (@substr($theMethod,0,5)=='parse')
		{
			@reset($this->_registry);
			while (list($group,$array)=@each($this->_registry))
			{
				if (isset($this->_registry[$group][$theMethod]))
				{
					$return=@call_user_func_array(array($this->_parser->{$group},$theMethod),$args);
				}
			}
		}
		else
		{
			$return=parent::__call($theMethod,$args);
		}
		return $return;
	}
	
	public function __set($theVar=null,$theValue)
	{
		if (!@is_null($theVar))
		{
			$this->_scope->global->$theVar=$theValue;
		}
		return true;
	}
	
	public function __get($theVar=null)
	{
		$return=null;
		if (!isset($this->_scope->global->$theVar))
		{
			if ($theVar=='config')
			{
				$return=parent::__get($theVar);
			}
			elseif (!$this->scopeExists($theVar))
			{
				$return=null;
			}
			else
			{
				$return=$this->_scope->{$theVar};
			}
		}
		else
		{
			$return=$this->_scope->global->$theVar;
		}
		return $return;
	}
	
	public function __isset($theVar=null)
	{
		$return=false;
		if (isset($this->_scope->global->$theVar))
		{
			$return=true;
		}
//		elseif ($this->isArray($theVar))
//		{
//			$fragments=explode('[',$theVar,2);
//			eval('$return=isset($this->_scope->_var['.$fragments[0].']['.$fragments[1].');');
//		}
		return $return;
	}
	
	public function newScope($scopeName=null)
	{
		$return=false;
		if (!@is_null($scopeName))
		{
			if (!$this->scopeExists($scopeName))
			{
				$this->_scope->{$scopeName}=new template_scope($scopeName);
				$return=$this->_scope->{$scopeName};
			}
			else
			{
				$this->exception('Template handler was unable to create a new scope because the scope "'.$scopeName.'"'
								.' already exists. Use resetScope() or destroyScope() before calling newScope() '
								.' if the scope has already been created.');
			}
		}
		return $return;
	}
	
	public function scopeExists($scopeName)
	{
		$return=false;
		if (isset($this->_scope->{$scopeName}))
		{
			if ($this->_scope->{$scopeName} instanceof  template_scope)
			{
				$return=true;
			}
		}
		return $return;
	}
	
	public function resetScope($scopeName)
	{
		$return=false;
		if (isset($this->_scope->{$scopeName}))
		{
			$this->destroyScope($scopeName);
			$return=$this->newScope($scopeName);
		}
		else
		{
			$this->exception('Template handler was unable to reset the "'.$scopeName.'" scope because it didn\'t exist.');
		}
		return $return;
	}
	
	public function destroyScope($scopeName)
	{
		$return=false;
		if (isset($this->_scope->{$scopeName}))
		{
			unset($this->_scope->{$scopeName});
			$return=true;
		}
		else
		{
			$this->exception('Template handler was unable to destroy the "'.$scopeName.'" scope because it didn\'t exist.');
		}
		return $return;
	}
	
	/* ERROR
	 * Throws an error to the template exception class.
	 */
	 
	public function error($theError=null)
	{
		$return=false;
		if ($theError)
		{
			try
			{
				throw new templateError($theError);
			}
			catch (templateError $error)
			{
				$error->outputError();
			}
			$return=true;
		}
		return $return;
	}
}
?>