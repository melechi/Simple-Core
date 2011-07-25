<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_functions extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('functions','parseFunctions',1);
		$this->parent->registerParser('functions','parseFunction_count',1);
		$this->parent->registerParser('functions','parseFunction_empty',1);
		return true;
	}
	
	public function parseFunctions($theContent=null)
	{
		if (!empty($theContent))
		{
			$theContent=$this->parseFunction_count($theContent);
			$theContent=$this->parseFunction_empty($theContent);
		}
		return $theContent;
	}
	
	public function parseFunction_count($theContent=null)
	{
		if (!empty($theContent))
		{
			$matches=array();
			@preg_match_all('@COUNT\(([^\)]+)\)@i',$theContent,$matches);
			if (count($matches[1]))
			{
				for ($i=0,$j=count($matches[1]); $i<$j ;$i++)
				{
					if (!$this->matchVar($matches[1][$i]))
					{
						$this->error('Syntax error. "'.$matches[1][$i].'" is invalid.');
					}
					else
					{
						$thisVar=$this->extractVar($matches[1][$i]);
						$theContent=@str_replace($matches[0][$i],count($this->{$thisVar}),$theContent);
					}
				}
			}
		}
		return $theContent;
	}
	
	public function parseFunction_empty($theContent=null)
	{
		if (!empty($theContent))
		{
			$matches=array();
			@preg_match_all('@EMPTY\(([^\)]+)\)@i',$theContent,$matches);
			if (count($matches[1]))
			{
				for ($i=0,$j=count($matches[1]); $i<$j ;$i++)
				{
					if (!$this->matchVar($matches[1][$i]))
					{
						$this->error('Syntax error. "'.$matches[1][$i].'" is invalid.');
					}
					else
					{
						$thisVar=$this->extractVar($matches[1][$i]);
						if (!isset($this->{$thisVar}))
						{
							$theContent=@str_replace($matches[0][$i],'true',$theContent);
						}
						else
						{
							$theContent=@str_replace($matches[0][$i],((empty($this->{$thisVar})?true:false)),$theContent);
						}	
					}
				}
			}
		}
		return $theContent;
	}
}
?>