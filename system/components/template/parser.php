<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class template_parser extends overloader
{
	abstract public function registerParsers();
	
	const REG_VAR			='\$\w*\:[\w]+(\[\w+\])*';
	const REG_VAR_EXTRACT	='\$(\w*)\:([\w]+(\[\w+\])*)';
	const REG_STRINGVALUE	='[\w\ \-\+]';
	
	public $parent=false;
	
	public function __construct($selfParent)
	{
		$this->parent=$selfParent;
		$this->registerParsers();
		return true;
	}
	
	public function setMyDir(){}
	
	public function __set($theVar=null,$theValue)
	{
		return $this->parent->__set($theVar,$theValue);
//		if ($theVar)
//		{
//			if (isset($this->$theVar))
//			{
//				$this->error('Unable to create template variable! "'.$theVar.'" has already been created. You MUST use updateVar() to change a value of a preset template variable.');
//			}
//			else
//			{
//				$this->_var[$theVar]=$theValue;
//			}
//		}
//		return true;
	}
	
	public function __get($theVar=null)
	{
		if ($theVar=='component')
		{
			return parent::__get($theVar);
		}
		else
		{
			return $this->parent->__get($theVar);
		}
//		$return=false;
//		if (isset($this->parent->$theVar))
//		{
//			$return=$this->parent->$theVar;
//		}
//		elseif ($this->isArray($theVar))
//		{
//			$fragments=array();
//			preg_match_all('@('.self::REG_STRINGVALUE.'+)?\[('.self::REG_STRINGVALUE.'+)\]+@i',$theVar,$fragments);
//			$arraySegment='';
//			for ($i=0,$j=count($fragments[2]); $i<$j; $i++)
//			{
//				if (is_numeric($fragments[2][$i]))
//				{
//					$arraySegment.="[{$fragments[2][$i]}]";
//				}
//				else
//				{
//					$arraySegment.="['{$fragments[2][$i]}']";
//				}
//			}
////			print "isset(\$this->parent->_var['{$fragments[1][0]}']$arraySegment);<br />";
//			eval("\$return=\$this->parent->_var['{$fragments[1][0]}']$arraySegment;");
//		}
//		else
//		{
//			$return=parent::__get($theVar);
//			if (!$return)
//			{
//				$this->error('Unable to get template variable. "'.$theVar.'" is not a registered variable.');
//			}
//		}
//		return $return;
	}
	
	public function __isset($theVar=null)
	{
		return $this->parent->__isset($theVar);
//		$return=false;
//		if (isset($this->parent->$theVar))
//		{
//			$return=true;
//		}
//		elseif ($this->isArray($theVar))
//		{
//			$fragments=array();
//			preg_match_all('@('.self::REG_STRINGVALUE.'+)?\[('.self::REG_STRINGVALUE.'+)\]+@i',$theVar,$fragments);
//			$arraySegment='';
//			for ($i=0,$j=count($fragments[2]); $i<$j; $i++)
//			{
//				if (is_numeric($fragments[2][$i]))
//				{
//					$arraySegment.="[{$fragments[2][$i]}]";
//				}
//				else
//				{
//					$arraySegment.="['{$fragments[2][$i]}']";
//				}
//			}
////			print "isset(\$this->parent->_var['{$fragments[1][0]}']$arraySegment);<br />";
//			eval("\$return=isset(\$this->parent->_var['{$fragments[1][0]}']$arraySegment);");
//		}
//		return $return;
	}
	
	public function __call($theMethod,$theArgs)
	{
		return $this->parent->__call($theMethod,$theArgs);
	}
	
	public function bindParent($templateCore=null)
	{
		if (!$templateCore instanceof component_template)
		{
			throw new templateError('Initiation Error! Attempt to load a parser failed.'
									.' "'.@get_class($templateCore).'" is not an instance of "component_template".');
		}
		else
		{
			$this->parent=$templateCore;
		}
		return true;
	}
	
	public static function isArray($theVar=null)
	{
		$return=false;
		if (!@empty($theVar))
		{
			if (@preg_match('/^[\w]+(\[\w+\])+$/',$theVar))$return=true;
		}
		return $return;
	}
	
	public function matchVar($theVar=null)
	{
		$return=false;
		if (@preg_match('/^'.self::REG_VAR.'$/',$theVar))
		{
			$return=true;
		}
		return $return;
	}
	
	public function returnVar($theVar=null)
	{
		$return=null;
		if (!@empty($theVar))
		{
			$extract=$this->extractVar($theVar);
			if (@is_array($extract))
			{
//				if (!isset($this->{$extract[0]}->{$extract[1]}))
//				{
//					$this->error('Unable to get template variable. SCOPE::'.$extract[0].'[\''.$extract[1].'\'] is not a registered variable in this scope.');
//				}
//				else
//				{
//					$return=$this->{$extract[0]}->{$extract[1]};
//				}
				if (!isset($this->{$extract[0]}->{$extract[1]}))
				{
					$return=null;
				}
				else
				{
					$return=$this->{$extract[0]}->{$extract[1]};
				}
			}
			else
			{
//				if (!isset($this->{$extract}))
//				{
//					$this->error('Unable to get template variable. SCOPE::global[\''.$extract.'\'] is not a registered variable in this scope.');
//				}
//				else
//				{
//					$return=$this->{$extract};
//				}
				if (!isset($this->{$extract}))
				{
					$return=null;
				}
				else
				{
					$return=$this->{$extract};
				}
			}
		}
		return $return;
	}
	
	public function extractVar($theVar=null)
	{
		$return=$theVar;
		$result=array();
		if (@preg_match('/^'.self::REG_VAR_EXTRACT.'$/',$theVar,$result))
		{
			if (empty($result[1]))
			{
				$return=$result[2];
			}
			else
			{
				$return=array($result[1],$result[2]);
			}
		}
		return $return;
	}
	
	//TODO: Make this better.
	public function evalBool($toEval=null)
	{
		if (@is_bool($toEval))
		{
			switch ($toEval)
			{
				case true:		$toEval='true';		break;
				case false:		$toEval='false';	break;
			}
		}
		return $toEval;
	}
	
	public function isBool($theVar=null)
	{
		$return=false;
		if (!@is_null($theVar))
		{
			if ($theVar=='true' || $theVar=='false')$return=true;
		}
		return $return;
	}
	
	public function escapeVars($theString=null)
	{
		return @str_replace('$','\$',$theString);
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