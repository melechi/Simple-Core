<?php
class component_i18n extends component
{
	const EXCEPTION_NAME='Language Exception';
	
	private $files			=array();
	private $domains		=array();
	private $activeDomain	=null;
	private $defaultDomain	=array
	(
		'application'	=>null,
		'domain'		=>'messages',
		'path'			=>null,
		'charset'		=>'iso-8859-1'
	);
	
	public function initiate()
	{
		include_once('includes/MOReader.php');
		
		$this->defaultDomain['application']	=$this->application->{$this->config->defaultApplication};
		$this->defaultDomain['locale']		=$this->defaultDomain['application']->my->dir.'locale'._;
		
		if (!function_exists('gettext'))
		{
			function gettext($message)
			{
				global ${CORE};
				return ${CORE}->component->i18n->getText($message);
			}
			function ngettext($single,$plural,$number)
			{
				global ${CORE};
				return ${CORE}->component->i18n->nGetText($single,$plural,$number);
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
	
	public function bindTextDomain(application $application,$domain,$path,$charset=null)
	{
		$domainIndex							=$application->my->name.'.'.$domain;
		$this->domains[$domainIndex]			=$this->defaultDomain;
		$this->domains[$domainIndex]['domain']	=$domain;
		$this->domains[$domainIndex]['path']	=$application->my->dir.$path._;
		if (!is_null($charset))
		{
			$this->domains[$domain]['charset']=$charset;
		}
		return $this;
	}
	
	public function setDomain(application $application,$domain)
	{
		$domainIndex=$application->my->name.'.'.$domain;
		if (isset($this->domains[$domainIndex]))
		{
			$this->activeDomain=$domainIndex;
		}
		else
		{
			$this->exception('Unable to set domain to "'.$domainIndex.'" because the domain has not been registered.');
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
	
	private function loadMoFile(application $application,$locale,$domain)
	{
		$id=$application->my->name.'.'.$locale.'.'.$domain;
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