<?php
class user_user extends branch
{
	public function add($accountID)
	{
		$query=<<<SQL
		INSERT INTO user
		(
			user_account_id,
			user_name_first,
			user_name_last
		)VALUES(
			'{$accountID}',
			'{$this->global->post('user_name_first')}',
			'{$this->global->post('user_name_last')}'
		);
SQL;
		return $this->component->database->c('user')->query($query);
	}
}
?>