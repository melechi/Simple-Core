<?php
/**
 * @package simplecore
 * @subpackage logger
 */
/**
 * This is the simple core logging class.
 * 
 * It handles logging for everything in the framework and is intelligently
 * hooked into the core overloader to allow seemless organisation of logs
 * grouped by date, type and debugging.
 * 
 * @author Timothy Chandler <tim@s3.net.au>
 * @version 1.0
 * @since 30/09/2010
 */
class core_logger
{
	/**
	 * @var core
	 */
	public $parent		=null;
	/**
	 * @access private
	 * @var array
	 */
	private $buffer		=array();
	/**
	 * @access private
	 * @var string
	 */
	private $dayStamp	=null;
	/**
	 * @access private
	 * @var string
	 */
	private $fileName	='general';
	/**
	 * Class constructor. Handles the initiation of the class,
	 * connecting it with its parent, the core, and setting up
	 * the datestamp and filename.
	 * 
	 * @param string $fileName - The name to give the logfile.
	 */
	public function __construct($fileName='general')
	{
		global ${CORE};
		$this->parent=${CORE};
		$this->dayStamp=date('Ymd');
		$this->fileName=$fileName;
	}
	
	public function __destruct()
	{
//		$this->bufferCommit();
	}
	
	public function log($message,$category=null,$type='info')
	{
		$this->bufferPush
		(
			$type,
			$message,
			$category
		);
		$this->bufferCommit();
	}
	
	public function info($message,$category=null)
	{
		$this->log($message,$category,'info');
	}
	
	public function warn($message,$category=null)
	{
		$this->log($message,$category,'warn');
	}
	
	public function error($message,$category=null)
	{
		$this->log($message,$category,'error');
	}
	
	public function critical($message,$category=null)
	{
		$this->log($message,$category,'critical');
	}
	
	public function debug($message,$category=null)
	{
		//TODO: collect debug info.
//		if ($this->content=@ob_get_contents())
//		{
//			
//		}
		$this->log($message,$category,'debug');
	}
	
	private function bufferPush($type,$data,$category=null)
	{
		$this->buffer[]=array
		(
			'type'		=>$type,
			'data'		=>$data,
			'category'	=>$category,
			'timestamp'	=>time()
		);
		return $this;
	}
	
	private function bufferCommit()
	{
		$NL			="\n";
		$data		='';
		$debugData	='';
		foreach ($this->buffer as $bufferItem)
		{
			$dataSet=($bufferItem['type']!='debug')?'data':'debugData';
			$$dataSet.='['.$this->dayStamp.' - '.date('g:i:sa',$bufferItem['timestamp']).']';
			$$dataSet.='['.$bufferItem['type'].']';
			if (!empty($bufferItem['category']))$$dataSet.='['.$bufferItem['category'].']';
			if (in_array($bufferItem['type'],array('debug','critical')))
			{
				$$dataSet.=$NL.'===DEBUG START==='.$NL;
				$$dataSet.=$bufferItem['data'].$NL;
				$$dataSet.='===DEBUG END==='.$NL;
			}
			else
			{
				$$dataSet.=' '.$bufferItem['data'].$NL;
			}
		}
		$this->buffer=array();
		$dir=$this->parent->config->path->logs.$this->dayStamp._;
		if (!is_dir($dir))mkdir($dir,0777);
		file_put_contents($dir.$this->fileName.'.log',$data,FILE_APPEND+LOCK_EX);
		if (!empty($debugData))
		{
			file_put_contents($dir.$this->fileName.'_debug.log',$debugData,FILE_APPEND+LOCK_EX);
		}
	}
}
/**
 * This class is used in place of the real logger class when 
 * @author Timothy Chandler <tim@s3.net.au>
 * @version 1.0
 * @since 30/09/2010
 * @subpackage Logger
 */
class core_logger_dummy
{
	public function __construct(){}
	public function __destruct(){}
	public function log($message,$category=null,$type=null){}
	public function info($message,$category=null){}
	public function warn($message,$category=null){}
	public function error($message,$category=null){}
	public function critical($message,$category=null){}
	public function debug($message,$category=null){}
	private function bufferPush($type,$data,$category=null){}
	private function bufferCommit(){}
}
?>