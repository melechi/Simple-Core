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
 *** - Error Handler
 *** - Version 1.0
 ***********************************/
final class core_error
{
	private $template=false;
	private $templateParsed=false;
	private $errors=false;
	
	public function __construct()
	{
		set_error_handler(array($this,'catchError'));
		return true;
	}
	
	public function catchError($eCode=null,$eString=null,$theFile=null,$theLine=null,&$scope=null)
	{
		global ${CORE};
		if ($this->loadTemplate('php'))
		{
			$noOutput=false;
			$code=false;
			switch ($eCode)
			{
				case E_STRICT:
				{
					$code='Strict Rule Error';
					//print 'STRICT: '.$eString.' in '.$theFile.' on line '.$theLine.'.<br />';
					$noOutput=true;
					break;
				}
//				case E_NOTICE:
//				{
//					//Used for cacher.
//					if ($eString=='Trying to get property of non-object')
//					{
//						//$trace=@debug_backtrace();
//						//print_r($trace);
//					}
//					break;
//				}
				case E_NOTICE:
				{
//					print 'NOTICE:: '.$eString.' in '.$theFile.' on line '.$theLine.'.<br />';
					$noOutput=true;
					break;
				}
				case E_NOTICE:				if(!$code)$code='Notice';
				case E_ERROR:				if(!$code)$code='Error';
				case E_WARNING:				if(!$code)$code='Warning';
				case E_PARSE:				if(!$code)$code='Parse Error';
				case E_CORE_ERROR:			if(!$code)$code='PHP Core Error';
				case E_CORE_WARNING:		if(!$code)$code='PHP Core Warning';
				case E_COMPILE_ERROR:		if(!$code)$code='PHP Compilation Error';
				case E_COMPILE_WARNING:		if(!$code)$code='PHP Compilation Warning';
				case E_USER_ERROR:			if(!$code)$code='User Thrown Error';
				case E_USER_WARNING:		if(!$code)$code='User Thrown Warning';
				case E_USER_NOTICE:			if(!$code)$code='User Thrown Notice';
				case E_RECOVERABLE_ERROR:	if(!$code)$code='Recoverable Error';
				{
					$debug='';
					if (${CORE}->config->debug)
					{
						$scope=print_r($scope,1);
						$debug=<<<DEBUG
	<tr>
		<td colspan="2" class="rowTitle">Debug Data Dump:</td>		
	</tr>
	<tr>
		<td colspan="2" class="rowInfo"><pre id="debugDump">{$scope}</pre></td>		
	</tr>
DEBUG;
					}
					$this->errors=<<<ERROR
<table id="errorTable">
	<tr>
		<td colspan="2">The following Error was caught:</td>
	</tr>
	<tr>
		<td class="rowTitle">Code: </td>
		<td class="rowInfo">{$code}</td>
	</tr>
	<tr>
		<td class="rowTitle">Message: </td>
		<td class="rowInfo">{$eString}</td>
	</tr>
	<tr>
		<td class="rowTitle">File: </td>
		<td class="rowInfo">{$theFile}</td>
	</tr>
	<tr>
		<td class="rowTitle">Line: </td>
		<td class="rowInfo">{$theLine}</td>
	</tr>
	{$debug}
</table>
ERROR;
					break;
				}
				default:
				{
					$this->errors='An Unknown Error.';
				}
			}
			if (!$noOutput)
			{
				if ($this->parseTemplate())$this->outputTemplate();
			}
		}
		return true;
	}
	
	private function loadTemplate($theTemplate=null)
	{
		global ${CORE};
		$return=false;
		if ($theTemplate)
		{
			if (@is_file(${CORE}->config->path->data.'error/'.$theTemplate.'.tpl'))
			{
				$this->template=@file_get_contents(${CORE}->config->path->data.'error/'.$theTemplate.'.tpl');
				if (!empty($this->template))$return=true;
			}
		}
		return $return;
	}
	
	private function parseTemplate()
	{
		global ${CORE};
		$return=false;
		if (!empty($this->template))
		{
			$fNr=array
			(
				'{VERSION}'=>${CORE}->version,
				'{ERRORS}'=>$this->errors
			);
			$this->template=@str_ireplace(@array_keys($fNr),@array_values($fNr),$this->template);
			$this->templateParsed=true;
			$return=true;
		}
		return $return;
	}
	
	public function outputTemplate($print=true)
	{
		$return=false;
		if ($this->templateParsed)
		{
			if ($print)
			{
				print $this->template;
				$return=true;
			}
			else
			{
				return $this->template;
			}
		}
		return $return;
	}
}
?>