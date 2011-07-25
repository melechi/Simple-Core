<?php
class fml_element_rule_type_emailExists extends fml_element_rule_type
{
	public function validate()
	{
		if ($this->component->account->validation->emailExists($this->fieldAttribute('value')))
		{
			$this->setStatus(FML_RULE_STATUS_FAIL);
			$this->setErrorMessage('Email address already registered. Please choose a different one.');
		}
		else
		{
			$this->setStatus(FML_RULE_STATUS_PASS);
		}
		return true;
	}
}
?>