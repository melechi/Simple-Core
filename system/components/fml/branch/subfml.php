<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_subfml extends branch
{
	const OPERANDS='(\|\||&&|==|===|!=|!==)';
	const OPERANDS_STRING_CLOSE='(\'\|\||\'&&|\'==|\'===|\'!=|\'!==|\"\|\||\"&&|\"==|\"===|\"!=|\"!==)';
	const NAMESPACES='(this|var|local|post|get|request|server)';
	
	public function evaluate(fml_element $scope,$expression='false')
	{
		$return=null;
		$stringOpen=false;
		//Strip whitespace characters from certain points of the expression.
		$expression=preg_replace('@\s*'.self::OPERANDS.'\s*@','$1',$expression);
		//Split the expression into more managble blocks.
		$fragments=preg_split('/\b/',$expression);
		//Clean up the resulting array.
		if (!end($fragments))array_pop($fragments);
		if (!reset($fragments))array_shift($fragments);
		$toEval='$return=';
		//Loop through all the fragments and build $toEval.
		for ($i=0,$j=count($fragments); $i<$j; $i++)
		{
			if ($fragments[$i]=='(')
			{
				$toEval.=$fragments[$i];
			}
			elseif (preg_match('/^'.self::NAMESPACES.'$/',$fragments[$i]))
			{
				switch ($fragments[$i])
				{
					case 'this':
					{
						$toEval.='$scope';
						break;
					}
					case 'var':
					{
						$toEval.='$this->parent->var->get(\'';
						break;
					}
					case 'local':
					{
						$toEval.='$this->parent->getLocalDefinition($scope->instance->local,\'';
						break;
					}
					case 'post':
					{
						$toEval.='$this->global->post(\'';
						break;
					}
					case 'get':
					{
						$toEval.='$this->global->get(\'';
						break;
					}
					case 'request':
					{
						$toEval.='$this->global->request(\'';
						break;
					}
					case 'server':
					{
						$toEval.='$this->global->server(\'';
						break;
					}
				}
			}
			elseif ($fragments[$i]=='(\'' || $fragments[$i]=='(\"')
			{
				$toEval.=$fragments[$i];
				$stringOpen=true;
			}
			elseif ($fragments[$i]=='.')
			{
				if (preg_match('@var|local|post|get|request|server@',$fragments[($i-1)]))
				{
					$i++;
					$toEval.=$fragments[$i]."')";
				}
				elseif (!$stringOpen)
				{
					$toEval.='->';
				}
				else
				{
					$toEval.=$fragments[$i];
				}
			}
			elseif (preg_match('/^'.self::OPERANDS_STRING_CLOSE.'$/',$fragments[$i]))
			{
				$toEval.=$fragments[$i];
			}
			elseif (preg_match('@(\)\.|\'\)\.)@',$fragments[$i]))
			{
				if ($fragments[$i]=="').")
				{
					$toEval.="')->";
				}
				else
				{
					$toEval.=')->';
				}
			}
			else
			{
				if (isset($fragments[($i-1)]) && $fragments[($i-1)]=='(')
				{
					$toEval.="'".$fragments[$i]."'";
				}
				else
				{
					$toEval.=$fragments[$i];
				}
			}
		}
		$toEval.=';';
		//Evaluate the expression.
//		$this->debug->dumpThisToFile(print_r($fragments,true),'fml.log');
//		$this->debug->dumpThisToFile($toEval,'fml.log');
		eval($toEval);
//		$this->debug->dumpThisToFile(var_export($return,1),'fml.log');
		//Return the result.
		return $return;
	}
	
	public function doSubEvaluation(fml_element $scope,$string='')
	{
		$matches=array();
		preg_match_all('@\{[^\}]+\}@',$string,$matches);
		foreach ($matches[0] as $match)
		{
			$string=str_replace($match,$this->evaluate($scope,trim($match,'{}')),$string);
		}
		return $string;
	}
	
	public function containsExpression($string='')
	{
		return (bool)preg_match('@\{[^\}]+\}@',$string);
	}
}
?>