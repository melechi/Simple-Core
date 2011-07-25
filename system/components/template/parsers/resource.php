<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_resource extends template_parser
{
	private $resources=array();
	private $coreResources=array();
	
	public function registerParsers()
	{
		$this->parent->registerParser('resource','parseResourceTags',1);
		$this->parent->registerParser('resource','parseResouceDump',1);
		return true;
	}
	
	public function parseResourceTags($theContent=null)
	{
		if (!empty($theContent))
		{
			/* {CSS:<name>[:<priority>[:<core>]]}
			 * Save into:
			 * $this->resources[<type>][<name>]=<priority>;
			 * $this->coreResources[<type>][<name>]=<priority>;
			 */
			$this->parseTag('css',$theContent);
			$this->parseTag('js',$theContent);
		}
		return $theContent;
	}
	
	private function parseTag($type='',&$theContent)
	{
		$type=strtolower($type);
		$theMatches=array();
		if (@preg_match_all('@\{'.$type.':([\w\/]+)(:([\w]+))?(:([\w]+))?\}@i',$theContent,$theMatches))
		{
			for ($i=0,$j=count($theMatches[1]); $i<$j; $i++)
			{
				$resource='resources';
				//Concatination support.
				if (@strstr($theMatches[1][$i],'.'))
				{
					$fragments=@explode('.',$theMatches[1][$i]);
					if (!@end($fragments))@array_pop($fragments);
					if (!@reset($fragments))@array_shift($fragments);
					for ($k=0,$l=count($fragments); $k<$l; $k++)
					{
						if ($this->matchVar($fragments[$j]))
						{
							$fragments[$k]=$this->{$this->extractVar($fragments[$k])};
						}
					}
					$theMatches[1][$i]=@str_ireplace('.'.$type,'',@implode('',$fragments));
				}
				else
				{
					if ($this->matchVar($theMatches[1][$i]))
					{
						$theMatches[1][$i]=@str_ireplace('.'.$type,'',$this->{$this->extractVar($theMatches[1][$i])});
					}
				}
				if (!empty($theMatches[3][$i]))
				{
					if ($this->matchVar($theMatches[3][$i]))
					{
						$theMatches[3][$i]=$this->{$this->extractVar($theMatches[3][$i])};
					}
					if ($theMatches[3][$i]=='core')
					{
						$theMatches[3][$i]=0;
						$resource='coreResources';
					}
				}
				else
				{
					$theMatches[3][$i]=0;
				}
				if ($resource!='coreResources' && (!empty($theMatches[5][$i])))
				{
					if ($this->matchVar($theMatches[5][$i]))
					{
						$theMatches[5][$i]=$this->{$this->extractVar($theMatches[5][$i])};
					}
					$resource='coreResources';
				}
				$this->{$resource}[$type][$theMatches[1][$i]]=$theMatches[3][$i];
				$theContent=str_replace($theMatches[0][$i],'',$theContent);
			}
		}
		return true;
	}
	
	public function parseResouceDump($theContent=null)
	{
		if (!empty($theContent))
		{
			if (stristr($theContent,'{RESOURCEDUMP}'))
			{
				$dump='';
				//Core Resources first.
				if (isset($this->coreResources['css']))
				{
					arsort($this->coreResources['css'],SORT_NUMERIC);
					reset($this->coreResources['css']);
					while (list($key)=each($this->coreResources['css']))
					{
						$dump.='<link rel="stylesheet" type="text/css" href="'.$this->config->path->publicroot.'css/'.$key.'.css" />'."\r\n";
					}
				}
				if (isset($this->coreResources['js']))
				{
					arsort($this->coreResources['js'],SORT_NUMERIC);
					reset($this->coreResources['js']);
					while (list($key)=each($this->coreResources['js']))
					{
						$dump.='<script type="text/javascript" src="'.$this->config->path->publicroot.'js/'.$key.'.js"></script>'."\r\n";
					}
				}
				//Application resources second.
				if (isset($this->resources['css']))
				{
					arsort($this->resources['css'],SORT_NUMERIC);
					reset($this->resources['css']);
					while (list($key)=each($this->resources['css']))
					{
						$dump.='<link rel="stylesheet" type="text/css" href="'.$this->config->path->publicroot.'css/'.$key.'.css" />'."\r\n";
					}
				}
				if (isset($this->resources['js']))
				{
					arsort($this->resources['js'],SORT_NUMERIC);
					reset($this->resources['js']);
					while (list($key)=each($this->resources['js']))
					{
						$dump.='<script type="text/javascript" src="'.$this->config->path->publicroot.'js/'.$key.'.js"></script>'."\r\n";
					}
				}
				$theContent=str_replace('{RESOURCEDUMP}',$dump,$theContent);
			}
		}
		return $theContent;
	}
}
?>