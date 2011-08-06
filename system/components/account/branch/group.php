<?php
/*
 * Simple Core 2.3.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class account_group extends branch
{
	private $lastInsertID=false;
	
	public function add($groupName)
	{
		$return=false;
		$query=<<<SQL
		INSERT INTO [PREFIX]group
		(
			group_name,
			group_status
		)VALUES(
			'{$groupName}',
			'1'
		);
SQL;
		if($return=$this->component->database->c('core')->query($query))
		{
			$this->lastInsertID=$this->component->database->lastInsertID();
		}
		return $return;
	}
	
	public function getLastInsertID()
	{
		return $this->lastInsertID;
	}
	
	public function edit($groupID=null,$values=array())
	{
		$return=false;
		if ($groupID && count($values))
		{
			$update=array();
			$query='UPDATE [PREFIX]group SET ';
			foreach ($values as $key=>$val)
			{
				$update[].="$key='$val'";
			}
			$query.=@implode(', ',$update)." WHERE group_id='$groupID';";
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function delete($groupID)
	{
		$return=false;
		if ($groupID)
		{
			$query=<<<SQL
			DELETE FROM [PREFIX]group
			WHERE group_id='{$groupID}';
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function addAccount($params)
	{
		$return=false;
		if (count($params))
		{
			$query=<<<SQL
			INSERT INTO [PREFIX]group
			(
				gaccount_group_id,
				gaccount_account_id,
				gaccount_meta
			)VALUES(
				'{$params['gaccount_group_id']}',
				'{$params['gaccount_account_id']}',
				'{$params['gaccount_meta']}'
			);
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	public function emptyGroup($groupID)
	{
		$query=<<<SQL
		DELETE FROM [PREFIX]group
		WHERE gaccount_id='{$groupID}';
SQL;
		return $this->component->database->c('core')->query($query);
	}
	
	public function removeAccount($groupID,$gAccount_accountID)
	{
		$query=<<<SQL
		DELETE FROM [PREFIX]group
		WHERE gaccount_group_id='{$groupID}'
		AND gaccount_account_id='{$gAccount_accountID}';
SQL;
		return $this->component->database->c('core')->query($query);
	}
}
?>