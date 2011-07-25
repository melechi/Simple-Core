<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_setFieldMessage extends fml_element
{
	public function initiate()
	{
		$field=$this->up('#'.$this->rawAttributes['field']);
		if ($this->up()->rawElement->getName()=='rule')
		{
			//If the rule status is fail, then set a message ONLY IF ONE HAS NOT ALREADY BEEN SET!
			if ($this->up()->getStatus()==FML_RULE_STATUS_FAIL && !strstr($field->type->template,'error'))
			{
				if ($this->rawAttributes['error']==='true' || $this->rawAttributes['error']===true)
				{
					$field->type->template='<label class="error"><div class="message">'.$this->rawAttributes['message'].'</div>'.$field->type->originalTemplate.'</label>';
				}
				elseif ($this->rawAttributes['error']==='false' || $this->rawAttributes['error']===false)
				{
					$field->type->template='<label class="message"><div class="message">'.$this->rawAttributes['message'].'</div>'.$field->type->originalTemplate.'</label>';
				}
			}
		}
		else
		{
			//If a message has already been set, then DON'T SET A NEW ONE!
			if (!strstr($field->type->template,'error'))
			{
				$field->type->template='<label class="error"><div class="error message">'.$this->rawAttributes['message'].'</div>'.$field->type->originalTemplate.'</label>';
			}
		}
		return true;
	}
	
	public function parents()
	{
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		$this->setParent('rule');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('field',	null,	FML_DATATYPE_ID);
		$this->setRequiredAttribute('message',	null,	FML_DATATYPE_STRING);
		$this->setAttribute(		'error',	true,	FML_DATATYPE_BOOL);
		return true;
	}
	
	public function template(){return '';}
}
?>