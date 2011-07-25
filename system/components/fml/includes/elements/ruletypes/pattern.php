<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_rule_type_pattern extends fml_element_rule_type
{
	public function initiate()
	{
		$this->parent->setRequiredAttribute('pattern',null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function validate()
	{
		if (!preg_match($this->ruleAttribute('pattern'),trim($this->fieldAttribute('value'))))
		{
			$this->setStatus(FML_RULE_STATUS_FAIL);
			$this->setErrorMessage('Invalid input.');
		}
		else
		{
			$this->setStatus(FML_RULE_STATUS_PASS);
		}
		return true;
	}
}
?>