<?php
class application_core_admin extends adminControlPanel
{
	public function initiate()
	{
		$this->connectModule('direct');
	}
}
?>