<?php
/*
 * Simple Core 2.1.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
//TODO: Entire framework does not work in PHP version 3.1.0 > WHY?!
error_reporting(E_ALL);
#OBJECT DEFINITIONS
@define('CORE','CORE');
#Globals
global ${CORE},$_OVERRIDE;
//Special Global Functions
function ifSetOr(&$theVar,$defaultTo=null)
{
	if (!isset($theVar))
	{
		$theVar=$defaultTo;
	}
	return true;
}
function ifIsFileInclude($file='')
{
	if ($return=is_file($file))
	{
		include($file);
	}
	return $return;
}
function ifIsFileIncludeOnce($file='')
{
	if ($return=is_file($file))
	{
		include_once($file);
	}
	return $return;
}
function ifIsFileRequire($file='')
{
	if ($return=is_file($file))
	{
		require($file);
	}
	return $return;
}
function ifIsFileRequireOnce($file='')
{
	if ($return=is_file($file))
	{
		require_once($file);
	}
	return $return;
}
/**
 * THE SIMPLE CORE ;)
 * 
 * This is the heart of the framework. It handles the framework's
 * flow and processes all core logic. It uses various handlers,
 * also known as core classes, to deal with framework specific
 * logic and processing.
 * 
 * Although it uses it's various handlers, the core is responsible
 * for the execution of such handlers and holds the logic to
 * efficently get the job done.
 *
 * @final
 * @author Timothy Chandler
 * @version 2.3.0
 * @copyright Simple Site Solutions 06/12/2007
 */
final class core
{
	//Public Attributes
	public $version				='2.3.0';
	/**
	 * @var config - The core configuration (usually config.xml)
	 */
	public $config				=false;
	public $debug				=false;
	public $component			=false;
	public $application			=false;
	public $global				=false;
	public $corelogger			=false;
	public $coreError			=false;
	public $server				=array();
	public $defaultApplication	=null;
	public $my					=null;
	public $unitTesting			=null;
	
	//URI STUFF
	public $URI					='';
	public $nodes				=array();
	public $basePath			='';
	public $fullPath			='';
	
