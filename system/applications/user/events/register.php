<?php
class user_event_register extends event
{
	public function initiate()
	{
		$this->component->fml->registerCustomRule($this->parent->my->includeDir.'fml_rule_emailExists.php');
		if (!$this->parent->newFMLInstance('register')->isValid())
		{
			$this->component->feedback->error('Your form contained errors. Please check the error messages next to the field.');
		}
		else
		{
			$this->parent->setTemplateVar('SUBMISSION_COMPLETE',true);
			$accountID=$this->component->account->add('user');
			if ($this->parent->user->add($accountID))
			{
				$this->component->feedback->message('Registration Complete!');
			}
			else
			{
				$this->component->feedback->error('Oops! Something went wrong!');
			}
		}
	}
}
?>