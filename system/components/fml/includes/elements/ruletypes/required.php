<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_rule_type_required extends fml_element_rule_type
{
	public function initiate()
	{
		$this->field->registerTemplateToken('required',' required');
		$this->field->registerTemplateToken('required_node','*');
		return true;
	}
	
	public function validate()
	{
		$value=$this->fieldAttribute('value');
		$evalMe='$value=$value';
		$matches=array();
		preg_match_all('@\[(\w*)\]@',$this->fieldAttribute('name'),$matches);
		for ($i=0,$depth=count($matches[1]); $i<$depth; $i++)
		{
			if (empty($matches[1][$i]))
			{
				break;
			}
			else
			{
				$evalMe.='['.$matches[1][$i].']';
			}
		}
		eval($evalMe.';');
		if (empty($value) && $value!=='0' && $value!==0)
		{
			$this->setStatus(FML_RULE_STATUS_FAIL);
			$this->setErrorMessage('This is a required field.');
		}
		else
		{
			$this->setStatus(FML_RULE_STATUS_PASS);
		}
		return true;
	}
}
?>