	public function initiate()
	{
		global $_OVERRIDE;
		//Set core headers.
		@header('X-Powered-By: PHP/'.@phpversion().' & Simple Core/'.$this->version);
		@header('X-Simple-Core: Version '.$this->version);
		
//		if (!(version_compare(PHP_VERSION,'5.2.11','>=') && version_compare(PHP_VERSION,'5.3','<')))
//		{
//			throw new Exception('Incompatible version of PHP detected. You are running version: '.PHP_VERSION.'. Please use PHP 5.2.11 or higher. PHP 5.3 is currently unsupported.');
//		}
		
		if (!$this->obtainServerEnvironment())
		{
			throw new Exception('Core was unable to obtain server environment.');
		}
			
		/* LOAD CONFIG
		 * Check if a config directive was passed in.
		 * If it wasn't, we'll look for it.
		 */
		include_once(dirname(__FILE__)._.'system'._.'core'._.'component'._.'config.php');
		if (!isset($_OVERRIDE['config']))
		{
			if (@is_file(dirname(__FILE__)._.'config.xml'))
			{
//				$this->config=simplexml_load_file(dirname(__FILE__)._.'config.xml');
				$this->config=new config(dirname(__FILE__)._.'config.xml');
			}
			else
			{
				throw new Exception('Failed to locate config file.',0);
			}
		}
		else
		{
			if (@is_file($_OVERRIDE['config']))
			{
//				$this->config=simplexml_load_file($_OVERRIDE['config']);
				$this->config=new config($_OVERRIDE['config']);
			}
			else
			{
				throw new Exception('Failed to locate config file in config override mode.',0);
			}
		}
		if (!isset($this->config->defaultApplication))
		{
			throw new Exception('Config is either empty or missing!');
		}
		else
		{
			//Parse the configuration paths.
			$this->parseConfigPaths();
			
			//Setup the core's 'my' property.
			$this->my=new stdClass;
			$this->my->name			='core';
			$this->my->dir			=(string)$this->config->path->system;
			$this->my->branchDir	=$this->my->dir;
			$this->my->includeDir	=realpath($this->my->dir.'core')._;
			$this->my->abstractDir	=realpath($this->my->includeDir.'abstract')._;
			$this->my->componentDir	=realpath($this->my->includeDir.'component')._;
			
			//Load the object container and overloader.
			include_once($this->config->path->system.'objectContainer.php');
			include_once($this->config->path->system.'overloader.php');
			
			//Initiate object containers.
			$this->component		=new objectContainer($this,'component');
			$this->application		=new objectContainer($this,'application');
			
			//Load all the core abstract classes.
			foreach (new DirectoryIterator($this->my->abstractDir) as $iteration)
			{
				if ($iteration->isFile())
				{
					include_once($iteration->getPath()._.$iteration->getFilename());
				}
			}
			//Load all the core component classes.
			foreach (new DirectoryIterator($this->my->componentDir) as $iteration)
			{
				if ($iteration->isFile())
				{
					include_once($iteration->getPath()._.$iteration->getFilename());
				}
			}
			//Handle loading the debugger.
			if (isset($this->config->debug) && (bool)(int)$this->config->debug)
			{
				$this->debug=new core_debug;
				error_reporting(E_ALL);
			}
			else
			{
				error_reporting(0);
			}
			//Everything else...
			//Taint Mode
			if ((bool)(int)$this->config->taintmode->active)
			{
				$this->global=new taint((int)$this->config->taintmode->level);
			}
			else
			{
				$this->global=new notaint;
			}
			//Logging
			if ((bool)$this->config->logging['enabled'] && (bool)$this->config->logging->core)
			{
				$this->corelogger=new core_logger('core');
				$this->corelogger->info('Logger Enabled');
			}
			else
			{
				$this->corelogger=new core_logger_dummy;
			}
			//Prepare the URI vars.
			$this->prepareURI();
			//DATABASE
			if (isset($this->config->component->database->connection))
			{
				$this->corelogger->info('Database connection(s) found. Establishing connection(s).');
				//Proceed with initiation.
				if (!$this->component->database->setup($this->config->component->database->connection))
				{
					$this->exception('Database setup failed.',component_database::EXCEPTION_NAME);
				}
//				unset($config);
			}
			//SESSION
			if (isset($this->config->component->session->active) && (bool)(int)$this->config->component->session->active)
			{
				$this->corelogger->info('Database sessions enabled. Establishing/resuming session.');
				if (!isset($this->config->component->database->connection))
				{
					$this->exception('Unable to use session component. The session component depends on an'
									.' active "core" database connection but there is no "core" connection defined in the config.','Session Handler Exception');
				}
				else
				{
					$this->component->session->start();
				}
			}
			else
			{
				$this->corelogger->info('Database sessions NOT enabled. Using vanilla PHP sessions.');
				session_start();
			}
			//PAGE
			if (isset($this->config->component->page->captureBuffer) && (bool)(int)$this->config->component->page->captureBuffer)
			{
				$this->corelogger->info('Page output handling enabled. Taking over output buffering.');
				if (!$this->component->page->startPageCapture())
				{
					$this->exception('Unable to start page capturing.',component_page::EXCEPTION_NAME);
				}
			}
			//APPLICATION
			if (!isset($this->config->defaultApplication))
			{
				$this->exception('Unable to load any applications. A default application was not specified.'
								.' You must specify <defaultApplication></defaultApplication> in the core'
								,' config.xml file.','Core Exception');
			}
			elseif (!is_dir($this->config->path->applications.$this->config->defaultApplication._))
			{
				$this->exception('Unable to load default application. The application doesn\'t exist.'
								.' The default applicaiton is "'.$this->config->defaultApplication.'".','Core Exception');
			}
			elseif (!is_file($this->config->path->applications
			.$this->config->defaultApplication._.$this->config->defaultApplication.'.php'))
			{
				$this->exception('Unable to load default application. The application core doesn\'t exist.'
				.' The default applicaiton is "'.$this->config->defaultApplication.'".','Core Exception');
			}
			else
			{
				$this->corelogger->info('Default application found. Running application -> "'.$this->config->defaultApplication.'".');
				$this->defaultApplication=$this->application->{$this->config->defaultApplication}->run();
			}
		}
	}
	
