<?php
class user_page_register extends page
{
	public function initiate()
	{
		if(!$this->SUBMISSION_COMPLETE)
		{
			$this->component->fml->registerCustomRule($this->parent->my->includeDir.'fml_rule_emailExists.php'); 
			$this->FORM_REGISTER=$this->parent->newFMLInstance('register');
		}
	}
}
?>