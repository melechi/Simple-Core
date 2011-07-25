<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/***********************************
 * SIMPLE SITE SOLUTIONS
 **- SIMPLE CORE
 *** - PAGE HANDLER
 *** - Version 2.0
 ***********************************/
class component_page extends component
{
	const EXCEPTION_NAME='Page Capturing Exception';
	
	private $GZIPAvailable;
	private $GZIP=false;
	private $content;
	
	public function startPageCapture()
	{
		$return=false;
		if (@ob_start())
		{
			@ob_implicit_flush(0);
			$return=true;
		}
		return $return;
	}
	
	public function stopPageCapture()
	{
		$return=false;
		if ($this->content=@ob_get_contents())
		{
			if (@ob_end_clean())$return=true;
		}
		return $return;
	}
	
	public function flushCaptured()
	{
		$return=false;
		if (@ob_end_clean())$return=true;
		return $return;
	}
	
	public function outputPageCapture($compressionLevel=0)
	{
		$return=false;
		if ($this->GZIP)
		{
			header('Content-Encoding: gzip');
			die('HEY!!! (This is the page handler... check it out...)');
			$return=@gzencode($this->content,$compressionLevel);
		}
		else
		{
			$return=$this->content;
		}
		return $return;
	}
	
	public function isGZIPAvailable()
	{
		$return=false;
		if (empty($this->GZIPAvailable))
		{
			if(@preg_match('@gzip|deflate@i',$_SERVER['HTTP_ACCEPT_ENCODING']))
			{
				$this->GZIPAvailable=true;
				$return=true;
			}
		}
		else
		{
			$return=$this->GZIPAvailable;
		}
		return $return;
	}
	
	public function GZIPon()
	{
		$return=false;
		if (empty($this->GZIPAvailable))$this->isGZIPAvailable();
		if ($this->GZIPAvailable)
		{
			$this->GZIP=true;
			$return=true;
		}
		return $return;
	}
	
	public function GZIPoff()
	{
		$return=false;
		if ($this->GZIPAvailable)
		{
			$this->GZIP=false;
			$return=true;
		}
		return $return;
	}
}
?>