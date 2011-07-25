<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class templateError extends Exception
{
	/* OUTPUT ERROR
	 * Formats an error and presents it
	 * to the browser.
	 * 
	 * This function exits, so as a result
	 * does not return anything.
	 */
	
	public function outputError()
	{
		$debugTrace=print_r($this->getTrace(),1);
		print<<<OUTPUT
<h1>Simple Core</h1>
<hr />
<h1 style="color:red;">Template Error!</h1>
<p style="border:2px solid #616D7E;background-color:#6D7B8D;color:#FFF;padding:5px;">{$this->getMessage()}</p>
<h2>Debug Information</h2>
<textarea style="width:1000px;height:500px;">{$debugTrace}</textarea>
OUTPUT;
		exit();
	}
}
?>