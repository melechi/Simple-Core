<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_loops extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('loops','parseLoops',1);
//		$this->parent->registerParser('loops','parseEachLoops',1);
		return true;
	}

	public function parseLoops($theContent=null)
	{
		if (!empty($theContent))
		{
			$theMatches=array();
			@preg_match_all('@\{EACH\:('.self::REG_VAR.')=([\w\*]+)\}(.*?)\{ENDEACH\}@is',$theContent,$theMatches);
			if(!empty($theMatches[0]))
			{
				$this->parseEachLoops($theContent,$theMatches);
			}
			$theMatches=array();
			@preg_match_all('@\{REPEAT\:('.self::REG_VAR.')\}(.*?)\{ENDREPEAT\}@is',$theContent,$theMatches);
			if(!empty($theMatches[0]))
			{
				$this->parseRepeats($theContent,$theMatches);
			}
		}
		return $theContent;
	}

	private function parseRepeats(&$theContent,$theMatches=null)
	{
		if (!empty($theContent))
		{
			if(!is_array($theMatches))
			{
				$theMatches=array();
				@preg_match_all('@\{REPEAT\:('.self::REG_VAR.')\}(.*?)\{ENDREPEAT\}@is',$theContent,$theMatches);
			}
			if (!empty($theMatches[1]))
			{
				for ($i=0,$j=count($theMatches[0]); $i<$j; $i++)
				{
					$result='';
					if (!$this->matchVar($theMatches[1][$i]))
					{
						$this->error('Syntax error. "'.$theMatches[1][$i].'" is invalid.');
					}
					else
					{
						$thisVar=$this->extractVar($theMatches[1][$i]);
						$l=(int)$this->{$thisVar};
						for($k=0,$l;$k<$l; $k++)
						{
							$result.=$theMatches[3][$i];
						}
					}
					$theContent=@str_replace($theMatches[0][$i],$result,$theContent);
				}
			}
		}
		return true;
	}

	private function parseEachLoops(&$theContent,$theMatches=null)
	{
		if (!empty($theContent))
		{
			if(!is_array($theMatches))
			{
				$theMatches=array();
				@preg_match_all('@\{EACH\:('.self::REG_VAR.')=([\w\*]+)\}(.*?)\{ENDEACH\}@is',$theContent,$theMatches);
			}
			if (!empty($theMatches[1]))
			{
				for ($i=0,$j=count($theMatches[1]); $i<$j; $i++)
				{
					$result='';
					if (!$this->matchVar($theMatches[1][$i]))
					{
						$this->error('Syntax error. "'.$theMatches[1][$i].'" is invalid.');
					}
					else
					{
						$thisVar=$this->extractVar($theMatches[1][$i]);
						if (is_array($this->{$thisVar}))
						{
							$arrayElements=array();
							/*
							 * This next regular expression will extract special tags with a syntax of {[TOKEN]:[MEMBER]}
							 * where TOKEN is taken from the loop constructor {EACH:[ARRAY]=[TOKEN]} and represents the
							 * current member of the array based on the iteration.
							 */
							@preg_match_all('@\{\\'.$theMatches[3][$i].'\:?([\w]+)?\}@i',$theMatches[4][$i],$arrayElements);
							$k=0;$l=count($this->{$thisVar});
							if($k<$l)
							{
								// The following gathers the repeat blocks, into $repeater, starting from the outer-most (nested) repeats.
								$repeats=array();
								$repeater=array();
								$subject=$theMatches[4][$i];
								while(@preg_match('@(.*?)(\{REPEAT\:)([\$\:\w\d]+)\}(.*\1*?)(\{ENDREPEAT\})(.*)@is',$subject,$repeats))
								{
									if(!empty($repeats[2]) && is_string($repeats[2]))
									{
										$repeater[]=$repeats;
										$subject=$repeats[4];
									}
								}
								// The following replaces the repeating blocks with parsed content.
								if(!empty($repeater))
								{
									$recursions=array();
									for($m=0,$n=min(2,count($repeater)); $m<$n; $m++)
									{
										$x=$repeater[$m][3];
										if(!is_numeric($x) && $this->matchVar($x))
										{
											$x=$this->extractVar($x);
											$x=(int)$this->{$x};
										}
										if($x>0)
										{
											$recursions[$m]['x']=$x;
											$recursions[$m][0]= $repeater[$m][0];
											$recursions[$m]['search']= $repeater[$m][2].$repeater[$m][3].'}'.$repeater[$m][4].$repeater[$m][5];
											$recursions[$m]['replaceable']= $repeater[$m][4];
											$recursions[$m]['prefix']= $repeater[$m][1];
											$recursions[$m]['suffix']= $repeater[$m][6];
										}
									}
									unset($repeater);
									$m=0; $n=count($recursions); // NB: that code below only caters for the two outer-most repeat blocks
									if($n==1)
									{ //...then only one repeat block
										//$search=$recursions[$m]['search'];
										$replaceable=$recursions[$m]['replaceable'];
										while($k<$l)
										{
											$replace='';
											$x=min($l,$k+$recursions[$m]['x']);
											$this->parseEachForIterations($thisVar,$arrayElements,$k,$x,$replaceable,$replace);
											$result.=$recursions[$m]['prefix'].$replace.$recursions[$m]['suffix'];
										}
									}
									elseif($n>1)
									{ //... parse outer-most plus next inner.
										$x=$recursions[$m]['x'];
										//$search=$recursions[$m+1]['search'];	// Not used? - Tim
										$replaceable=$recursions[$m+1]['replaceable'];
										while($k<$l)
										{
											$replacements=array();
											for($w=0;$w<$x && $k<$l;$w++)
											{
												$replace='';
												$y=min($l,$k+$recursions[$m+1]['x']);
												$this->parseEachForIterations($thisVar,$arrayElements,$k,$y,$replaceable,$replace);
												$replacements[]=$recursions[$m+1]['prefix'].$replace.$recursions[$m+1]['suffix'];
											}
											$result.=$recursions[$m]['prefix'].implode($replacements).$recursions[$m]['suffix'];
										}
									}
								}
								else
								{
									while($k<$l)
									{
										$replaceable=$theMatches[4][$i];
										$this->parseEachForIterations($thisVar,$arrayElements,$k,$l,$replaceable,$result);
									}
								}
							}
						}
					}
					$theContent=@str_replace($theMatches[0][$i],$result,$theContent);
				}
			}
		}
		return true;
	}

	private function parseEachForIterations($thisVar,&$arrayElements,&$k,$x,$replaceable,&$result)
	{
		$replaceTemplate=$replaceable;
		while($k<$x)
		{
			if (is_array($this->{$thisVar}[$k]))
			{
				for($m=0,$n=count($arrayElements[1]); $m<$n; $m++)
				{
					$result.='';
					if (isset($this->{$thisVar}[$k][$arrayElements[1][$m]]))
					{
						$replaceable=@str_replace($arrayElements[0][$m],$this->evalBool($this->{$thisVar}[$k][$arrayElements[1][$m]]),$replaceable);
					}
					else
					{
						$replaceable=@str_replace($arrayElements[0][$m],'',$replaceable);
					}
				}
			}
			elseif (is_string($this->{$thisVar}[$k]))
			{
				$replaceable=@str_replace($arrayElements[0][0],$this->{$thisVar}[$k],$replaceable);
			}
			$result.=$replaceable;
			$replaceable=$replaceTemplate;
			$k++;
		}
		return true;
	}
}
?>
