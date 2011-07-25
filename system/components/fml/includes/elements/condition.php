<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class fml_element_condition extends fml_element
{
	public $condition=false;
	public $result=null;
	 
	public function initiate()
	{
		$this->condition=$this->rawAttributes['condition'];
		if (!$this->evaluate())
		{
			$this->destroyChildElements();
		}
		return true;
	}
	
	public function parents()
	{
		$this->setParent('conditional');
		return true;
	}
	
	public function template(){}
	
	public function evaluate()
	{
		$this->result=$this->instance->parent->subfml->evaluate($this->parent->scope,'('.$this->condition.')?true:false;');
		return $this->result;
	}
}
?>