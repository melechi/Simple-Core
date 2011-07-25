<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_scope
{
	public $name=false;
	private $_var=array();
	
	public function __construct($scopeName=null)
	{
		$return=false;
		if (@is_null($scopeName))
		{
			$this->error('Unable to initiate template scope object because the constructor was not given a namespace for the scope to operate under.');
		}
		else
		{
			@settype($this->_var,'object');
			$this->name=$scopeName;
		}
		return $return;
	}
	
	public function __isset($theVar=null)
	{
		$return=false;
		if (isset($this->_var->{$theVar}))
		{
			$return=true;
		}
		elseif (template_parser::isArray($theVar))
		{
			$fragments=explode('[',str_replace(array('[',']'),array('[\'','\']'),$theVar),2);
			eval('$return=isset($this->_var->{\''.$fragments[0].'\'}['.$fragments[1].');');
		}
		return $return;
	}
	
	public function __set($theVar=null,$theValue=null)
	{
		$return=false;
		if (!@is_null($theVar))
		{
			$this->_var->{$theVar}=$theValue;
			$return=$this->_var->{$theVar};
		}
		return $return;
	}
	
	public function setBound($theVar=null,&$theValue=null)
	{
		$return=false;
		if (!@is_null($theVar))
		{
			$this->_var->{$theVar}=&$theValue;
			$return=$this->_var->{$theVar};
		}
		return $return;
	}
	
	public function __get($theVar=null)
	{
		$return=false;
		
		if (template_parser::isArray($theVar))
		{
			$fragments=explode('[',$theVar,2);
			eval('$return=@$this->_var->{\''.$fragments[0].'\'}[\''.str_replace(']','\']',$fragments[1]).';');
		}
		elseif (!isset($this->_var->{$theVar}))
		{
			$this->error('Unable to get template variable. SCOPE::'.$this->name.'[\''.$theVar.'\'] is not a registered variable in this scope.');
		}
		else
		{
			$return=$this->_var->{$theVar};
		}
		return $return;
	}
	
	public function setArray($theVar=null,$index=null,$theValue=null)
	{
		$return=false;
		if (!@is_null($theVar))
		{
			if (!isset($this->_var->{$theVar}))$this->_var->{$theVar}=array();
			if (@is_null($index))
			{
				$this->_var->{$theVar}[]=$theValue;
				$return=true;
			}
			else
			{
				$this->_var->{$theVar}[$index]=$theValue;
				$return=true;
			}
		}
		return $return;
	}
	
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