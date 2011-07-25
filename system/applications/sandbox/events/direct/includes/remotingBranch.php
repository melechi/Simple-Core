<?php
class sandbox_remotingBranch extends branch
{
	private $output=array
	(
		'type'		=>null,
		'action'	=>null,
		'method'	=>null,
		'name'		=>null,
		'data'		=>null,
		'result'	=>null,
		'where'		=>null,
		'timestamp'	=>null,
		'timeformat'=>null
	);
	
	public function initiate()
	{
		if (method_exists($this,'init'))$this->init();
		if ($this->global->post('tid'))		$this->output['tid']	=$this->global->post('tid');
		if ($this->global->post('action'))	$this->output['action']	=$this->global->post('action');
		if ($this->global->post('method'))	$this->output['method']	=$this->global->post('method');
	}
	
	public function output()
	{
		header('content-type:application/json');
		$this->output['timestamp']	=time();
		$this->output['timeformat']	=date('g:i:s a');
		
		die(json_encode($this->output));
	}
	
	public function fireEvent($name,$data)
	{
		$this->output['type']='event';
		$this->output['name']=$name;
		$this->output['data']=$data;
		$this->output();
	}
	
	public function respond($response)
	{
		$this->output['type']	='rpc';
		$this->output['result']	=$response;
		$this->output();
	}
	
	public function exception($message,$where=null)
	{
		if ($this->config->debug)
		{
			$this->output['type']	='exception';
			$this->output['message']=$message;
			$this->output['where']	=$where;
			$this->output();
		}
		//Suppress output if not in debug mode.
		$this->component->page->flushCaptured();//Destroy any output caught in the buffer.
		$this->core->shutdown();				//Let the framework exit cleanly.
	}
}
?>