<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/***********************************
 * SIMPLE SITE SOLUTIONS
 **- SIMPLE CORE
 *** - DATABASE HANDLER
 *** - Version 2.0
 ***********************************/
/*FUTURE VERSION IDEAS
 * Priority Support
 * Caching - Now has to some extent.
 */
class component_database extends component
{
	const EXCEPTION_NAME='Database Exception';

	private $driver=array();
	public $connections=array();
	private $queries=array();
	private $lastLink=0;

	public function __call($theFunction,$args)
	{
		$return=false;
		if ($theFunction)
		{
			for ($i=0,$j=count($args); $i<$j; $i++)
			{
				if (!is_resource($args[$i]))
				{
					$args[$i]=(string)$args[$i];
				}
			}
			$driver=array_shift($args);
			if (@is_object($this->driver[$driver]))
			{
				if (!in_array
				(
					$theFunction,
					array
					(
						'connect',
						'pconnect',
						'close',
						'selectdb',
						'dbquery',
						'affectedrows',
						'numrows',
						'lastid',
						'fetcharray',
						'seek',
						'queryinfo',
						'errormessage'
					)
				))
				{
					$this->exception('Invalid function called after object check. The function probably hasn\'t been defined.');
				}
				$function=(string)$this->driver[$driver]->$theFunction;
				$return=call_user_func_array($function,$args);
				if (!$return && $theFunction!='errormessage')$this->connections[$driver]['error']=$this->errormessage($driver);
			}
			else
			{
				if (!parent::__call($theFunction,$args))
				{
					$this->exception('Database function does not exist in driver. Unable to proceed. The requested function'
												.' was "'.$theFunction.'". If you are sure that you have used the correct function'
												.' then it is likely that your database driver is out of date. Please check the'
												.' Simple Core website for the latest version.');
				}
			}
		}
		return $return;
	}

	private function loadDriver($theDriver=null)
	{
		$return=false;
		if ($theDriver)
		{
			$return=new config($theDriver);
			$return=$return->functions;
		}
		return $return;
	}

	public function newConnection($theConnection=null)
	{
		$return=false;
		$driverPath=dirname(__FILE__)._.'drivers'._;		//TODO: This is a quickfix, may want to clean it up later.
		if (!@is_null($theConnection))
		{
			if (@is_file($driverPath.$theConnection->type.'.xml'))
			{
				if (!$this->driver[(string)$theConnection['name']]=$this->loadDriver($driverPath.$theConnection->type.'.xml'))
				{
					$this->exception('Unable to load database driver. The file must be corrupt.<br />');
				}
				if (!$this->connections[(string)$theConnection['name']]['link']=$this->connect((string)$theConnection['name'],(string)$theConnection->host,(string)$theConnection->username,(string)$theConnection->password))
				{
					$this->exception('Failed to connect to database.<br />');
				}
				elseif (!$this->selectdb((string)$theConnection['name'],(string)$theConnection->database,$this->connections[(string)$theConnection['name']]['link']))
				{
					$this->exception('Failed to select the requested database.<br />');
				}
				else
				{
					$this->connections[(string)$theConnection['name']]['database']=(string)$theConnection->database;
					$this->connections[(string)$theConnection['name']]['prefix']=(string)$theConnection->prefix;
					$this->lastLink=(string)$theConnection['name'];
					$return=true;
				}
			}
			else
			{
				$this->exception('Unable to initiate database connection "'.$theConnection['name'].'". Could not find a driver for "'.$theConnection->type.'".');
			}
		}
		return $return;
	}

	public function useConnection($theConnection=null)
	{
		if (isset($this->connections[$theConnection]))
		{
			$this->lastLink=$theConnection;
			$this->selectdb($this->lastLink,$this->connections[$this->lastLink]['database'],$this->connections[$this->lastLink]['link']);
		}
		else
		{
			$this->exception('Unable to use database connection "'.$theConnection.'". The connection has not been established.');
		}
		return $this;
	}

	public function c($theConnection=null)
	{
		return $this->useConnection($theConnection);
	}

	public function setup($connections=null)
	{
		$driverPath=dirname(__FILE__)._.'drivers'._;		//TODO: This is a quickfix, may want to clean it up later.
		if (!is_array($connections))$connections=array($connections);
		for ($i=0,$j=count($connections); $i<$j; $i++)
		{
			if ($connections[$i] instanceof config)
			{
				$this->newConnection($connections[$i]);
			}
//			else
//			{
//				if (!$theConnection)
//				{
//					$this->exception('No connection details were given to database->setup.');
//				}
//				elseif (!$driverPath)
//				{
//					$this->exception('No driver path was given to database->setup.');
//				}
//			}
		}
		return true;
	}

	public function disconnect($linkName=null)
	{
		$return=false;
		if (is_string($linkName))
		{
			if (isset($this->connections[$linkName]['link']))
			{
				if ($this->close($linkName,$this->connections[$linkName]['link']))
				{
					$return=true;
				}
			}
		}
		return $return;
	}

