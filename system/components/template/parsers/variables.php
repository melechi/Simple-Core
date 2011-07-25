<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_variables extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('variables','parseVariables',1);
		return true;
	}

	public function parseVariables($theContent=null)
	{
		$theMatches=array();
		if (preg_match_all('@\{('.self::REG_VAR.')\}@i',$theContent,$theMatches))
		{
			for ($i=0,$j=count($theMatches[1]); $i<$j; $i++)
			{
				$theVar=$this->returnVar($theMatches[1][$i]);
				$theContent=str_replace($theMatches[0][$i],$theVar,$theContent);
			}
		}
		return $theContent;
	}
}
?>