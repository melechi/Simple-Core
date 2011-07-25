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
 *** - CAPTCHA HANDLER
 *** - Version 2.0
 ***********************************/

class component_captcha extends component
{
	private $fontPath=false;
	private $defaultBGcolor='FFFFFF';
	private $theImage=array();
	private $imageProperties=array();
	private $lastImage=false;
	private $randomString=false;
	
	const EXCEPTION_NAME='Captcha Manager Exception';
	
	public function initiate()
	{
		return true;
	}
	
	public function newImage($imageName=null,$width=null,$height=null)
	{
		$return=false;
		if ($imageName && $width && $height)
		{
			$this->theImage[$imageName]=@imagecreate($width,$height);
			$this->imageProperties[$imageName]=array('width'=>$width,'height'=>$height);
			$this->setLastImage($imageName);
			$return=true;
		}
		return $return;
	}
	
	private function setLastImage($lastImage=null)
	{
		$return=false;
		if ($lastImage)
		{
			$this->lastImage=$lastImage;
			$return=true;
		}
		return $return;
	}
	
	public function lastImage($property=null)
	{
		if ($property)
		{
			return $this->imageProperties[$this->lastImage][$property];
		}
		else
		{
			return ($this->lastImage)?$this->theImage[$this->lastImage]:false;
		}
	}
	
	public function setOutputHeader($imageType=null)
	{
		switch ($imageType)
		{
			case 'jpg':
			case 'jpeg':
			case 1:
			{
				@header('Content-type: image/jpeg');
				break;
			}
			case 'gif':
			case 2:
			{
				@header('Content-type: image/gif');
				break;
			}
			case 'png':
			case 3:
			{
				@header('Content-type: image/png');
				break;
			}
			default:
			{
				@header('Content-type: image/jpeg');
				break;
			}
		}
		@header('Cache-Control: no-cache, must-revalidate');
		@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		return true;
	}
	
	private function generateRandom($minLength=null,$maxLength=null,$characterSet=null)
	{
		$return='';
		if (!$minLength || !$maxLength)
		{
			$length='6';
		}
		else
		{
			$length=@rand($minLength,$maxLength);
		}
		if (!$characterSet)$characterSet="ABCDEFGHJKLMNPRSTUVWXYZ123456789";
		for ($i=1; $i<$length; $i++)
		{
			$strposition=rand(1,strlen($characterSet));
			$return.=@substr($characterSet,$strposition,1);
		}
		return $return;
	}
	
	/* IMAGE COLOR
	 * Accepts 1 argument as the color.
	 * Accepted argument formats:
	 * - Hex (#FFFFFF)
	 * - True Color (255255255)
	 * -- True Color MUST BE 9 NUMBERS, NO SPACES!
	 * Function will set the color resource to the
	 * last used image.
	 */
	
	private function imageColor($color=null)
	{
		$return=false;
		if ($color)
		{
			$r=false;
			$g=false;
			$b=false;
			if (@preg_match('@\d{9}@i',$color))
			{
				@sscanf($color,'%3s%3s%3s',$r,$g,$b);
			}
			elseif (@preg_match('@(#)?\w{6}@i',$color))
			{
				@str_replace('#','',$color);
				@sscanf($color,'%2x%2x%2x',$r,$g,$b);
			}
			$return=@imagecolorallocate($this->lastImage(),$r,$g,$b);
		}
		return $return;
	}
	
	private function randomImageText($textFunction=null,$font=null,$color=null,$text=null)
	{
		$return=false;
		if ($textFunction && $text && $font && $color)
		{
			$oProperty=($this->lastImage('width')/@strlen($text));
			if ($oProperty>0)
			{
				for ($i=0, $o=15; $i<@strlen($text); $i++,$o+=$oProperty)
				{
					$fontSize=@mt_rand(14,20);
					$YpositionMin=($fontSize+5);
					$YpositionMax=($this->lastImage('height')-5);
					@$textFunction($this->lastImage()							//Resource Image
									,$fontSize									//Font Size
									,@mt_rand(0,50)								//Angle
									,$o											//X Position
									,@mt_rand($YpositionMin,$YpositionMax)		//Y Position
									,$color										//Font Color
									,$this->fontPath.$font						//Font Type
									,@substr($text,$i,1)						//Text Character
									);
				}
				$return=true;
			}
		}
		return $return;
	}
	
	public function outputImage($save=false,$imageType=null,$quality=null)
	{
		$return=false;
		switch ($imageType)
		{
			case 'jpg':
			case 'jpeg':
			case 1:
			{
				$function='imagejpeg';
				break;
			}
			case 'gif':
			case 2:
			{
				$function='imagegif';
				break;
			}
			case 'png':
			case 3:
			{
				$function='imagepng';
				break;
			}
			default:
			{
				$function='imagejpeg';
				break;
			}
		}
		if (!$save)
		{
			$this->setOutputHeader($imageType);
			if ($function=='imagejpeg')
			{
				if (!$quality)$quality=50;
				@$function($this->lastImage(),null,$quality);
			}
			else
			{
				@$function($this->lastImage());
			}
			$return=true;
		}
		else
		{
			if ($function=='imagejpeg')
			{
				if (!$quality)$quality=50;
				@$function($this->lastImage(),$save,$quality);
			}
			else
			{
				@$function($this->lastImage(),$save);
			}
			$return=true;
		}
		return $return;
	}
	
	public function genSpamCode($width=null,$height=null,$font=null,$color=null,$bgColor=null,$imageType=null,$fileSave=false)
	{
		$return=false;
		if ($width && $height && $font && $color)
		{
			if ($this->newImage('spamcode',$width,$height))
			{
				if (!$bgColor)$bgColor=$this->defaultBGcolor;
				$bgColor=$this->imageColor($bgColor);
				$color=$this->imageColor($color);
				$this->randomString=$this->generateRandom(6,8);
				$this->randomImageText('imagefttext',$font,$color,$this->randomString);
				if ($this->outputImage($fileSave,$imageType))$return=true;
				@imagedestroy($this->lastImage());
				$return=$this->randomString;
			}
		}
		return $return;
	}
	
	public function percentBar($width=null,$height=null,$percentage=null,$color1=null,$color2=null)
	{
		$return=false;
		if (@is_int($width) && @is_int($height) && @is_int($percentage))
		{
			if ($this->newImage('bar',$width,$height))
			{
				if (!$color1)$color1='41A317';
				if (!$color2)$color2='FF0000';
				$foreground=$this->imageColor($color1);
				$background=$this->imageColor($color2);
				$border=$this->imageColor('000000');
				$fillWidth=($percentage/100)*$width;
				imagefilledrectangle($this->lastImage(),0,0,$width,$height,$foreground);
				imagefilledrectangle($this->lastImage(),$fillWidth,0,$width,$height,$background);
				if ($this->outputImage(false,'jpg'))$return=true;
			}
		}
		return $return;
	}
}
?>