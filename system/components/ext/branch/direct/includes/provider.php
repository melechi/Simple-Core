<?php
class ext_direct_server_provider extends overloader
{
	private $responder	=null;
	private $output		=array
	(
		'tid'		=>null,
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
	
	public $console=null;
	
	public function __construct(event $parent,direct_direct_server_responder $responder,$request)
	{
		parent::__construct($parent);
		$this->responder		=$responder;
		$this->my->dir			=realpath(dirname(__FILE__))._;
		$this->my->branchDir	=realpath($this->my->dir.$this->branchFolder)._;
		$this->my->includeDir	=realpath($this->my->dir.$this->xIncludeFolder)._;
		if (method_exists($this,'initiate'))$this->initiate();
		if (isset($request['tid']))		$this->output['tid']	=$request['tid'];
		if (isset($request['action']))	$this->output['action']	=$request['action'];
		if (isset($request['method']))	$this->output['method']	=$request['method'];
		$this->console=new ext_direct_server_console($this);
		set_error_handler(array($this,'catchError'));
	}
	
	public function catchError($eCode=null,$eString=null,$theFile=null,$theLine=null,&$scope=null)
	{
		$code=false;
		switch ($eCode)
		{
//				case E_STRICT:
//				{
//					$code='Strict Rule Error';
//					//print 'STRICT: '.$eString.' in '.$theFile.' on line '.$theLine.'.<br />';
//					$noOutput=true;
//					break;
//				}
//				case E_NOTICE:
//				{
//					//Used for cacher.
//					if ($eString=='Trying to get property of non-object')
//					{
//						//$trace=@debug_backtrace();
//						//print_r($trace);
//					}
//					break;
//				}
//				case E_NOTICE:
//				{
////					print 'NOTICE:: '.$eString.' in '.$theFile.' on line '.$theLine.'.<br />';
//					$noOutput=true;
//					break;
//				}
			case E_NOTICE:				if(!$code)$code='Notice';
			case E_ERROR:				if(!$code)$code='Error';
			case E_STRICT:				break;//if(!$code)$code='Strict Rule Error';
			case E_WARNING:				if(!$code)$code='Warning';
			case E_PARSE:				if(!$code)$code='Parse Error';
			case E_CORE_ERROR:			if(!$code)$code='PHP Core Error';
			case E_CORE_WARNING:		if(!$code)$code='PHP Core Warning';
			case E_COMPILE_ERROR:		if(!$code)$code='PHP Compilation Error';
			case E_COMPILE_WARNING:		if(!$code)$code='PHP Compilation Warning';
			case E_USER_ERROR:			if(!$code)$code='User Thrown Error';
			case E_USER_WARNING:		if(!$code)$code='User Thrown Warning';
			case E_USER_NOTICE:			if(!$code)$code='User Thrown Notice';
			case E_RECOVERABLE_ERROR:	if(!$code)$code='Recoverable Error';
			{
				$message=<<<ERROR
<div class="errorBlock">
	<p><b>Type:</b> {$code}</p><br />
	<p><b>Message:</b> {$eString}</p><br />
	<p><b>File:</b> {$theFile}</p><br />
	<p><b>Line:</b> {$theLine}</p>
</div>
ERROR;
				$this->exception($message,$theFile.' on Line '.$theLine);
				break;
			}
		}
	}
	
	public function pushResponse()
	{
		$this->output['timestamp']	=time();
		$this->output['timeformat']	=date('g:i:s a');
		$this->responder->push($this->output);
	}
	
	public function fireEvent($name,$data)
	{
		$this->output['type']='event';
		$this->output['name']=$name;
		$this->output['data']=$data;
		$this->pushResponse();
	}
	
	public function respond($response)
	{
		$this->output['type']	='rpc';
		$this->output['result']	=$response;
		$this->pushResponse();
	}
	
	public function exception($message,$where=null)
	{
		if ((bool)(string)$this->config->debug)
		{
			$this->output['type']	='exception';
			$this->output['name']	='exception';
			$this->output['message']=$message;
			$this->output['where']	=$where;
			$this->pushResponse();
		}
		//Suppress output if not in debug mode.
		$this->component->page->flushCaptured();//Destroy any output caught in the buffer.
		//$this->parent->parent->parent->shutdown();				//Let the framework exit cleanly.
	}
}
?>