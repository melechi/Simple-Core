<?php
/*
 * Simple Core 2.1.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class core_exception extends overloader
{
	public function __construct($parent,$exceptionMessage)
	{
		parent::__construct($parent);
		try
		{
			throw new Exception($exceptionMessage,0);
		}
		catch (Exception $exception)
		{
			//Do some work with the exception.
			$origen=$this->getOrigen($exception->getTrace());
			$this->getCodeSnapshot($origen);
			$parent->corelogger->critical
			(
				<<<INFO
File:		{$origen['file']}
Line:		{$origen['line']}
Class:		{$origen['class']}
Function:	{$origen['function']}
Start Line:	{$origen['startline']}
INFO
				,'exception'
			);
			//Now use the core application to present the exception :).
			try
			{
				$this->application->core->dryRun(array('exception'));
			}
			catch(Exception $e)
			{
				die('Core had a critical exception which could not be handled.');
			}
//			$this->application->core->bindToAddress('exception');
			$this->application->core->setTemplateVar('MESSAGE',			$exception->getMessage());
			$this->application->core->setTemplateVar('FILE_FILENAME',	$origen['file']);
			$this->application->core->setTemplateVar('FILE_LINE',		$origen['line']);
			$this->application->core->setTemplateVar('FILE_CLASS',		$origen['class']);
			$this->application->core->setTemplateVar('FILE_FUNCTION',	$origen['function']);
			$this->application->core->setTemplateVar('FILE_STARTLINE',	$origen['startline']);
			$this->application->core->setTemplateVar('FILE_SOURCE',		htmlentities($origen['source']));
			$this->application->core->setTemplateVar('DEBUG',			print_r($exception->getTrace(),true));
//			$this->application->core->setTemplateVar('BUFFER',			$buffer);
			
			//Execute the exception view.
			if (!$this->unitTesting)
			{
				$this->application->core->exception();
			}
			else
			{
				throw $exception;
			}
		}
		exit();
	}
	
	private function getCodeSnapshot(&$exception)
	{
		if ($exception['file']!='N/A')
		{
			$line=file($exception['file']);
			$startLine=0;
			$stopLine=$exception['line'];
			for ($i=$exception['line']; $i>1; $i--)
			{
				if (preg_match('@function +\w+\(@',$line[($i+1)]))
				{
					$startLine=($i+1);
					$stopLine=($exception['line']-($i+1));
					break;
				}
			}
			$exception['startline']=($startLine+1);
			$exception['source']=implode('',array_slice($line,$startLine,$stopLine));
		}
		return true;
	}
	
	private function getOrigen($theTrace=array())
	{
		$return=array
		(
			'file'		=>'N/A',
			'line'		=>'N/A',
			'class'		=>'N/A',
			'function'	=>'N/A',
			'startline'	=>'N/A',
			'source'	=>'N/A'
		);
		//Skip the first one because that is always this class.
		for ($i=1,$j=count($theTrace); $i<$j; $i++)
		{
			//The function name is always 'exception'. Find that and we find our origen.
			if ($theTrace[$i]['function']=='exception')
			{
				$return=array
				(
					'file'=>$theTrace[$i]['file'],
					'line'=>$theTrace[$i]['line'],
					'class'=>$theTrace[($i+1)]['class'],
					'function'=>$theTrace[($i+1)]['function']
				);
				break;
			}
		}
		return $return;
	}
}
?>