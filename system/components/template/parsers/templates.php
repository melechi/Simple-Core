<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_templates extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('templates','parseTemplateTags',1);
		return true;
	}
	
	public function parseTemplateTags($theContent=null)
	{
		$theMatches=array();
		if (preg_match_all('/\{TEMPLATE:([^}]+)\}/i',$theContent,$theMatches))
		{
			//The size of the templateTags array will grow, so ALWAYS count.
			for ($i=0; $i<count($theMatches[1]); $i++)
			{
				$theMatches[1][$i]=str_ireplace('.tpl','',$theMatches[1][$i]);
				//Handle concatination.
				if (strstr($theMatches[1][$i],'.'))
				{
					$fragments=explode('.',$theMatches[1][$i]);
					if (!end($fragments))array_pop($fragments);
					if (!reset($fragments))array_shift($fragments);
					for ($j=0,$k=count($fragments); $j<$k; $j++)
					{
						if ($this->matchVar($fragments[$j]))
						{
							$fragments[$j]=$this->{$this->extractVar($fragments[$j])};
						}
					}
					$theMatches[1][$i]=str_ireplace('.tpl','',implode('',$fragments));
				}
				else
				{
					if ($this->matchVar($theMatches[1][$i]))
					{
						$theMatches[1][$i]=str_ireplace('.tpl','',$this->{$this->extractVar($theMatches[1][$i])});
					}
				}
//				print 'THIS MATCH: '.$theMatches[1][$i]."<br />\r\n";
				//TODO: Patch very likely recursion issue.
				if (!$this->isTemplate($theMatches[1][$i]))
				{
					$this->error('Template "'.$theMatches[1][$i].'.tpl" could not be found. It is either missing or corrupt.');
				}
				else
				{
					//Load the template into memory.
					$thisTemplate=$this->loadTemplate($theMatches[1][$i]);
					//Parse any conditionals it may have in it.
					$thisTemplate=$this->parseFunctions($thisTemplate,strtolower($theMatches[1][$i]));
					$thisTemplate=$this->parseVariables($thisTemplate,strtolower($theMatches[1][$i]));
					$thisTemplate=$this->parseLoops($thisTemplate,strtolower($theMatches[1][$i]));
					$thisTemplate=$this->parseConditionals($thisTemplate,strtolower($theMatches[1][$i]));
					//TODO: Fix an issue calling parseURL form templates.php - crashes.
//					$thisTemplate=$this->parseURLs($thisTemplate,strtolower($theMatches[1][$i]));
					
					//Find any TEMPLATE tags it may have in it and add it to the array we're currently looping though.
					$newMatches=array();
					if (preg_match_all('/\{TEMPLATE:([^}]+)\}/i',$thisTemplate,$newMatches))
					{
						$theMatches[0]=array_merge($theMatches[0],$newMatches[0]);
						$theMatches[1]=array_merge($theMatches[1],$newMatches[1]);
					}
					//Finally, replace the template tag that we're currently using, with the loaded content.
					$theContent=str_replace($theMatches[0][$i],$thisTemplate,$theContent);
				}
//				if (count(array_keys($templateTags[1],$templateTags[1][$i]))>10)
//				{
//					$this->error('To much recursion! "'.strtolower($templateTags[1][$i]).'.tpl" was loaded more then 10 times.');
//				}
			}
		}
		return $theContent;
	}
	
	/* IS TEMPLATE
	 * Checks if a given template string exists as
	 * a file in the filesystem.
	 */
	
	private function isTemplate($theTemplate=null)
	{
		return (is_file($theTemplate.'.tpl'));
	}
	
	/* LOAD TEMPLATE
	 * Load a template into memory and return it.
	 */
	
	public function loadTemplate($theTemplate=null)
	{
		$return=false;
		if ($theTemplate)
		{
			$return=file_get_contents($theTemplate.'.tpl');
		}
		return $return;
	}
}
?>