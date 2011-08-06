<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Account Component
 * 
 * @author Timothy Chandler
 * @version 2.0
 * @copyright Simple Site Solutions 26/11/2007
 */
class component_account extends component
{
	const EXCEPTION_NAME='Account Handler';
	
	private $lastInsertID=false;
	private $lastConfirmationCode=false;
	
	public function initiate()
	{
		$this->branch('authentication');
		$this->branch('validation');
		$this->branch('group');
		return true;
	}
	
	public function add($namespace='')
	{
		$return=false;
		if (count($this->global->post()))
		{
			$this->lastConfirmationCode=$this->generateRandom(64,128);
			$query=<<<SQL
			INSERT INTO [PREFIX]account
			(
			account_username,
			account_email,
			account_password,
			account_confirmationcode,
			account_lastip,
			account_namespace
			)VALUES(
			'{$this->global->post('account_username')}',
			'{$this->global->post('account_email')}',
			MD5('{$this->global->post('account_password')}'),
			'{$this->lastConfirmationCode}',
			'{$this->global->post('account_lastip')}',
			'{$namespace}'
			);
SQL;
			$return=$this->component->database->c('core')->query($query);
			if($return)$this->lastInsertID=$this->component->database->lastInsertID();
		}
		return $return;
	}
	
	public function edit($accountID=null,$values=array())
	{
		$return=false;
		if ($accountID && count($values))
		{
			$update=array();
			$query='UPDATE [PREFIX]account SET ';
			foreach ($values as $key=>$val)
			{
				if ($key=='key' || $key=='submit' || $key=='account_tandcagree')continue;
				$update[].="$key='$val'";
			}
			$query.=@implode(', ',$update)." WHERE account_id='$accountID';";
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function getLastInsertID()
	{
		return $this->lastInsertID;
	}
	
	public function delete($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			DELETE FROM [PREFIX]account
			WHERE account_id='{$accountID}';
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function changeStatus($accountID=null,$status=null)
	{
		$return=false;
		if ($accountID && $status)
		{
			$query=<<<SQL
			UPDATE [PREFIX]account
			SET account_status='{$status}'
			WHERE account_id='{$accountID}';
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function updateLastLogin($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			UPDATE [PREFIX]account
			SET account_lastlogin=CURRENT_TIMESTAMP
			WHERE account_id='{$accountID}';
SQL;
			$return=$this->component->database->c('core')->query($query);
			$this->storeLoginHistory($accountID);
		}
		return $return;
	}
	
	public function updateLastIP($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			UPDATE [PREFIX]account
			SET account_lastip='{$_SERVER['REMOTE_ADDR']}'
			WHERE account_id='{$accountID}';
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function isCurrentPassword($accountID=null,$thePassword=null)
	{
		$return=false;
		if ($accountID && $thePassword)
		{
			$query=<<<SQL
			SELECT account_id
			FROM [PREFIX]account
			WHERE account_id='{$accountID}'
			AND account_password='{$thePassword}'
			LIMIT 1;
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function getLastLogin($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			SELECT account_lastlogin
			FROM [PREFIX]account
			WHERE account_id='{$accountID}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$return=$this->component->database->result();
			}
		}
		return $return;
	}
	
	public function getAccountStatus($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			SELECT account_status
			FROM [PREFIX]account
			WHERE account_id='{$accountID}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$return=$this->component->database->result();
			}
		}
		return $return;
	}
	
	public function getLastConfirmationCode()
	{
		return $this->lastConfirmationCode;
	}
	
	public function get($get='*',$key=1,$value=1,$from=0,$to=1,$order='',$extra='')
	{
		$return=false;
		$limit=($from==0 && $to==1)?'1':(string)$from.','.(string)$to;
		$query=<<<SQL
		SELECT {$get}
		FROM [PREFIX]account
		WHERE {$key}='{$value}'
		{$extra}
		LIMIT {$limit}
		{$order};
SQL;
		if ($this->component->database->c('core')->query($query))
		{
			$return=$this->component->database->result();
		}
		return $return;
	}
	
	public function getPrivs($accountID=null)
	{
		$return=false;
		if ($accountID)
		{
			$query=<<<SQL
			SELECT (account_privs|0) AS account_privs
			FROM [PREFIX]account
			WHERE account_id='{$accountID}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$return=$this->component->database->result();
			}
		}
		return $return;
	}
	
	public function generateRandom($minLength,$maxLength)
	{
		$return='';
		$characters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$length=@mt_rand($minLength,$maxLength);
		for ($i=1; $i<$length; $i++)
		{
			$strposition=@mt_rand(1,@strlen($characters));
			$return.=@substr($characters,$strposition,1);
		}
		return $return;
	}

	private function storeLoginHistory($accountID=null)
	{
		$return=false;
		$sessionID=$this->component->session->sessionID();
		if ($accountID && $sessionID)
		{
			$query="INSERT INTO [PREFIX]account_session_history (account_id,session_id) VALUES ('{$accountID}','{$sessionID}');";
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
}
?>