	public function query($query=null,$linkID=null)
	{
		$return=false;
		if ($query)
		{
			if ($linkID)$this->lastLink=$linkID;
			if (@is_resource($this->connections[$this->lastLink]['link']) || @is_object($this->connections[$this->lastLink]['link']))
			{
				$query=@str_ireplace('[PREFIX]',$this->connections[$this->lastLink]['prefix'],$query);
				$this->connections[$this->lastLink]['result']['query']=$query;
				$this->connections[$this->lastLink]['result']['resource']=$this->dbquery($this->lastLink,$query,$this->connections[$this->lastLink]['link']);
				$this->connections[$this->lastLink]['result']['pointer']=0;
				$this->connections[$this->lastLink]['result']['set']=false;
				if (@stristr($query,'INSERT'))$this->connections[$this->lastLink]['result']['lastid']=$this->lastid($this->lastLink,$this->connections[$this->lastLink]['link']);
			}
			$this->error($this->lastLink);
			if (!$this->connections[$this->lastLink]['error'])
			{
				if (@is_resource($this->connections[$this->lastLink]['result']['resource']))
				{
					$this->connections[$this->lastLink]['result']['num']=$this->numrows($this->lastLink,$this->connections[$this->lastLink]['result']['resource']);
					$this->connections[$this->lastLink]['result']['affected']=$this->affectedrows($this->lastLink,$this->connections[$this->lastLink]['link']);
					if ($this->connections[$this->lastLink]['result']['num'])$return=$this;
				}
				elseif (@is_bool($this->connections[$this->lastLink]['result']['resource']))
				{
					if (@preg_match('@INSERT|DELETE@i',$query))
					{
						$this->connections[$this->lastLink]['result']['affected']=$this->affectedrows($this->lastLink,$this->connections[$this->lastLink]['link']);
						if ($this->connections[$this->lastLink]['result']['affected'])$return=true;
					}
					elseif (@stristr($query,'UPDATE'))
					{
						$matches=array();
						@preg_match('@Changed\:\ ([0-9]+)@i',$this->queryinfo($this->lastLink,$this->connections[$this->lastLink]['link']),$matches);
						if ((int)$matches[1])
						{
							$return=true;
						}
						elseif ((int)$matches[1]===0)
						{
							$return=1;
						}
					}
				}
			}
		}
		return $return;
	}

	public function lastInsertID($linkID=null)
	{
		$return=false;
		if ($linkID)$this->lastLink=$linkID;
		if (isset($this->connections[$this->lastLink]['result']['lastid']))
		{
			$return=$this->connections[$this->lastLink]['result']['lastid'];
		}
		return $return;
	}

	private function cacheQuery($query=null,$linkID=null)
	{
		$return=false;
		if ($linkID)$this->lastLink=$linkID;
		if ($query)
		{
			$this->queries[$this->lastLink][]=$query;
		}
		return $return;
	}

	public function loop($numeric=null,$linkID=null)
	{
		$return=false;
		if ($linkID)$this->lastLink=$linkID;
		if (@is_resource($this->connections[$this->lastLink]['link']))
		{
			if ($this->connections[$this->lastLink]['result']['pointer']<=@$this->connections[$this->lastLink]['result']['num'])
			{
				while ($return=$this->fetcharray($this->lastLink,$this->connections[$this->lastLink]['result']['resource'],(($numeric)?2:1)))
				{
					$this->moveLoop(1);
					break;
				}
			}
		}
		return $return;
	}

	private function moveLoop($direction=null,$linkID=null)
	{
		$return=false;
		switch ($direction)
		{
			case 0:		$this->connections[$this->lastLink]['result']['pointer']--;		break;
			case 1:		$this->connections[$this->lastLink]['result']['pointer']++;		break;
		}
		if ($this->connections[$this->lastLink]['result']['pointer']!==$this->connections[$this->lastLink]['result']['num'])
		{
			if ($linkID)$this->lastLink=$linkID;
			if ($this->seek($this->lastLink,$this->connections[$this->lastLink]['result']['resource'],$this->connections[$this->lastLink]['result']['pointer']))
			{
				$return=$this;
			}
		}
		return $return;
	}

