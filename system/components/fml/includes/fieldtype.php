<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class fml_element_field_type extends overloader
{
	public $template=false;
	public $originalTemplate=false;
	
	public function __construct($parent)
	{
		parent::__construct($parent);
		$this->originalTemplate=$this->template;
		if (method_exists($this,'initiate'))$this->initiate();
		return true;
	}
	
	public function template()
	{
		return @str_replace	//Suppress array to string conversion error.
		(
			array_keys($this->parent->templateTokens),
			array_values($this->parent->templateTokens),
			$this->fieldTemplate()
		);
	}
	
	public function fieldTemplate()
	{
		return $this->template;
	}
}
?>