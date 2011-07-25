<?php
/*
 *	Copyright (c) 2009 Timothy Chandler <tim@s3.net.au>
 *
 *	Permission is hereby granted, free of charge, to any person
 *	obtaining a copy of this software and associated documentation
 *	files (the "Software"), to deal in the Software without
 *	restriction, including without limitation the rights to use,
 *	copy, modify, merge, publish, distribute, sublicense, and/or sell
 *	copies of the Software, and to permit persons to whom the
 *	Software is furnished to do so, subject to the following
 *	conditions:
 *	
 *	The above copyright notice and this permission notice shall be
 *	included in all copies or substantial portions of the Software.
 *	
 *	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 *	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 *	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 *	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 *	OTHER DEALINGS IN THE SOFTWARE.
 *
 */
/**
 * PHP Internationalization.
 * 
 * This i18n class provides a means for developers to provide internationalization
 * in their PHP software.
 * 
 * This class will emulate {@link http://www.gnu.org/software/gettext/ gettext}, a common
 * internationalization standard. This class also acts as a wrapper for the system-level
 * gettext instance. So if gettext is present on the system, it will use that instead. 
 * 
 * @package i18n
 * @author Timothy Chandler <tim@s3.net.au>
 * @version 1.0
 * @since 15/09/2009
 */
class i18n
{
	private $files			=array();
	private $domains		=array();
	private $activeDomain	=null;
	private $defaultDomain	=array
	(
		'domain'		=>'messages',
		'path'			=>null,
		'charset'		=>'iso-8859-1'
	);
	
	public function __construct()
	{
		include_once('MOReader.php');
		
		$this->defaultDomain['locale']=dirname(__FILE__).'locale'.DIRECTORY_SEPARATOR;
		
		if (!function_exists('gettext'))
		{
			$GLOBALS['i18n']=$this;
			function gettext($message)
			{
				return $GLOBALS['i18n']->getText($message);
			}
			function ngettext($single,$plural,$number)
			{
				return $GLOBALS['i18n']->nGetText($single,$plural,$number);
			}
		}
		function _($message)
		{
			return gettext($message);
		}
		function __($single,$plural,$number)
		{
			return ngettext($single,$plural,$number);
		}
	}
	
	public function bindTextDomain($domain,$path,$charset=null)
	{
		$this->domains[$domain]				=$this->defaultDomain;
		$this->domains[$domain]['domain']	=$domain;
		$this->domains[$domain]['path']		=$path;
		if (!is_null($charset))
		{
			$this->domains[$domain]['charset']=$charset;
		}
		return $this;
	}
	
	public function setDomain($domain)
	{
		if (isset($this->domains[$domain]))
		{
			$this->activeDomain=$domain;
		}
		else
		{
			$this->exception('Unable to set domain to "'.$domain.'" because the domain has not been registered.');
		}
	}
	
	public function getText($message)
	{
		$reader	=$this->loadMoFile
		(
			$this->domains[$this->activeDomain]['application'],
			'fr_FR',
			$this->domains[$this->activeDomain]['domain']
		);
		$def	=$reader->getStringDef($message);
		if (!is_null($def['translation']))
		{
			return $def['translation'];
		}
		else
		{
			return $def['normal'];
		}
	}
	
	public function nGetText($message,$plural,$n)
	{
		$reader	=$this->loadMoFile
		(
			$this->domains[$this->activeDomain]['application'],
			'fr_FR',
			$this->domains[$this->activeDomain]['domain']
		);
		$pluralNumber=$reader->getPluralNumber();
		if ($pluralNumber!==1)
		{
			$def=$reader->getStringDef($message);
			if (!empty($def['pluralTranslation']))
			{
				$result=null;
				eval('$result=('.$reader->getPluralFormat().');');
				if ($result)
				{
					return $def['pluralTranslation'];
				}
				else if (!empty($def['translation']))
				{
					return $def['translation'];
				}
				else if (isset($def['normal']))
				{
					return $def['normal'];
				}
			}
		}
		return $message;
	}
	
	private function loadMoFile($locale,$domain)
	{
		$id=$locale.'.'.$domain;
		if (isset($this->files[$id]))
		{
			return $this->files[$id];
		}
		else
		{
			$file=$this->domains[$this->activeDomain]['path'].$locale._.'LC_MESSAGES'._.$domain.'.mo';
			if (is_file($file))
			{
				return $this->files[$id]=new MOReader($file,$this->domains[$this->activeDomain]['charset']);
			}
			else
			{
				$this->exception('Unable to locate locale file "'.$file.'". The file is either missing or corrupt.');
			}
		}
	}
}
?>