	public function applicationExists($application=null)
	{
		$return=false;
		if (is_dir($this->config->path->applications.$application._)
		&& is_file($this->config->path->applications
			.$application._.$application.'.php'))
		{
			$return=true;
		}
		return $return;
	}
	
	public function exception($exceptionMessage=null,$exceptionCode=null)
	{
		$this->corelogger->info('Caught Exception. Details follow...');
		$this->corelogger->error($exceptionMessage,'exception');
		if ($this->debug)
		{
			new core_exception($this,$exceptionMessage);
		}
		exit();
	}
	
	public function shutdown()
	{
		if (isset($this->component->database))
		{
			$this->component->database->disconnect();
		}
		/** [PAGE OUTPUT] **/
		if (isset($this->component->page))
		{
			//Stop the page captureing.
			$this->component->page->stopPageCapture();
			//If gzip compression is turned on in the config file, turn it on for the page handler.
			#die(var_dump($this->config->general->gzip));
			if ($this->config->component->page->gzip)$this->component->page->GZIPon();
			//Output the caputred and compressed page.
			print $this->component->page->outputPageCapture($this->config->component->page->gzipLevel);
		}		
		/** [/PAGE OUTPUT] **/
		if (!$this->unitTesting)
		{
			$this->corelogger->info('Tidy Core Shutdown.');
			exit();
		}
		else
		{
			$this->corelogger->info('Core isn\'t fully shutting down because unit testing is in progress.');
		}
	}
	
	private function prepareURI()
	{
		if (is_null($this->global->get('URI')))
		{
			//Catch the URI.
			list($this->URI)=explode('?',$this->global->server('REQUEST_URI'),2);
			$this->URI=preg_replace('@'.addslashes($this->config->path->publicroot).'@i','/',$this->URI);
			$this->URI=str_replace('index.php','',$this->URI);
			//Create nodes from the caught URI.
			$this->nodes=explode('/',$this->URI);
			if (!end($this->nodes))array_pop($this->nodes);
			if (!reset($this->nodes))array_shift($this->nodes);
			//If we have no nodes (root address), specify node 0 as empty.
			if (!isset($this->nodes[0]))$this->nodes[0]='';
			//Create the base path.
			$this->basePath=$this->global->server('HTTP_HOST').$this->config->path->publicroot;
			//Construct the full path from the base path.
			$this->fullPath=$this->basePath.ltrim('/',$this->URI);
			if (count($this->global->get()) && (!empty($this->nodes[0])))$this->fullPath.='?'.$this->global->server('QUERY_STRING');
		}
		else
		{
			//Catch the URI.
			$this->URI=$this->global->get('URI');
			//Create nodes from the caught URI.
			$this->nodes=explode('/',$this->URI);
			if (!end($this->nodes))array_pop($this->nodes);
			if (!reset($this->nodes))array_shift($this->nodes);
			//If we have no nodes (root address), specify node 0 as empty.
			if (!isset($this->nodes[0]))$this->nodes[0]='';
			//Create the base path.
			$this->basePath=$this->global->server('HTTP_HOST').$this->config->path->publicroot.'?URI=';
			//Construct the full path from the base path.
			$this->fullPath=$this->basePath.$this->URI;
			//Reconstruct the get vars onto the full path, filtering out the URI get var.
			foreach ($this->global->get() as $key=>$val)
			{
				if ($key!='URI')$this->fullPath.="&$key=$val";
			}
		}
		return true;
	}
	
