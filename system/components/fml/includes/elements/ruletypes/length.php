<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_rule_type_length extends fml_element_rule_type
{
	public function initiate()
	{
		$this->parent->setAttribute('min',0,FML_DATATYPE_INT);
		$this->parent->setAttribute('max',0,FML_DATATYPE_INT);
		return true;
	}
	
	public function validate()
	{
		$this->setStatus(FML_RULE_STATUS_PASS);
		if ((int)$this->ruleAttribute('min')!==0)
		{
			if (!isset($this->field->rawAttributes['value']{(int)$this->ruleAttribute('min')-1}))
			{
				$this->setStatus(FML_RULE_STATUS_FAIL);
				$this->setErrorMessage('Field entry is too short. Minimum field length is '.(int)$this->ruleAttribute('min').'.');
			}
		}
		elseif ((int)$this->ruleAttribute('max')!==0)
		{
			if (isset($this->field->rawAttributes['value']{(int)$this->ruleAttribute('max')+1}))
			{
				$this->setStatus(FML_RULE_STATUS_FAIL);
				$this->setErrorMessage('Field entry is too long. Maximum field length is '.(int)$this->ruleAttribute('max').'.');
			}
		}
		return true;
	}
}
?>