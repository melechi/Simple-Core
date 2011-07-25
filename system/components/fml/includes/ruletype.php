<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class fml_element_rule_type extends overloader
{
	abstract function validate();
	
	const STATUS_PASS=1;
	const STATUS_FAIL=-1;
	
	private $status=0;
	private $errorMessage='';
	public $field=false;
	
	public function __construct($parent)
	{
		parent::__construct($parent);
		$this->field=$this->parent->up('field');
		if (!defined('FML_RULE_STATUS_PASS'))define('FML_RULE_STATUS_PASS',self::STATUS_PASS);
		if (!defined('FML_RULE_STATUS_FAIL'))define('FML_RULE_STATUS_FAIL',self::STATUS_FAIL);
		if (method_exists($this,'initiate'))$this->initiate();
		if ($this->parent->instance->mode==FML_MODE_VALIDATE)
		{
			$this->validate();
		}
		return true;
	}
	
	public function fieldAttribute($attribute=null)
	{
		return (isset($this->field->rawAttributes[$attribute]))?$this->field->rawAttributes[$attribute]:null;
	}
	
	public function ruleAttribute($attribute=null)
	{
		return (isset($this->parent->rawAttributes[$attribute]))?$this->parent->rawAttributes[$attribute]:null;
	}
	
	public function setStatus($status=null)
	{
		if ($status==self::STATUS_PASS || $status==self::STATUS_FAIL)
		{
			$this->status=$status;
		}
		return $this;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function setErrorMessage($theMessage=null)
	{
		$this->errorMessage=$theMessage;
		return $this;
	}
	
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}
}
?>