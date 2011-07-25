<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_comment extends fml_element
{
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
		$this->setParent('include');
		$this->setParent('eachQueryResult');
		return true;
	}
	
	public function attributes()
	{
		$this->setAttribute('public',true,FML_DATATYPE_BOOL);
		return true;
	}
	
	public function template()
	{
		$return='';
		if ((string)$this->rawAttributes['public']==='true')
		{
			$return="<!-- [FML COMMENT]\r\n".$this->rawElement."\r\n-->";
		}
		return $return;
	}
}
?>