	public function result()
	{
		$return=false;
		$args=func_get_args();
		//TODO: Test this further... could cause a bug.
		if (isset($this->connections[count($args)]['link']))
		{
			$this->lastLink=count($args);
			@array_pop($args);
		}
		if (@is_resource($this->connections[$this->lastLink]['result']['resource']))
		{
			if (!$this->connections[$this->lastLink]['result']['set'])
			{
				while ($this->connections[$this->lastLink]['result']['set'][]=$this->loop()){}
				if (!end($this->connections[$this->lastLink]['result']['set']))array_pop($this->connections[$this->lastLink]['result']['set']);
				if (!reset($this->connections[$this->lastLink]['result']['set']))array_shift($this->connections[$this->lastLink]['result']['set']);
			}
			if  (!count($args))
			{
				$matches=array();
				preg_match('/LIMIT\s+(\d+)(,(\d+))?;?$/',$this->connections[$this->lastLink]['result']['query'],$matches);
				if (isset($matches[1]) && (((int)@$matches[1]===1 && empty($matches[3])) || ((int)@$matches[1]===0 && (int)@$matches[3]===1)))
				{
					if (isset($this->connections[$this->lastLink]['result']['set'][0]))
					{
						$return=$this->connections[$this->lastLink]['result']['set'][0];
						if (is_array($return) && count(array_keys($return))===1)$return=reset($return);
					}
				}
				elseif (stristr($this->connections[$this->lastLink]['result']['query'],'limit 1')
				&& preg_match('@SELECT\s*(DISTINCT|DISTINCTROW)?\s*\`[\w\s]+\`@i',$this->connections[$this->lastLink]['result']['query']))
				{
					$key=array_keys($this->connections[$this->lastLink]['result']['set']);
					if (count($key)===1)
					{
						$return=$this->connections[$this->lastLink]['result']['set'][$key[0]];
					}
					else
					{
						$return=$this->connections[$this->lastLink]['result']['set'];
					}
				}
				elseif (preg_match_all('@SELECT\s*(DISTINCT|DISTINCTROW)\s*(\`([\w\s]+)\`\,\s*)|(\`([\w\s]+)\`\s*)+\s*FROM@i',$this->connections[$this->lastLink]['result']['query'],$matches) && !empty($matches[3][0]))
				{
					if (count($matches[3]) || count($matches[5]))
					{
						if (!end($matches[3]))		array_pop($matches[3]);
						if (!reset($matches[3]))	array_shift($matches[3]);
						if (!end($matches[5]))		array_pop($matches[5]);
						if (!reset($matches[5]))	array_shift($matches[5]);
						$workingMatches=@array_merge($matches[3],$matches[5]);
						if (count($workingMatches))
						{
							for ($i=0,$j=count($this->connections[$this->lastLink]['result']['set']); $i<$j; $i++)
							{
								for ($k=0,$l=count($workingMatches); $k<$l; $k++)
								{
									if (!empty($workingMatches[$k]))
									{
										$return[$workingMatches[$k]][]=$this->connections[$this->lastLink]['result']['set'][$i][$workingMatches[$k]];
									}
								}
							}
						}
					}
					if (count($return)==1)
					{
						$key=@array_keys($return);
						$return=$return[$key[0]];
					}
				}
				elseif (@preg_match('@SELECT\s*(COUNT\(\`\w+\`\))\s*FROM@i',$this->connections[$this->lastLink]['result']['query'],$matches))
				{
					$return=$this->connections[$this->lastLink]['result']['set'][0][$matches[1]];
				}
				if ($return===false)
				{
					//This causes results to be unpredictable.
//					if (count($this->connections[$this->lastLink]['result']['set'])==1)
//					{
//						$return=reset($this->connections[$this->lastLink]['result']['set']);
//					}
//					else
//					{
//						$return=$this->connections[$this->lastLink]['result']['set'];
//					}
					$return=$this->connections[$this->lastLink]['result']['set'];
				}
			}
			elseif (count($args)===1)
			{
				if (stristr($this->connections[$this->lastLink]['result']['query'],'limit 1') || stristr($this->connections[$this->lastLink]['result']['query'],'limit 0,1'))
				{
					foreach ($this->connections[$this->lastLink]['result']['set'] as $key=>$val)
					{
						if (!count($args))break;
						if ($key==$args[0])
						{
							$return=$val[$args[0]];
							break;
						}
					}
				}
				else
				{
					$return=array();
					foreach ($args as $argVal)
					{
						for ($i=0,$j=count($this->connections[$this->lastLink]['result']['set']); $i<$j; $i++)
						{
							foreach ($this->connections[$this->lastLink]['result']['set'][$i] as $key=>$val)
							{
								if ($argVal==$key)
								{
									$return[$i]=$val;
								}
							}
						}
					}
				}
			}
			elseif (count($args)>1)
			{
				$return=array();
				for ($i=0,$j=count($this->connections[$this->lastLink]['result']['set']); $i<$j; $i++)
				{
					foreach ($args as $argVal)
					{
						if (isset($this->connections[$this->lastLink]['result']['set'][$i][$argVal]))
						{
							$return[$i][$argVal]=$this->connections[$this->lastLink]['result']['set'][$i][$argVal];
						}
					}
				}
				if (stristr($this->connections[$this->lastLink]['result']['query'],'limit 1') || stristr($this->connections[$this->lastLink]['result']['query'],'limit 0,1'))
				{
					$return=reset($return);
				}
			}
		}
		return $return;
	}

	public function error($linkID=null)
	{
		if ($linkID)$this->lastLink=$linkID;
		$this->connections[$this->lastLink]['error']=$this->errormessage($this->lastLink);
		return $this->connections[$this->lastLink]['error'];
	}
}
?>
