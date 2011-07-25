<?php
class sandbox_event_API_updater extends ext_direct_server_provider
{
	public function initiate()
	{
		$messageCache=$this->component->session->messageCache;
		$messageCache=(is_array($messageCache))?implode($this->component->session->messageCache,','):'0';
		$time=(time()-300);
		$query=<<<SQL
		SELECT log_id
		FROM [PREFIX]log
		WHERE log_id NOT IN ({$messageCache})
		AND log_timestamp>{$time};
SQL;
		if ($this->component->database->c('chat')->query($query))
		{
			$result=$this->component->database->result('log_id');
			if (count($result))
			{
				$messages=$this->parent->getRecentMessages(implode($result,','));
				$this->fireEvent('onNewMessage',json_encode($messages));
			}
		}
		else
		{
			$this->fireEvent('onUpdateSuccess','Successfully polled at: '.date('g:i:s a'));
		}
	}
}
?>