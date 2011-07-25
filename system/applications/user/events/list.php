<?php
class user_event_list extends event
{
	public function initiate()
	{
		if ($j=count($this->global->post('account_delete')))
		{
			$accountsToDelete=$this->global->post('account_delete');
			for ($i=0; $i<$j; $i++)
			{
				$this->component->account->delete($accountsToDelete[$i]);
			}
			$this->component->feedback->message('Users deleted.');
		}
		else
		{
			$this->component->feedback->error('Nothing was deleted because you did not select anthing to delete.');
		}
	}
}
?>