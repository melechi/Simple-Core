<?php
/*
 * Simple Core 2
 * Copyright(c) 2004-2009, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class component extends overloader
{
	const EXCEPTION_NAME='';
	
	final public function __construct($parent)
	{
		parent::__construct($parent);
		if (method_exists($this,'initiate'))$this->initiate();
		return true;
	}
	
	public function setMyDir()
	{
		$this->my->dir=$this->config->path->components.str_replace('component_','',get_class($this))._;
		return true;
	}
	
	/**
	 * Stub for initiating the components in certain situations
	 * where the autoloading cannot be used.
	 * 
	 * @return component
	 */
	
	public function init()
	{
		return $this;
	}
}
?>