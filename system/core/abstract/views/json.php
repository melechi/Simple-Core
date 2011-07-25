<?php
abstract class view_json extends page
{
	public function initiate()
	{
		$this->setContentType('application/json');
		
	}
}
?>