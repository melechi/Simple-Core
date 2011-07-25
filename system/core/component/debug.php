<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
final class core_debug
{
	private $fileHandlers=array();
	
	public function __construct()
	{
		global ${CORE};
		$return=true;
		if (!isset(${CORE}->config->path->debug))
		{
			throw new ${CORE}->coreError('No debug path has been defined in the Simple Core config file.'
										.' The debug handler will not initiate without this path set.'
										.' Add <debug>{SYSTEM}debug'._.'</debug> within your <path></path>'
										.' config (if using xml as your config file.');
			$return=true;
		}
		return $return;
	}
	
	public function __destruct()
	{
		$this->closeAllFileHandlers();
		return true;
	}
	
	public function dump($type=null)
	{
		global ${CORE};
		$return=false;
		if ($type)
		{
			switch ($type)
			{
				case 'requestToFile':
				{
					
					break;
				}
			}
		}
		return $return;
	}
	
	public function dumpRequestToFile($theDebugSet=null)
	{
		global ${CORE};
		$return=false;
		$debugDir=$this->handleDebugSet($theDebugSet);
		$debugDir=${CORE}->config->path->debug;
		if ($theDebugSet)$debugDir.=ltrim($theDebugSet,_);
		$endCharacter=substr($debugDir,strlen($debugDir)-1,1);
		if ($endCharacter!='/' && $endCharacter!='\\')$debugDir.=_;
		if ($this->createNewFileHandlers($debugDir,'post.txt','get.txt'))
		{
			$post=(${CORE}->class->io->is_empty('POST'))?'Array()':${CORE}->class->io->dump('POST');
			$get=(${CORE}->class->io->is_empty('GET'))?'Array()':${CORE}->class->io->dump('GET');
			if (!fwrite($this->fileHandlers['post.txt'],$post))
			{
				throw new ${CORE}->coreError('debug::dumpRequestToFile() was unable to write to open file handler for post.txt.');
			}
			elseif (!fwrite($this->fileHandlers['get.txt'],$get))
			{
				throw new ${CORE}->coreError('debug::dumpRequestToFile() was unable to write to open file handler for get.txt.');
			}
			else
			{
				$return=true;
			}
		}
		return $return;
	}
	
	public function dumpThisToFile($theThis=null,$theFile=null,$theDebugSet=null)
	{
		$return=false;
		$debugDir=$this->handleDebugSet($theDebugSet);
		if ($theThis && $theFile)
		{
			$this->createNewFileHandlers($debugDir,$theFile);
			if (!fwrite($this->fileHandlers[$theFile],$theThis."\r\n"))
			{
				die('debug::dumpThisToFile() was unable to write to open file handler for '.$theFile.'.');
				//throw new ${CORE}->coreError('debug::dumpThisToFile() was unable to write to open file handler for '.$theFile.'.');
			}
			else
			{
				$return=true;
			}	
		}
		return $return;
	}
	
	private function handleDebugSet($theDebugSet=null)
	{
		global ${CORE};
		if ($theDebugSet)
		{
			$theDebugSet=${CORE}->config->path->debug.ltrim($theDebugSet,_);
		}
		else
		{
			$theDebugSet=${CORE}->config->path->debug;
		}
		$endCharacter=substr($theDebugSet,strlen($theDebugSet)-1,1);
		if ($endCharacter!='/' && $endCharacter!='\\')$theDebugSet.=_;
		return $theDebugSet;
	}
	
	private function createNewFileHandlers($directory=null)
	{
		global ${CORE};
		$return=false;
		$args=func_get_args();
		$numArgs=func_num_args();
		if ($directory && ($numArgs-1))
		{
			if (!strstr(addslashes($directory),addslashes(${CORE}->config->path->debug)))
			{
				throw new ${CORE}->coreError('Not allowed to dump debug data outside of the configured debug directory.<br />'
											.'<b>GIVEN DIRECTORY:</b> '.$directory.'<br />'
											.'<b>DEBUG DIRECTORY:</b> '.${CORE}->config->path->debug);
			}
			else
			{
				$directoryValid=true;
				if (!is_dir($directory))
				{
					if (!mkdir($directory))
					{
						throw new ${CORE}->coreError('Debug directory was valid but did not exist.'
													.' Simple Core attempted to create it but failed.');
						$directoryValid=false;
					}
				}
				if ($directoryValid)
				{
					for ($i=1; $i<$numArgs; $i++)
					{
						if (!isset($this->fileHandlers[$args[$i]]))
						{
							if(!$this->fileHandlers[$args[$i]]=fopen($directory.$args[$i],'a'))
							{
								die('TODO: Update this.');
//								throw new ${CORE}->coreError('debug::createNewFileHandlers() was unable to'
//															.' open "'.$directory.$args[$i].'".');
								break;
							}
						}
					}
					$return=true;
				}
			}
		}
		return $return;
	}
	
	private function closeAllFileHandlers()
	{
		while (list($key,)=each($this->fileHandlers))
		{
			fclose($this->fileHandlers[$key]);
		}
	}
}
?>