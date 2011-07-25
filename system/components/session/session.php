<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/***********************************
 * SIMPLE SITE SOLUTIONS Pty. Ltd.
 **- SIMPLE CORE
 *** - SESSION HANDLER
 *** - Version 2.1
 ***********************************/
class component_session extends component
{
	const EXCEPTION_NAME='Session Exception';
	
	private $__sessionName='simpleCore2';
	private $__sessionKey=array
	(
		'length'=>64,
		'chars'=>'abcdefghijklmnopqrstuvwxyz0123456789',
		'embed'=>'simpleCore'
	);
	private $__sessionID	=false;
	private $expireTime		=3600;
	private $sessionStarted	=false;
	
	public function initiate()
	{
		if (!isset($this->config->component->database->connection))
		{
			$this->exception('Unable to use session component. The session component depends on an'
							.' active "core" database connection but there is no "core" connection defined in the config.');
		}
		else
		{
			$this->expireTime=(int)$this->config->component->session->expiretime;
		}
		return true;
	}
	
	public function __get($theKey=null)
	{
		$return=false;
		if ($theKey=='component' || $theKey=='config')
		{
			$return=parent::__get($theKey);
		}
		if (!$return)
		{
			$return=$this->getVar($theKey);
			if ($this->is_serial($return))$return=unserialize(stripslashes($return));
//			var_dump($return);
		}
		return $return;
	}
	
	public function __set($theKey=null,$theVal=null)
	{
		//TODO: Implement a reserved word exception.
		$return=false;
		if  ($theKey)
		{
			if ($this->isReservedVar($theKey))
			{
				$return=parent::__set($theKey,$theVal);
			}
			elseif (is_array($theVal))
			{
				$return=$this->setVar($theKey,addslashes(serialize($theVal)));
			}
			else
			{
				$return=$this->setVar($theKey,$theVal);
			}
		}
		return $return;
	}
	
	public function __isset($theKey=null)
	{
		$return=false;
		if ($theKey)
		{
			$query=<<<SQL
			SELECT `session_var_id`
			FROM `[PREFIX]session_var`
			WHERE `session_var_key`='{$theKey}'
			AND `session_var_sessionid`='{$this->sessionID()}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))$return=true;
		}
		return $return;
	}
	
	public function __unset($theKey)
	{
		$return=false;
		if ($theKey)
		{
			$query=<<<SQL
			DELETE FROM `[PREFIX]session_var`
			WHERE `session_var_key`='{$theKey}'
			AND `session_var_sessionid`='{$this->sessionID()}';
SQL;
			if ($this->component->database->c('core')->query($query))$return=true;
		}
		return $return;
	}
	
