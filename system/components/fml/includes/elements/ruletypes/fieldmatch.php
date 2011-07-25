<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_rule_type_fieldmatch extends fml_element_rule_type
{
	public function initiate()
	{
		$this->parent->setRequiredAttribute('field',null,FML_DATATYPE_ID);
		return true;
	}
	
	public function validate()
	{
		if ($this->fieldAttribute('value')!=$this->parent->instance->getElementById($this->ruleAttribute('field'))->rawAttributes['value'])
		{
			$this->setStatus(FML_RULE_STATUS_FAIL);
			$this->setErrorMessage('Field values did not match.');
		}
		else
		{
			$this->setStatus(FML_RULE_STATUS_PASS);
		}
		return true;
	}
}
?>