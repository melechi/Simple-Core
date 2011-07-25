<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class event extends overloader
{
	abstract public function initiate();
	
	final public function __construct($parent,$bindTo,$dir)
	{
		parent::__construct($parent);
		$this->bindToAddress($bindTo);
		$this->my->name='event_'.$this->my->name;
		$this->my->dir=$dir;
		$this->my->branchDir=$this->my->dir.$this->branchFolder._;
		$this->my->includeDir=$this->my->dir.$this->xIncludeFolder._;
		return true;
	}
}
?>