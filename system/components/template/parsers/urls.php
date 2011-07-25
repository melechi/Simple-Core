<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_urls extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('urls','parseURLs',2);
		return true;
	}
	
	public function parseURLs($theContent=null,application $application=null)
	{
		if (!empty($theContent))
		{
			$matches=array();
			preg_match_all('@\{URL\:([^\}]+)\}@i',$theContent,$matches);
			if (count($matches[1]))
			{
				if ($application)
				{
					for ($i=0,$j=count($matches[1]); $i<$j ;$i++)
					{
						$theContent=str_replace($matches[0][$i],$application->makeURL($matches[1][$i]),$theContent);
					}
				}
				else
				{
					for ($i=0,$j=count($matches[1]); $i<$j ;$i++)
					{
						$theContent=str_replace($matches[0][$i],$this->makeURL($matches[1][$i]),$theContent);
					}
				}
			}
		}
		return $theContent;
	}
}
?>