	private function parseConfigPaths()
	{
		global $_OVERRIDE;
		if (!isset($this->config->path->root))	$this->config->path->root=dirname(__FILE__)._;
		if (!empty($this->config->path->publicroot))
		{
			$this->config->path->publicroot='/'.$this->config->path->publicroot.'/';
		}
		else
		{
			$this->config->path->publicroot='/';
		}
		if (!empty($this->config->path->publicrootcss))
		{
			$this->config->path->publicrootcss=$this->config->path->publicroot.$this->config->path->publicrootcss.'/';
		}
		if (!empty($this->config->path->publicrootjs))
		{
			$this->config->path->publicrootjs=$this->config->path->publicroot.$this->config->path->publicrootjs.'/';
		}
		if (!empty($this->config->path->publicrootimages))
		{
			$this->config->path->publicrootimages=$this->config->path->publicroot.$this->config->path->publicrootimages.'/';
		}
		if (isset($_OVERRIDE['publichtml']))
		{
			$this->config->path->publichtml=		$_OVERRIDE['publichtml'];
		}
		else
		{
			$this->config->path->publichtml=		$this->config->path->root.$this->config->path->publichtml._;
		}
		$this->config->path->system=			$this->config->path->root.$this->config->path->system._;
		$this->config->path->logs=				$this->config->path->root.$this->config->path->logs._;
		$this->config->path->data=				$this->config->path->system.$this->config->path->data._;
		$this->config->path->debug=				$this->config->path->system.$this->config->path->debug._;
		$this->config->path->core=				$this->config->path->system.$this->config->path->core._;
		$this->config->path->components=		$this->config->path->system.$this->config->path->components._;
		if (isset($_OVERRIDE['applicationsDir']))
		{
			$this->config->path->applications=	realpath($_OVERRIDE['applicationsDir'])._;
		}
		else
		{
			$this->config->path->applications=	realpath($this->config->path->system.$this->config->path->applications)._;
		}
		$fragments=explode(_,$this->config->path->applications);
		if (!end($fragments))array_pop($fragments);
		$this->config->path->publicapplications=$this->config->path->publicroot.end($fragments).'/';
		return true;
	}
	
	private function obtainServerEnvironment()
	{
		$return=false;
		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			if (@preg_match('@apache@i',$_SERVER['SERVER_SOFTWARE']))
			{
				$this->server['application']='apache';
			}
			else
			{
				$this->server['application']='unknown';
			}
		}
		elseif (isset($_SERVER['CLIENTNAME']) && $_SERVER['CLIENTNAME']=='Console')
		{
			$this->server['application']='zendDebugger';
		}
		else if (isset($_SERVER['PHP_SELF']) && stristr($_SERVER['PHP_SELF'],'phpunit'))
		{
			$this->server['application']='phpunit';
			$this->unitTesting			=true;
		}
		if (stristr(PHP_OS,'win'))
		{
			$this->server['os']='windows';
		}
		else
		{
			$this->server['os']='unix';
		}
		$this->server['slash']=DIRECTORY_SEPARATOR;
		if (!defined('_'))define('_',DIRECTORY_SEPARATOR);
		if (!empty($this->server['application'])
		&& !empty($this->server['os'])
		&& !empty($this->server['slash']))
		{
			$return=true;
		}
		return $return;
	}
	
//	public function runApplicationWithBoundAddress($application=null,$address=array())
//	{
//		$return=false;
//		if (!empty($application))
//		{
//			if (!isset($this->application->{$application}))
//			{
//				if (is_dir($this->config->path->applications.$application._)
//				&& (is_file($this->config->path->applications.$application._.$application.'.php')))
//				{
//					include_once($this->config->path->applications.$application._.$application.'.php');
//					$className='application_'.$application;
//					if (class_exists($className))
//					{
//						$return=$this->application->{$application}=new $className($this->core,$address);
//					}
//					else
//					{
//						$this->exception('Application "'.$application.'" has an invalid class name. Expecting "'.$className.'".','Core Object Handler');
//					}
//				}
//				else
//				{
//					$this->exception('Unable to find application "'.$application.'".','Core Object Handler');
//				}
//			}
//			else
//			{
//				$this->exception('');
//			}
//		}
//		return $return;
//	}
}
if (${CORE}=new core)
{
	try
	{
		${CORE}->initiate();
	}
	catch (core_exception $e)
	{
		$e->outputError();
	}
	${CORE}->shutdown();
}
?>