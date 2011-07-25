<?php
class sandbox_event_API_chat_main extends ext_direct_server_provider
{
	public function join()
	{
		$success			=false;
		$sessionID			=$this->component->session->sessionID();
		$username			='User '.$sessionID;
		$changeName			=true;
		$initialMessages	=array();
		if (!isset($this->component->session->userID))
		{
			$query=<<<SQL
			INSERT INTO [PREFIX]user
			(
				user_session_id,
				user_name,
				user_status
			)
			VALUES
			(
				'{$sessionID}',
				'User {$sessionID}',
				'idle'
			);
SQL;
			if ($this->component->database->c('chat')->query($query))
			{
				$this->component->session->userID=$this->component->database->lastInsertID();
				$success=true;
			}
		}
		else
		{
			$userID=$this->component->session->userID;
			$query=<<<SQL
			SELECT user_name
			FROM [PREFIX]user
			WHERE user_id={$userID}
			LIMIT 1;
SQL;
			if ($this->component->database->c('chat')->query($query))
			{
				$username=$this->component->database->result();
				$changeName=(preg_match('/^User \d+$/i',$username));
			}
			$success=true;
		}
		if ($success)
		{
			$initialMessages=$this->parent->getRecentMessages();
		}
		$this->respond
		(
			array
			(
				'success'			=>$success,
				'userName'			=>$username,
				'changeName'		=>$changeName,
				'initialMessages'	=>$initialMessages
			)
		);
	}
	
	public function send($message)
	{
		$timestamp	=time();
		$userID		=$this->component->session->userID;
		$query		=<<<SQL
		INSERT INTO [PREFIX]log
		(
			log_user_id,
			log_message,
			log_timestamp
		)
		VALUES
		(
			'{$userID}',
			'{$message}',
			'{$timestamp}'
		);
SQL;
		if ($this->component->database->c('chat')->query($query))
		{
			$this->respond(array('success'=>true));
		}
		else
		{
			$this->respond(array('success'=>false));
		}
	}
	
	public function setName($name='')
	{
		if (empty($name))$name='User '+$this->component->session->sessionID();
		$id=$this->component->session->userID;
		$query=<<<SQL
		UPDATE [PREFIX]user
		SET user_name="{$name}"
		WHERE user_id={$id};
SQL;
		if ($this->component->database->c('chat')->query($query))
		{
			$this->respond(array('success'=>true,'userName'=>$name));
		}
		else
		{
			$this->respond(array('success'=>false));
		}
	}
}
?>