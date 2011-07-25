<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class page extends overloader
{
	abstract public function initiate();
	
	public function __construct($parent,$bindTo,$dir,$templateDir,$templateContentDir)//,$address)
	{
		parent::__construct($parent);
		$this->bindToAddress($bindTo);
		$this->my->name='page_'.$this->my->name;
		$this->my->dir=$dir;
		$this->my->branchDir=$this->my->dir.$this->branchFolder._;
		$this->my->includeDir=$this->my->dir.$this->xIncludeFolder._;
		$this->my->templateDir=$templateDir;
		$this->my->templateContentDir=$templateContentDir;
//		$this->my->address=$address
		return true;
	}
	
	final public function setContentType($contentType='')
	{
		header('Content-Type:'.$contentType);
		return $this;
	}
	
	public function __set($theKey,$theVal)
	{
		if (!$this->isReservedVar($theKey))
		{
			if ($this->parent->useSmarty)
			{
				return $this->component->smarty->assign($theKey,$theVal);
			}
			else
			{
				return $this->component->template->{$theKey}=$theVal;
			}
		}
		else
		{
			return parent::__set($theKey,$theVal);
		}
	}
	
	public function __get($theKey)
	{
		if (!$this->isReservedVar($theKey))
		{
			if ($this->parent->useSmarty)
			{
				return $this->smarty->get_template_vars($theKey);
			}
			else
			{
				return $this->component->template->{$theKey};
			}
		}
		else
		{
			return parent::__get($theKey);
		}
	}
	
	public function newScope($scope)
	{
		$this->component->template->newScope($scope);
		return $this;
	}
}
?>