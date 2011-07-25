<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class template_parser_conditionals extends template_parser
{
	public function registerParsers()
	{
		$this->parent->registerParser('conditionals','parseConditionals',1);
		return true;
	}

	/* PARSE CONDITIONALS
	 * This function takes a string of
	 * content (usually a tpl document) and
	 * parses all the conditional template tags
	 * within it.
	 */

	public function parseConditionals($theContent=null)
	{
		$conditionals=array();				//A container for the conditionals that are extracted.
		$workingConditionals=array();		//A container for the conditionals that are valid and we can work with.
		//Start off by finding all the conditionals.
		if (@preg_match_all('@\{IF:([^\}]+)\}(.*?)\{ENDIF\}@is',$theContent,$conditionals))
		{
//				print 'CONDITIONALS:'."\r\n";
//				print_r($conditionals);
//				exit();
			//Loop through each conditional and break it down to something we can understand and work with.
			for ($i=0,$j=count($conditionals[1]); $i<$j; $i++)
			{
				//For each conditional found, we pull out any elseif conditions and factor them into the build.
				if (@stristr($conditionals[0][$i],'ELSEIF:'))
				{
					$fragments=@preg_split('@\{ELSEIF:([^\}]+)\}(.*?)@is',$conditionals[0][$i],-1,PREG_SPLIT_DELIM_CAPTURE);
					$elseifs=array();
					for ($k=0,$l=count($fragments); $k<$l; $k++)
					{
						if (empty($fragments[$k]))continue;
						if (@stristr($fragments[$k],'{IF:'))continue;
						if (@stristr($fragments[$k],'{ELSE}'))
						{
							$subFragments=@explode('{ELSE}',$fragments[$k],2);
							$elseifs[]=$subFragments[0];
						}
						elseif (@stristr($fragments[$k],'{ENDIF}'))
						{
							$elseifs[]=@str_replace('{ENDIF}','',$fragments[$k]);	//Possible trailing new lines could be caused by this.
						}
						else
						{
							$elseifs[]=$fragments[$k];
						}
					}
					$conditionals[1][$i]=array($conditionals[1][$i],@preg_replace(array('@\{ELSEIF:[^\}]+\}.*@is','@\{IF:([^\}]+)\}@'),'',$conditionals[0][$i]));
					$conditionals[1][$i]=@array_merge($conditionals[1][$i],$elseifs);

//						print 'ELSEIFS: '."\r\n";
//						print_r($elseifs);
				}
//					print 'CONDITIONALS:'."\r\n";
//					print_r($conditionals);
//					exit();
				if (@is_array($conditionals[1][$i]))
				{
					for ($k=0,$l=count($conditionals[1][$i]); $k<$l; $k++)
					{
						$workingConditionals[$i][$k]=$this->getWorkingConditionals($conditionals[1][$i][$k]);
						if (empty($workingConditionals[$i][$k]))unset($workingConditionals[$i][$k]);	//Cleanup condition. See the # note below.
					}
					@array_unshift($workingConditionals[$i],'_');
					@array_shift($workingConditionals[$i]);
				}
				else
				{
					$workingConditionals[$i]=$this->getWorkingConditionals($conditionals[1][$i]);
				}
			}
			#$workingConditionals contain some resedue (blank array elements), could cause some problems down the line. Will attempt to remove and see what happens.
//				print 'WORKING CONDITIONALS:'."\r\n";
//				print_r($workingConditionals);
//				exit();
			/* This next section deals with the actual parsing
			 * of the conditionals.
			 * If a condition is met, the IF statement will be
			 * replaced with its contents.
			 * If a condition is not met, the IF statement, and
			 * its contents, will be stripped out of the template
			 * unless there is an else clause, in which case that
			 * content will remain.
			 */
			for ($i=0,$j=count($conditionals[1]); $i<$j; $i++)
			{
				if (@isset($workingConditionals[$i]) && @is_array($workingConditionals[$i]))
				{
					$writeBody=true;
					$statement='';
//						print_r($workingConditionals);
					if (count($workingConditionals[$i])>1)
					{
						if (@is_array($workingConditionals[$i][0]))//Possible issue here.
						{
							//$statement.=$this->compileMultipleConditionalHeads($workingConditionals[$i]);
							$writeBody=false;
							$statement.=$this->compileMultipleConditionals($workingConditionals[$i],$conditionals[1][$i]);
						}
						else
						{
							$statement.=$this->compileConditionalHead($workingConditionals[$i],'multiple');
						}
					}
					else
					{
						$statement.=$this->compileConditionalHead($workingConditionals[$i][0],'single');
					}
					if (@stristr($conditionals[2][$i],'{ELSE}'))
					{
						$fragments=@explode('{ELSE}',$conditionals[2][$i]);
						if ($writeBody)$statement.=$this->compileConditionalBody($fragments[0]);
						$fragments[1]=template_parser::escapeVars($fragments[1]);
						$statement.='else{$result=<<<CONTENT'."\r\n{$fragments[1]}\r\nCONTENT;\r\n}";
					}
					else
					{
						if ($writeBody)$statement.=$this->compileConditionalBody($conditionals[2][$i]);
					}
//						print 'STATEMENT: '.$statement;
					$result='';
					eval($statement);
					$theContent=@str_replace($conditionals[0][$i],$result,$theContent);
				}
			}
		}
		return $theContent;
	}

	//TODO: Retest these.
	private function getWorkingConditionals($conditional=null)
	{
		$return=array();
		if (!@empty($conditional))
		{
			//Simple conditionals will be handled here.
			if (@preg_match('/^(\w|'.self::REG_VAR.')+$/',$conditional))
			{
				$return[0]=$conditional;
			}
			//and here...
			elseif (@preg_match('/^('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+(==|!=|>|<)('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+$/',$conditional))
			{
				$return[0]=$conditional;
			}
			//More complecated ones will be handled here.
			//elseif (@preg_match('/^(\w|\$\:[\w])+(==|!=|>|<)(\w|\$\:[\w])+(\s*(\|\||&&)\s*(\w|\$\:[\w])+(==|!=|>|<)(\w|\$\:[\w])+)+$/',$conditional))
			elseif (@preg_match('/^('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+(==|!=|>|<)?('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+?(\s*(\|\||&&)\s*('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+(==|!=|>|<)?('.self::REG_STRINGVALUE.'|'.self::REG_VAR.')+?)+$/i',$conditional))
			{
				$conditional=@str_replace(array('&&','||'),array('AND','OR'),$conditional);
				$return=@preg_split('@\ @',$conditional);
			}
//			else
//			{
//				$this->error('Syntax Error. "'.$conditional.'" is invalid.');
//			}
		}
		return $return;
	}

	private function parseConditionalFragments($conditional=null,$operator=null)
	{
		$return=array();
		if (!empty($conditional) && !empty($operator))
		{
			$return=@explode($operator,$conditional,2);
			if ($this->matchVar($return[0]))
			{
				$return[0]=$this->returnVar($return[0]);
			}
			if ($this->matchVar($return[1]))
			{
				$return[1]=$this->returnVar($return[1]);
			}
			$return[0]=$this->evalBool($return[0]);
			$return[1]=$this->evalBool($return[1]);
		}
		return $return;
	}

	private function compileConditionalHead($conditional=null,$mode='single')
	{
		$return='';
		if (!@empty($conditional))
		{
			$theMatch=array();
			if ($mode=='single')
			{
				//Pull out the conditonal key's == and !=.
				if (@preg_match('@(==|!=|>|<)@',$conditional,$theMatch))
				{
					$fragments=$this->parseConditionalFragments($conditional,$theMatch[1]);
					$return.='if (';
					$return.=($this->isBool($fragments[0])?$fragments[0]:'\''.$fragments[0].'\'');
					$return.=$theMatch[1];
					$return.=($this->isBool($fragments[1])?$fragments[1]:'\''.$fragments[1].'\'');
					$return.=')';
				}
				elseif ($this->matchVar($conditional))
				{
					$conditional=$this->extractVar($conditional);
					if (isset($this->{$conditional}))
					{
						$conditional=$this->{$conditional};
					}
					$conditional=$this->evalBool($conditional);
					$return.='if (';
					$return.=($this->isBool($conditional)?$conditional:'\''.$conditional.'\'');
					$return.=')';
				}
				elseif (@preg_match('/^'.self::REG_STRINGVALUE.'+$/',$conditional))
				{
					$conditional=$this->evalBool($conditional);
					if ($this->isBool($conditional))
					{
						$return.='if ('.$conditional.')';
					}
					else
					{
						$return.='if (\''.$conditional.'\')';
					}
				}
				else
				{
					$this->error('Template compilation failed! Parse error! No idea what you were trying to do there!'
										.' The conditonal was: '.$conditional);
				}
			}
			elseif ($mode=='multiple')
			{
				for ($i=0,$j=count($conditional); $i<$j; $i++)
				{
					//Pull out the conditonal key's == and !=.
					if (@preg_match('@(==|!=|>|<)@',$conditional[$i],$theMatch))
					{
						$fragments=$this->parseConditionalFragments($conditional[$i],$theMatch[1]);
						if (!$i)$return.='if (';
						$return.='(';
						$return.=($this->isBool($fragments[0])?$fragments[0]:'\''.$fragments[0].'\'');
						$return.=$theMatch[1];
						$return.=($this->isBool($fragments[1])?$fragments[1]:'\''.$fragments[1].'\'');
						$return.=')';
						if (($i+1)==$j)$return.=')';
					}
					elseif ($this->matchVar($conditional[$i]))
					{
						$conditional[$i]=$this->extractVar($conditional[$i]);
						if (isset($this->{$conditional[$i]}))
						{
							$conditional[$i]=$this->{$conditional[$i]};
						}
						$conditional[$i]=$this->evalBool($conditional[$i]);
						if (!$i)$return.='if (';
						$return.='(';
						$return.=($this->isBool($conditional[$i])?$conditional[$i]:'\''.$conditional[$i].'\'');
						$return.=')';
						if (($i+1)==$j)$return.=')';
					}
					//Pull out the conditonal key's 'OR' and 'AND'.
					elseif (@preg_match('@(OR|AND)@',$conditional[$i],$theMatch))
					{
						switch ($theMatch[1])
						{
							case 'OR':		$return.=' || ';		break;
							case 'AND':		$return.=' && ';		break;
							default:
							{
								$this->error('Template compilation failed! A \'OR\'/\'AND\' conditional failed to parse.');
							}
						}
					}
					elseif (@preg_match('/^'.self::REG_STRINGVALUE.'+$/',$conditional[$i]))
					{
						$conditional[$i]=$this->evalBool($conditional[$i]);
						if ($this->isBool($conditional[$i]))
						{
							if (!$i)$return.='if (';
							$return.='('.$conditional[$i].')';
							if (($i+1)==$j)$return.=')';
						}
						else
						{
							if (!$i)$return.='if (';
							$return.='(\''.$conditional[$i].'\')';
							if (($i+1)==$j)$return.=')';
						}
					}
					else
					{
						$this->error('Template compilation failed! Parse error! No idea what you were trying to do there!'
											.' The conditonal was: '.$conditional[$i]);
					}
				}
			}
		}
		return $return;
	}

	private function compileMultipleConditionals($conditionals=null,$content=null)
	{
		$return='';
		if (@is_array($conditionals) && @is_array($content))
		{
			for ($i=0,$j=count($conditionals),$c=1; $i<$j; $i++,$c+=2)
			{
				$theMatch=array();
				if (count($conditionals[$i])>1)
				{
					for ($k=0,$l=count($conditionals[$i]); $k<$l; $k++)
					{
						if (@preg_match('@(==|!=|>|<)@',$conditionals[$i][$k],$theMatch))
						{
							$fragments=$this->parseConditionalFragments($conditionals[$i][$k],$theMatch[1]);
							$prepend='';
							$append='';
							if (!$i)
							{
								if (!$k)
								{
									$prepend.='if (';
								}
							}
							else
							{
								if (!$k)
								{
									$prepend.='elseif (';
								}
							}
							if (($k+1)==$l)$append=')';
							$return.=$prepend.'(\''.$fragments[0].'\''.$theMatch[1].''.'\''.$fragments[1].'\')'.$append;
							if (($k+1)==$l)$return.=$this->compileConditionalBody($content[$c]);
						}
						elseif ($this->matchVar($conditionals[$i][$k]))
						{
							$conditionals[$i][$k]=$this->extractVar($conditionals[$i][$k]);
							if (isset($this->{$conditionals[$i][$k]}))
							{
								$conditionals[$i][$k]=$this->{$conditionals[$i][$k]};
							}
							$conditionals[$i][$k]=$this->evalBool($conditionals[$i][$k]);
							$prepend='';
							$append='';
							if (!$i)
							{
								if (!$k)
								{
									$prepend.='if (';
								}
							}
							else
							{
								if (!$k)
								{
									$prepend.='elseif (';
								}
							}
							$return.=$prepend.'('.($this->isBool($conditionals[$i][$k])?$conditionals[$i][$k]:'\''.$conditionals[$i][$k].'\')').$append;
							if (($k+1)==$l)$return.=')'.$this->compileConditionalBody($content[$c]);
						}
						elseif (@preg_match('@(OR|AND)@',$conditionals[$i][$k],$theMatch))
						{
							switch ($theMatch[1])
							{
								case 'OR':		$return.=' || ';		break;
								case 'AND':		$return.=' && ';		break;
								default:
								{
									$this->error('Template compilation failed! A \'OR\'/\'AND\' conditional failed to parse.');
								}
							}
						}
						elseif (@preg_match('/^'.self::REG_STRINGVALUE.'+$/',$conditionals[$i][$k]))
						{
							$conditionals[$i][$k]=$this->evalBool($conditionals[$i][$k]);
							if (!$i)
							{
								if (!$k)
								{
									$prepend.='if (';
								}
							}
							else
							{
								if (!$k)
								{
									$prepend.='elseif (';
								}
							}
							if ($this->isBool($conditionals[$i][$k]))
							{
								$return.='('.$conditionals[$i][$k].')';
							}
							else
							{
								$return.='(\''.$conditionals[$i][$k].'\')';
							}
							if (($k+1)==$l)$return.=')'.$this->compileConditionalBody($content[$c]);
						}
						else
						{
							$this->error('Template compilation failed! Parse error! No idea what you were trying to do there!'
												.' The conditonal was: '.$conditionals[$i][$k]);
						}
					}
				}
				else
				{
					//Pull out the conditonal key's == and !=.
					if (@preg_match('@(==|!=|>|<)@',$conditionals[$i][0],$theMatch))
					{
						$fragments=$this->parseConditionalFragments($conditionals[$i][0],$theMatch[1]);
						$return.=((!$i)?'if (':'elseif (');
						$return.=($this->isBool($fragments[0])?$fragments[0]:'\''.$fragments[0].'\'');
						$return.=$theMatch[1];
						$return.=($this->isBool($fragments[1])?$fragments[1]:'\''.$fragments[1].'\'');
						$return.=')';
						$return.=$this->compileConditionalBody($content[$c]);
					}
					elseif ($this->matchVar($conditionals[$i][0]))
					{
						$conditionals[$i][0]=$this->extractVar($conditionals[$i][0]);
						if (isset($this->{$conditionals[$i][0]}))
						{
							$conditionals[$i][0]=$this->{$conditionals[$i][0]};
						}
						$conditionals[$i][0]=$this->evalBool($conditionals[$i][0]);
						$return.=((!$i)?'if (':'elseif (');
						$return.=($this->isBool($conditionals[$i][0])?$conditionals[$i][0]:'\''.$conditionals[$i][0].'\'');
						$return.=')';
						$return.=$this->compileConditionalBody($content[$c]);
					}
					else
					{
						$this->error('Template compilation failed! Parse error! No idea what you were trying to do there!'
											.' The conditonal was: '.$conditionals[$i][0]);
					}
				}
			}
		}
		return $return;
	}

	private function compileConditionalBody($content=null)
	{
		$return='{';
		if ($content)
		{
			$return.='$result=<<<CONTENT'."\r\n".template_parser::escapeVars($content)."\r\nCONTENT;\r\n";
		}
		return $return.'}';
	}
}
?>
