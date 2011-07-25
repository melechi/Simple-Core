<?php
/*
 * Simple Core 2.1.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class branch extends overloader
{
	final public function __construct($parent)
	{
		parent::__construct($parent);
		$this->my->dir			=$this->parent->my->branchDir;
		$this->my->branchDir	=realpath($this->parent->my->branchDir.$this->branchFolder)._;
		$this->my->includeDir	=realpath($this->parent->my->branchDir.$this->xIncludeFolder)._;
		if (in_array('application',class_parents($this->parent)))$this->settings=$this->parent->settings;
		return true;
	}
}
?>