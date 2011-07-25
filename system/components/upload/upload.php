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
 *** - UPLOAD HANDLER
 *** - Version 2.0
 ***********************************/
class component_upload extends component
{
	const EXCEPTION_NAME='Upload Handler Exception';

	public $fileErrors=array();
	public $movedFiles=array();
	private $mimeTypes=array
	(
		'image'=>array
		(
			'image/gif'=>IMAGETYPE_GIF,
			'image/jpeg'=>IMAGETYPE_JPEG,
			'image/pjpeg'=>IMAGETYPE_JPEG,
			'image/png'=>IMAGETYPE_PNG,
			'image/bmp'=>IMAGETYPE_BMP
		)
	);

	public function handle()
	{
		if (count($_FILES))
		{
			$this->checkErrors();
		}
		return true;
	}

	public function move($location=null,$theFiles=null)
	{
		$return=false;
		if ($_FILES)
		{
			if ($location)
			{
				if ($theFiles)
				{
					if (is_array($theFiles))
					{
						for ($i=0; $i<count($theFiles); $i++)
						{
							if (@is_uploaded_file($_FILES[$theFiles[$i]]['tmp_name']))
							{
								$fileDest=$location.$this->getFileExtention($_FILES[$theFiles[$i]]['name']);
								if (@move_uploaded_file($_FILES[$theFiles[$i]]['tmp_name'],$fileDest))
								{
									$this->movedFiles[]=$fileDest;
									$return=true;
								}
								else
								{
									$return=false;
									break;
								}
							}
						}
					}
					else
					{
						if (@is_uploaded_file($_FILES[$theFiles]['tmp_name']))
						{
							$fileDest=$location.$this->getFileExtention($_FILES[$theFiles]['name']);
							if (@move_uploaded_file($_FILES[$theFiles]['tmp_name'],$fileDest))
							{
								$this->movedFiles[]=$fileDest;
								$return=true;
							}
						}
					}
				}
				else
				{
					$is_=($location{strlen($location)-1}==_);//(strrpos($location,_,-1)===(strlen($location)-1));
					if($is_ && !is_dir($location))
					{
						$v=umask(0); //so that avoid permission problems.
						mkdir($location,true);
						chmod($location,0775); //drwxrwxr_x
						umask($v);
					}
					foreach ($_FILES as $file)
					{
						$fileDest=$is_?$location.$file['name']:$location.$this->getFileExtention($file['name']);
						if (@is_uploaded_file($file['tmp_name']))
						{
							if (@move_uploaded_file($file['tmp_name'],$fileDest))
							{
								$this->movedFiles[]=$fileDest;
								$return=true;
							}
							else
							{
								$return=false;
								break;
							}
						}
					}
				}
			}
		}
		return $return;
	}

	public function challangeMimeTypes($mimeTypes=null)
	{
		$return=false;
		if ($mimeTypes)
		{
			//TODO: Finish this method.
		}
		return $return;
	}

	public function getFileExtention($fileName=null)
	{
		$return=false;
		if ($fileName)
		{
			$return=@preg_replace('@[^\.]*(.*)@','$1',$fileName);
		}
		return $return;
	}

	/*** ERROR HANDLING ***/

	public function checkErrors()
	{
		if (count($_FILES))
		{
			foreach ($_FILES as $file)
			{
				//if ($file['error']>0)$this->error($file['name'],$this->translateError($file['error']));
				if ($file['error']>0)$this->error($file['name'],$file['error']);
			}
		}
		return true;
	}

	public function translateError($errorNumber=null)
	{
		$return=false;
		if ($errorNumber)
		{
			switch ($errorNumber)
			{
				case 1:		$return='The file was too large.';					break;
				case 2:		$return='The file was too large.';					break;
				case 3:		$return='The file was not fully uploaded.';			break;
				case 4:		$return='No file was uploaded.';					break;
			}
		}
		return $return;
	}

	public function error($theFile=null,$theError=null)
	{
		$return=false;
		if ($theFile && $theError)
		{
			$i=count($this->fileErrors);
			$this->fileErrors[$i]['file']=$theFile;
			$this->fileErrors[$i]['error']=$theError;
			$return=true;
		}
		return $return;
	}

	public function isError()
	{
		return (count($this->fileErrors))?true:false;
	}

	public function dumpErrors()
	{
		$return='<ul>';
		for ($i=0,$j=count($this->fileErrors); $i<$j; $i++)
		{
			$return.='<li><b>'.$this->fileErrors[$i]['file'].'</b>: '.$this->fileErrors[$i]['error'].'</li>';
		}
		return $return.'</ul>';
	}

	public function getErrors($translated=false)
	{
		$return=array();
		if (!$translated)
		{
			$return=$this->fileErrors;
		}
		else
		{
			for ($i=0,$j=count($this->fileErrors); $i<$j; $i++)
			{
				$return[$this->fileErrors[$i]['file']].=$this->translateError($this->fileErrors[$i]['error']);
			}
		}
		return $return;
	}
}
?>
