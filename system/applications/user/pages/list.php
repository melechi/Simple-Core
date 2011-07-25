<?php
class user_page_list extends page
{
	public function initiate()
	{
		$this->component->database->c('core')->query('SELECT * FROM [PREFIX]account LEFT JOIN user.user ON user_account_id=account_id;');
		if (!$records=$this->component->database->result())
		{
			$records=array();
		}
		$HTML='';
		for ($i=0,$j=count($records); $i<$j; $i++)
		{
			$HTML.=<<<HTML
			<tr>
				<td><input type="checkbox" name="account_delete[]" value="{$records[$i]['account_id']}" /></td>
				<td>{$records[$i]['account_id']}</td>
				<td>{$records[$i]['user_name_first']} {$records[$i]['user_name_last']}</td>
			</tr>
HTML;
		}
		$this->parent->setTemplateVar('RECORDS',$HTML,'LIST');
		$this->FORM_LIST=$this->parent->newFMLInstance('list');
	}
}
?>