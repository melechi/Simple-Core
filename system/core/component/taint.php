<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class notaint
{
	private $post=array();
	private $get=array();
	private $request=array();
	private $server=array();

	public function __construct()
	{
		if (!count($_POST) && !empty($GLOBALS['HTTP_RAW_POST_DATA']))
		{
			$this->post=json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
		}
		else
		{
			$this->post=$_POST;
		}
		$this->get=$_GET;
		$this->request=$_REQUEST;
		$this->server=$_SERVER;
		return true;
	}

	public function set($container=null,$key=null,$value=null)
	{
		$return=false;
		if (!empty($container) && !empty($key))
		{
			if (!is_array($this->{$container}))$this->{$container}=array();
			$this->{$container}[$key]=$value;
		}
		return $return;
	}

	public function &post()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('post',$args,$numArgs);
	}

	public function &get()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('get',$args,$numArgs);
	}

	public function &request()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('request',$args,$numArgs);
	}

	public function &server()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('server',$args,$numArgs);
	}

	public function &returnVar($varType=null,$args=array(),$numArgs=0)
	{
		$return=null;
		if (!$numArgs)
		{
			$return=&$this->{$varType};
		}
		else
		{
			$value='$value=&$this->'.$varType;
			for ($i=0; $i<$numArgs; $i++)
			{
				$value.='[\''.$args[$i].'\']';
			}
			eval($value.';');
			$return=$value;
		}
		return $return;
	}
}
class taint
{
	private $level=3;
	private $post=array();
	private $get=array();
	private $request=array();
	private $server=array();

	public function __construct($level=3)
	{
		if (!count($_POST) && !empty($GLOBALS['HTTP_RAW_POST_DATA']))
		{
			$this->post=json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
			if (!is_array($this->post))
			{
				$this->post=array($this->post);
			}
		}
		else
		{
			$this->post=$_POST;
		}
		$this->level	=$level;
		$this->get		=(count($_GET))?$_GET:array();
		$this->request	=(count($_REQUEST))?$_REQUEST:array();
		$this->server	=(count($_SERVER))?$_SERVER:array();
		$this->flagTainted($this->post);
		$this->flagTainted($this->get);
		$this->flagTainted($this->request);
		$this->flagTainted($this->server);
		return true;
	}

	public function set($container=null,$key=null,$value=null)
	{
		$return=false;
		if (!empty($container) && !empty($key))
		{
			if (!is_array($this->{$container}))$this->{$container}=array();
			$this->{$container}[$key]=array('tainted'=>true,'value'=>$value);
		}
		return $return;
	}

	public function setLevel($level=3)
	{
		$this->level=$level;
		return true;
	}

	public function getLevel()
	{
		return $this->level;
	}

	private function is_array($variable)
	{
		return (is_array($variable) && !isset($variable['tainted']))?true:false;
	}

	private function flagTainted(&$container)
	{
		foreach ($container as &$val)
		{
			if (is_array($val))
			{
				$this->flagTainted($val);
			}
			else
			{
				$val=array('tainted'=>true,'value'=>$val);
			}
		}
		return true;
	}

	private function untaint(&$variable)
	{
		if ($this->is_array($variable))
		{
			foreach ($variable as &$val)
			{
				$this->untaint($val);
			}
		}
		else
		{
			if (is_array($variable))//TODO: trace the problem with using isset($variable['tainted']).
			{
				//Clean it up.
				$this->clean($variable['value']);
				//Mark untainted.
				$variable=$variable['value'];
			}
		}
		return true;
	}

	private function clean(&$variable)
	{
		if ($this->level>0)
		{
			if(!get_magic_quotes_gpc()) $variable=addslashes($variable);
		}
		if ($this->level>1)
		{
			$variable=htmlentities($variable,ENT_NOQUOTES);
		}
		if ($this->level>2)
		{
			$variable=strip_tags($variable);
		}
		return true;
	}

	private function isTainted(&$variable)
	{
		$return=true;
		if ($this->is_array($variable))
		{
			$return=false;
			foreach ($variable as $val)
			{
				if ($this->isTainted($val))
				{
					$return=true;
					break;
				}
			}
		}
		else
		{
			$return=is_array($variable)?true:false;
		}
		return $return;
	}

	public function &post()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('post',$args,$numArgs);
	}

	public function &get()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('get',$args,$numArgs);
	}

	public function &request()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('request',$args,$numArgs);
	}

	public function &server()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		return $this->returnVar('server',$args,$numArgs);
	}

	public function &returnVar($varType=null,$args=array(),$numArgs=0)
	{
		$return=null;
		if (!empty($varType))
		{
			if (!$numArgs)
			{
				$this->untaint($this->{$varType});
				$return=&$this->{$varType};
			}
			else
			{
				if ($numArgs===1)
				{
					if (isset($this->{$varType}[$args[0]]))
					{
						if ($this->is_array($this->{$varType}[$args[0]]))
						{
							if ($this->isTainted($this->{$varType}[$args[0]]))
							{
								$this->untaint($this->{$varType}[$args[0]]);
							}
							$return=&$this->{$varType}[$args[0]];
						}
						else
						{
							if ($this->isTainted($this->{$varType}[$args[0]]))
							{
								$this->untaint($this->{$varType}[$args[0]]);
							}
							$return=&$this->{$varType}[$args[0]];
						}
					}
				}
				else
				{
					$value='@$value=&$this->'.$varType;
					for ($i=0; $i<$numArgs; $i++)
					{
						$value.='[\''.$args[$i].'\']';
					}
					eval($value.';');
					if ($this->is_array($value))
					{
						if ($this->isTainted($value))
						{
							$this->untaint($value);
						}
						$return=&$value;
					}
					else
					{
						if ($this->isTainted($value))
						{
							$this->untaint($value);
						}
						$return=&$value;
					}
				}
			}
		}
		return $return;
	}
//
//	private function exception($container,$variable)
//	{
//		trigger_error('',E_USER_WARNING);
//		return true;
//	}
}
?>