	private function setVar($theKey=null,$theVal=null)
	{
		$return=false;
		if ($theKey)
		{
			if (isset($this->$theKey))
			{
				$query=<<<SQL
				UPDATE `[PREFIX]session_var`
				SET `session_var_val`='{$theVal}'
				WHERE `session_var_key`='{$theKey}'
				AND `session_var_sessionid`='{$this->sessionID()}'
				LIMIT 1;
SQL;
			}
			else
			{
				$query=<<<SQL
				INSERT INTO `[PREFIX]session_var` (
				`session_var_sessionid`,
				`session_var_key`,
				`session_var_val`
				)VALUES(
				'{$this->sessionID()}',
				'{$theKey}',
				'{$theVal}'
				);
SQL;
			}
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	private function getVar($theKey=null)
	{
		$return=false;
		if ($theKey)
		{
			$query=<<<SQL
			SELECT `session_var_val`
			FROM `[PREFIX]session_var`
			WHERE `session_var_key`='{$theKey}'
			AND `session_var_sessionid`='{$this->sessionID()}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$return=$this->component->database->result();
			}
		}
		return $return;
	}
	
	public function getAllVars()
	{
		$return=false;
		$query=<<<SQL
		SELECT `session_var_val`
		FROM `[PREFIX]session_var`
		WHERE `session_var_sessionid`='{$this->sessionID()}';
SQL;
		$result=$this->component->database->c('core')->query($query);
		if ($result)
		{
			$return=$this->component->database->result();
		}
		return $return;
	}
	
	public function sessionStarted()
	{
		return $this->sessionStarted;
	}
	
	public function destroy()
	{
		$return=false;
		$query=<<<SQL
		DELETE FROM `[PREFIX]session_var`
		WHERE `session_var_sessionid`='{$this->sessionID()}';
SQL;
		if ($this->component->database->c('core')->query($query))
		{
			$query=<<<SQL
			DELETE FROM `[PREFIX]session`
			WHERE `session_id`='{$this->sessionID}';
SQL;
			if ($this->component->database->query($query))
			{
				unset($_COOKIE[$this->__sessionName]);
				$return=$this->start();
			}
		}
		return $return;
	}
	
	public function sessionID()
	{
		$return=false;
		if (empty($this->__sessionID))
		{
			$return=$this->getSessionID();
		}
		else
		{
			$return=$this->__sessionID;
		}
		return $return;
	}
	
	function getSessionName()
	{
		return isset($_COOKIE[$this->__sessionName])?$_COOKIE[$this->__sessionName]:false;
	}
	
	private function is_serial($theValue=null)
	{
		$return=false;
		if (@is_string($theValue))
		{
			if (preg_match('@a:\d+:\{@',$theValue))
			{
				if (is_array(@unserialize($theValue)))$return=true;
			}
		}
		return $return;
	}
	
	public function start()
	{
		$return=false;
		if ($this->hasCookie() && $this->sessionID())
		{
			$return=true;
		}
		else
		{
			$key=$this->makeKey();
			if ($this->createCookie($key))
			{
				if ($this->insertNewSession($key))
				{
					$return=true;
				}
			}
		}
		$this->sessionStarted=$return;
		return $return;
	}
	
	private function hasCookie()
	{
		return(isset($_COOKIE[$this->__sessionName]))?true:false;
	}
	
	private function createCookie($theKey=null)
	{
		$return=false;
		if ($theKey)
		{
			if (@setcookie($this->__sessionName,$theKey,null,'/',null))$return=true;
			$_COOKIE[$this->__sessionName]=$theKey;
		}
		return $return;
	}
	
	private function makeKey()
	{
		$return=false;
//		$charMap=array
//		(
//			0=>'z',		1=>'y',		2=>'x',
//			3=>'w',		4=>'v',		5=>'u',
//			6=>'t',		7=>'s',		8=>'r',
//			9=>'q',
//			'a'=>'p',	'b'=>'o',	'c'=>'n',
//			'd'=>'m',	'e'=>'l',	'f'=>'k',
//			'g'=>'j',	'h'=>'i'
//		);
//		
		
		$ipElements=@implode('',@explode('.',$_SERVER['REMOTE_ADDR']));
		$keyElements=array();
		@sscanf($ipElements,'%2d%2d%2d%2d%2d%2d',$keyElements[0]
												,$keyElements[1]
												,$keyElements[2]
												,$keyElements[3]
												,$keyElements[4]
												,$keyElements[5]);
		if (!@end($keyElements))@array_pop($keyElements);
		for ($i=0; $i<count($keyElements); $i++)
		{
			//print $keyElements[$i].'::'.@chr($keyElements[$i]).'<br />';
			$keyElements[$i]=@chr($keyElements[$i]);
		}
		
		for ($i=0; $i<count($keyElements); $i++)
		{
			//print $keyElements[$i].'::'.@chr($keyElements[$i]).'<br />';
			$keyElements[$i]=@ord($keyElements[$i]);
		}
		$key=array();
		for ($i=0; $i<@$this->__sessionKey['length']; $i++)
		{
			$position=@mt_rand(0,@strlen($this->__sessionKey['chars']));
			$key[]=@substr($this->__sessionKey['chars'],($position-1),1);
		}
		$return=@implode('',$key);
		return $return;
	}
	
	private function insertNewSession($theKey=null)
	{
		$return=false;
		if ($theKey)
		{
			$time=time();
			$ip=@$_SERVER['REMOTE_ADDR'];
			$query=<<<SQL
			INSERT INTO `[PREFIX]session` (
			`session_key`,
			`session_ip`,
			`session_touched`
			)VALUES(
			'{$theKey}',
			'{$ip}',
			'{$time}'
			);
SQL;
			if ($this->component->database->c('core')->query($query))$return=true;
			$this->__sessionID=$this->component->database->lastInsertID();
		}
		return $return;
	}
	
	public function isExpired($expireTime=false)
	{
		return false;
		$return=false;
		if (!empty($_COOKIE[$this->__sessionName]))
		{
			if (!$expireTime)$expireTime=&$this->expireTime;
			$key=&$_COOKIE[$this->__sessionName];
			$query=<<<SQL
			SELECT session_touched
			FROM [PREFIX]session
			WHERE session_key='{$key}'
			LIMIT 1;
SQL;
			$this->component->database->c('core')->query($query);
			//if (((int)$this->component->database->c('core')->result()+(60*$expireTime))<time())
			if (((int)$this->component->database->c('core')->result()+$expireTime)<time())
			{
				$return=true;
			}
		}
		return $return;
	}
	
	public function updateTouched()
	{
		$return=false;
		if (!empty($_COOKIE[$this->__sessionName]))
		{
			$theKey=&$_COOKIE[$this->__sessionName];
			$time=time();
			$query=<<<SQL
			UPDATE `[PREFIX]session`
			SET `session_touched`='{$time}'
			WHERE `session_key`='{$theKey}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$query=<<<SQL
				SELECT `session_id`
				FROM `[PREFIX]session`
				WHERE `session_key`='{$theKey}'
				LIMIT 1;
SQL;
				if ($this->component->database->query($query))
				{
					$return=true;
					$this->__sessionID=$this->component->database->result();
				}
			}
		}
		return $return;
	}
	
	private function getSessionID()
	{
		$return=false;
		if (!$this->__sessionID)
		{
			if (isset($_COOKIE[$this->__sessionName]))
			{
				$key=$_COOKIE[$this->__sessionName];
				$query=<<<SQL
				SELECT `session_id`
				FROM `[PREFIX]session`
				WHERE `session_key`='{$key}'
				LIMIT 1;
SQL;
				if ($this->component->database->c('core')->query($query))
				{
					$return=$this->__sessionID=$this->component->database->result();
				}
			}
		}
		else
		{
			$return=$this->__sessionID;
		}
		return $return;
	}
	
	public function getExpireTime()
	{
		return $this->expireTime;
	}
}
?>