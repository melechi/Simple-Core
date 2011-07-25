<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_query extends fml_element
{
	public $result=false;
	
	public function initiate()
	{
		if (!$this->instance->isRegisteredQuery($this->rawAttributes['sqlid']))
		{
			$this->instance->error('SQL ID "'.$this->rawAttributes['sqlid'].'" is not a registered query.');
		}
		else
		{
			$this->component->database->c($this->rawAttributes['connection'])->query($this->instance->getRegisteredQuery($this->rawAttributes['sqlid']));
			$this->result=$this->component->database->result();
			$this->clean($this->result);
		}
		return true;
	}
	
	public function parents()
	{
		$this->setParent('form');
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		$this->setParent('field');
		$this->setParent('rule');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		$this->setParent('field');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('sqlid',	null,FML_DATATYPE_ID);
		$this->setAttribute('connection',		null,FML_DATATYPE_STRING);
		return true;
	}
	
	public function template()
	{
		return '{CHILDREN}';
	}
}
?>