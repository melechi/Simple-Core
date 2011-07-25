<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_rule extends fml_element
{
	public $xIncludeFolder='ruletypes';
	
	public $type=false;
	private $ruleTypes=array();
	
	public function initiate()
	{
//		$iterationNum=1;
//		foreach (new DirectoryIterator($this->my->includeDir) as $iteration)
//		{
//			if ($iteration->isFile())
//			{
//				$this->ruleTypes[basename($iteration->getFilename(),'.php')]=$iterationNum;
//				$iterationNum++;
//			}
//		}
//		unset($iterationNum);
		if (!isset($this->rawAttributes['type']))
		{
			$this->instance->error(0,0,'No element type defined on rule element.');
		}
		else
		{
			$className='fml_element_rule_type_'.$this->rawAttributes['type'];
			if (!class_exists($className))
			{
				$this->instance->error(0,0,'"'.$this->rawAttributes['type'].'" is an invalid rule element type.');
			}
			else
			{
				//Initiate rule and let it do its thing.
				$this->type=new $className($this);
				//Get the associated field.
				$field=$this->up('field');
				if ($this->instance->isValid() && $this->getStatus()==FML_RULE_STATUS_FAIL)
				{
					$this->instance->valid=false;
				}
				//If this rule has failed and the rule has no children.
				if ($this->getStatus()==FML_RULE_STATUS_FAIL && !count($this->rawElement->children()))
				{
					//If an error message has not been set on the associated field, then set one.
					if (!strstr($field->type->template,'error'))
					{
						$field->type->template='<label class="error"><div class="error message">'.$this->type->getErrorMessage().'</div>'.$field->type->originalTemplate.'</label>';
					}
				}
				//Else if the rule has children.
				/*
				 * NOTE: At this point, it does not matter if the rule has
				 * passed or failed. There are child elements, which means
				 * that is is possible that a conditional will check if
				 * the rule was successful and do something with it.
				 */
				elseif (count($this->rawElement->children()))
				{
					//If an error message has not been set on the associated field...
					if (!strstr($field->type->template,'error'))
					{
						$result=$this->rawElement->xpath('.//setFieldMessage');//TODO: [Tim] Changed this, needs testing.
						//... and the rule has no setFieldMessage children, then set one.
						if (!count($result))
						{
							$field->type->template='<label class="error"><div class="error message">'.$this->type->getErrorMessage().'</div>'.$field->type->originalTemplate.'</label>';
						}
					}
				}
			}
		}
		return true;
	}
	
	public function parents()
	{
		$this->setParent('field');
		$this->setParent('dbfield');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		return true;
	}
	
	public function attributes()
	{
		//$this->setRequiredAttribute('type',null,FML_DATATYPE_ENUM,$this->ruleTypes);
		$this->setRequiredAttribute('type',null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		return '<!-- FML Rule: "'.$this->rawAttributes['type'].'" -->'."\r\n{CHILDREN}";
	}
	
	public function setStatus($status)
	{
		return $this->type->setStatus((int)$status);
	}
	
	public function getStatus()
	{
		return $this->type->getStatus();
	}
}
?>