<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_conditional extends fml_element
{
	public function initiate()
	{
		if (isset($this->rawAttributes['scope']))
		{
			$this->scope=$this->up('#'.$this->rawAttributes['scope']);
			if (!is_object($this->scope))
			{
				$this->instance->error(0,0,'Invalid conditional scope "'.$this->rawAttributes['scope'].'".');
			}
		}
		else
		{
			$this->scope=$this->up();
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
		$this->setParent('eachQueryResult');
		return true;
	}
	
	public function attributes()
	{
		$this->setAttribute('scope',null,FML_DATATYPE_ID);
		return true;
	}
	
	public function template(){}
}
?>