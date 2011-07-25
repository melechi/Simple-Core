<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class sqlite_instance_transaction extends branch
{
	private $status=0;
	
	public function begin()
	{
		if ($this->hasBegan())
		{
			$this->exception('Transaction has already begun. Don\'t call begin() a second time before ending a transaction (commit or rollback).');
		}
		else
		{
			$this->parent->connection->beginTransaction();
			$this->status=1;
		}
		return $this->parent;
	}
	
	public function commit()
	{
		$return=$this->parent->connection->commit();
		$this->status=0;
		//return $this->parent;
		return $return;
	}
	
	public function rollback()
	{
		$this->parent->connection->rollBack();
		$this->status=0;
		return $this->parent;
	}
	
	public function hasBegan()
	{
		return $this->status===1?true:false;
	}
}
?>