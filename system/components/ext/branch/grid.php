<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_grid extends branch
{
	public $branchFolder='grid';
	
	public function initiate()
	{
		$this->xInclude('getSetOptions');
		include_once($this->my->dir.'__grid.php');
		$this->branch('column');
		$this->branch('selectionModel');
		$this->branch('plugin');
		$this->branch('toolbar');
		return true;
	}
	
	public function newGrid()
	{
		$return=__ext_grid::construct();
		$return->parent=$this;
		return $return;
	}
}
?>