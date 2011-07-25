<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class account_validation extends branch
{
//	public function __get($theVar)
//	{
//		$return=null;
//		if ($theVar=='component' || $theVar=='request')
//		{
//			$return=$this->parent->{$theVar};
//		}
//		return $return;
//	}
//	
	function emailExists($emailAddress=null)
	{
		$return=false;
		if ($emailAddress)
		{
			$query=<<<SQL
			SELECT `account_id`
			FROM `[PREFIX]account`
			WHERE `account_email`='{$emailAddress}';
SQL;
			$return=$this->component->database->c('core')->query($query);
		}
		return $return;
	}
	
	function account_checkConfirmationCode()
	{
		$return=false;
		if (count($_POST))
		{
			$query=<<<SQL
			SELECT 	`account_id`,
					`account_confirmationcode`,
					`account_status`
			FROM `[PREFIX]account`
			WHERE `account_confirmationcode`='{$_POST['account_confirmationcode']}'
			LIMIT 1;
SQL;
			//if ($this->component->database->c('core')->query($query) && $this->component->database->getNumRows())
			if ($this->component->database->c('core')->query($query))
			{
				$account=$this->component->database->dumpResults();
				$return['type']=$account['account_status'];
				$return['id']=$account['account_id'];
			}
		}
		return $return;
	}
}
?>