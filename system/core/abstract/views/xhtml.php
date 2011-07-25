<?php
abstract class view_xhtml extends page
{
	public function initiate()
	{
		$this->setContentType('application/xhtml+xml');
		
	}
}
?>