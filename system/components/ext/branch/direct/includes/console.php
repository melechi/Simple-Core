<?php
class ext_direct_server_console
{
	public $parent=null;
	
	public function __construct(ext_direct_server_provider $parent)
	{
		$this->parent=$parent;
	}
	
	public function __call($name,$args)
	{
		$this->parent->fireEvent('console',array('type'=>$name,'data'=>$args));
	}
}
?>