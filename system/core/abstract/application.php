<?php
/*
 * Simple Core 2
 * Copyright(c) 2004-2009, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Core application class.
 *
 * All applications in Simple Core stem from this class.
 * It is the root of all smart application-specific logic.
 *
 * It is important to fully understand the flow of Simple Core applications.
 *
 * *An application starts when an entity within the system requests the use of an application.
 * *The application is checked for existance, loaded into memory and then constructed - all by the Simple Core.
 * *The application core (this class) exclucively handles the construction of the application.
 * *During construction, the applications settings.xml file will be loaded if the $hasSettings attribute has been
 *  flagged as true. These settings will be mapped and any database connection requests found within these settings
 *  will be handled. Additionally, authentication proceedures will be handled should the $hasAuthentication attribute
 *  be flagged as true.
 * *At this stage, the non-core construction for the application takes place by calling the applications {@link initiate()}
 *  method.
 * *Once the construction is complete, the application idles in a ready state until the Simple Core executes it.
 * *Execution is exclusive to the application core snd is handled by the {@link execute()} method.
 * *Everything after this is handled by the application itself with the exception of database cleanups which is handled
 *  by the Simple Core during is destruction phase.
 *
 * @abstract
 * @author Timothy Chandler
 * @version 1.0
 * @copyright Simple Site Solutions 17/11/2007
 */
abstract class application extends overloader
{
	/**
	 * Abstract method for constructing an application after core-application construction.
	 *
	 * Abstract method for constructing an application after core-application construction
	 * has been completed. If this method returns false, an exception will be thrown. This
	 * can be used as the basis for application-specific initialization errors.
	 * @access public
	 * @return bool
	 */
	abstract function initiate();

	public $hasSettings			=false;
	public $hasAuthentication	=false;
	public $useBreadcrumbs		=false;
	public $useSmarty			=false;
	public $settings			=false;
	/**
	 * @var sitemap This is the sitemap! Wooo!
	 */
	public $sitemap				=false;
	public $eventmap			=false;
	public $event				=false;
	public $breadcrumbs			=false;
	public $smarty				=false;
	public $formManager			=array();
	public $ready				=false;
	public $initiated			=false;
	public $templateSettings	=array();
	public $plugins				=array();
	
	public $isBound			=false;
	public $binding			=array
	(
		'boundTo'=>null,
		'rootURL'=>array
		(
			'original'	=>null,
			'root'		=>null,
			'bound'		=>null
		)
	);
	
	/**
	 * Constructor. Handles core application mapping and logic.
	 *
	 * This method can never be overwritten by Simple Core applications. This is so that
	 * the process of creating an application is simplfied. The Simple Core requires certain
	 * logic to be handled by this application core, so by removing the possibility for
	 * parent::__construct() to be forgotten in constructor methods, it also removes a lot of
	 * confusion and errors.
	 *
	 * That's not to say that applications cannot have a constructor. However it the
	 * {@link initiate()} method that must be used instead.
	 *
	 * @final
	 * @access public
	 * @return self
	 */
	
	final public function __construct($parent)
	{
		parent::__construct($parent);
		$this->my->address				=$this->makeURL('');
		$this->my->secureAddress		=$this->makeURL('','https');
		$this->my->contentAddress		=$this->my->address.'applications/'.$this->my->name.'/';
		$this->my->secureContentAddress	=$this->my->secureAddress.'applications/'.$this->my->name.'/';
		//SETTINGS
		if ($this->hasSettings)
		{
			if (is_file($this->my->dir.'settings.xml'))
			{
				$rawSettings=file_get_contents($this->my->dir.'settings.xml');
				$fNr=array();
				foreach ($this->my as $key=>$val)
				{
					$fNr['{$my->'.$key.'}']=$val;
				}
				foreach ($this->config->path->toArray() as $key=>$val)
				{
					$fNr['{$config->path->'.$key.'}']=$val;
				}
//				$this->settings=simplexml_load_string(str_replace(array_keys($fNr),array_values($fNr),$rawSettings));
//				$this->settings=simplexml_load_file($this->my->dir.'settings.xml');
				$this->settings=new config(str_replace(array_keys($fNr),array_values($fNr),$rawSettings));
				if (!$this->settings)
				{
					$this->exception('Unable to load appliaction settings.');
				}
				else
				{
					if (isset($this->settings->database))
					{
						$this->component->database->setup($this->settings->database);
//						$connections=$this->settings->database;
////						print_r($this->settings);exit('END:APPLICATION');
//						
////						exit();
//						foreach ($this->settings->database->toArray() as $connectionSettings)
//						{print 123;var_dump($connectionSettings);exit();
//							$this->component->database->newConnection($connectionSettings);
//						}
					}
				}
			}
			else
			{
				$this->exception('Application settings file not found. $hasSettings has been set to true, this means that'
								.' you must have a settings.xml file in the same folder that your main applicaiton .php file sits.');
			}
		}
		//SMARTY
		if ($this->useSmarty)
		{
			$this->smarty=$this->component->smarty;
			if ($this->hasSettings && (isset($this->settings->component->smarty)))
			{
				//TODO: finish this
			}
			if (method_exists($this,'smarty'))
			{
				$this->smarty();
			}
		}
		//SESSIONS
		if (isset($this->config->component->session->active)
		&& (bool)(int)$this->config->component->session->active
		&& $this->component->session->sessionStarted()
		&& isset($this->config->component->database->connection))
		{
			if (isset($this->settings->component) && isset($this->settings->component->session->onSessionExpire))
			{
				if ($this->component->session->isExpired((double)(string)$this->settings->component->session->expireTime))
				{
					if (!method_exists($this,(string)$this->settings->component->session->onSessionExpire))
					{
						$this->exception('onSessionExpire is defined in settings.xml but no onSessionExpire method was found in application.');
					}
					else
					{
						$this->{(string)$this->settings->component->session->onSessionExpire}();
					}
				}
				else
				{
					$this->component->session->updateTouched();
				}
			}
			else
			{
				if ($this->component->session->isExpired())
				{
					$this->component->session->destroy();
					$this->component->session->start();
				}
				else
				{
					if (!$this->component->session->updateTouched())
					{
						$this->component->session->destroy();
						$this->component->session->start();
					}
				}
			}
			//AUTHENTICATION - Depends on session.
			if ($this->hasAuthentication)
			{
				$this->setTemplateVar('AUTHENTICATED',$this->component->account->authentication->isAuthenticated());
				$this->setTemplateVar('USERNAME',$this->component->session->account_username);
				$this->setTemplateVar('EMAIL',$this->component->session->account_email);
				if (is_object($this->settings))
				{
					//Handle privilege definitions if there are any.
					if (isset($this->settings->component) && isset($this->settings->component->account->privileges))
					{
						$defMap=array();
						//Start by defining placements.
						$placement=1;
						foreach ($this->settings->component->account->privileges->definitions->children() as $val)
						{
							$defMap[(string)$val['name']]=$placement;
							$val->addAttribute('placement',$placement);
							$placement*=2;
						}
						//Now get the values for the sets.
						foreach ($this->settings->component->account->privileges->sets->children() as $val)
						{
							$table=explode(',',(string)$val['definitions']);
							$table=array_map('trim',$table);
							for ($i=0,$j=count($table); $i<$j; $i++)
							{
								if (isset($defMap[$table[$i]]))
								{
									$table[$i]=&$defMap[$table[$i]];
								}
								else
								{
									$this->exception('Account Privilege Mapping error. Privilege "'.$table[$i].'" is no defined.');
								}
							}
							$val->addAttribute('value',array_sum($table));
						}
					}
					if (isset($this->settings->component) && isset($this->settings->component->account))$this->component->account->authentication->useSettings($this->settings->component->account);
				}
			}
		}
		//FORMS
		if (is_dir($this->my->dir.'forms'._))
		{
			foreach (new DirectoryIterator($this->my->dir.'forms'._) as $iteration)
			{
				if ($iteration->isFile())
				{
					$info=pathinfo($iteration->getFilename());
					if ($info['extension']=='xml')
					{
						$this->formManager[$info['filename']]=$iteration->getPath()._.$iteration->getFilename();
					}
				}
			}
		}
		//ACTIVE RECORDS
		if (is_dir($this->my->dir.'activeRecords'._))
		{
			$this->registerActiveRecords();
		}
		//BREADCRUMBS
		if ($this->useBreadcrumbs)
		{
			$this->breadcrumbs=$this->component->breadcrumbs;
			$this->breadcrumbs->generateCrumbs();
		}
		//Set Ready.
		$this->ready=true;
		return $this;
	}

	public function setMyDir()
	{
		$this->my->dir=$this->config->path->applications.str_replace('application_','',get_class($this))._;
		$fragments=explode(_,$this->config->path->applications);
		if (!end($fragments))array_pop($fragments);
		$this->my->publicDir=$this->config->path->publichtml.end($fragments)._.$this->my->name._;
		return true;
	}

	/**
	 * Variable getting overloader.
	 *
	 * Adds an extra step before the core overloader does it's job.
	 * The extra step checks to see if a sleeping branch exists by
	 * that variable key and initiates it if it does.
	 *
	 * @access public
	 * @return mixed
	 */

	public function __get($theVar)
	{
		$return=null;
		if ($this->branchContainer_exists($theVar))
		{
			$return=$this->branchContainer_initiate($theVar);
		}
		else
		{
			$return=parent::__get($theVar);
		}
		return $return;
	}

	/**
	 * Primary execution method for all applications.
	 *
	 * This is the primary execution method for all applications. It is exclucive
	 * to the core-application class (this class) and cannot be extended or overwritten.
	 * This is because Simple Core's core pattern is a MVC.
	 *
	 * The Simple Core, acting as the controller invokes this execute method which
	 * acts as a wrapper for the model and view.
	 *
	 * The model and view are created as singletons. Eventmap represents the model and
	 * affects the actions of the Sitemap which acts as the view.
	 *
	 * @final
	 * @access public
	 * @return bool
	 * @todo the way that sitemap and eventmap work will most likely
	 * cause major issues with PHP 5.3 because of the new invoke closure functionality.
	 */

	final public function execute()
	{
		if ((count($this->global->post())
		|| (bool)$this->global->get('event')===true
		|| $this->global->server('HTTP_X_REQUESTED_WITH')=='XMLHttpRequest')
		&& method_exists($this,'eventmap'))
		{
			$this->eventmap=new eventmap($this);
			$this->eventmap->bindToAddress($this->node());
			if (!$this->unitTesting)
			{
				$this->eventmap();
			}
		}
		if (method_exists($this,'sitemap'))
		{
			$this->sitemap=new sitemap($this);
			$this->sitemap->bindToAddress($this->node());
			$this->sitemap->event=$this->eventmap;
			if (is_array($this->templateSettings) && count($this->templateSettings))
			{
				$this->sitemap->mergeTemplateSettings($this->templateSettings);
			}
			if (!$this->unitTesting)
			{
				$this->sitemap();
			}
		}
		return true;
	}
	
	/**
	 * Dummy method for other applications to use.
	 * 
	 * This method will force the core to initiate the applcation but not
	 * exeucte it. This allows another application to take control of this
	 * application, bind addresses and execute it etc.
	 * 
	 * @final 
	 * @access public
	 * @return application
	 */
	
	final public function run($bindTo=false)
	{
		if ($bindTo!==false)$this->bindToAddress($bindTo);
		if (!$this->initiate())
		{
			$this->exception('Application failed to initiate. A successful initiation must return true.');
		}
		else
		{
			$this->initiated=true;
			$this->execute();
		}
		return $this;
	}
	
	final public function dryRun($bindTo=false)
	{
		if ($bindTo!==false)$this->bindToAddress($bindTo);
		return $this;
	}

	/**
	 * Intended to quickly bind paths togeather with an array based on OS.
	 *
	 * Does not work properly for some reason.
	 *
	 * @todo This has a bug - fix it.
	 * @access public
	 * @return string
	 */

	public function makePath()
	{
		return @implode(_,@func_get_args())._;
	}

	/**
	 * A location header wrapper method for safe redirects.
	 *
	 * @access public
	 * @return bool
	 */

	public function forward($location=null,$makeURL=false,$protocol='http')
	{
		if ($location)
		{
			if (!$this->unitTesting)
			{
				if (ob_get_length())ob_clean();
				header('location: '.(($makeURL)?$this->makeURL($location,$protocol):$location).'');
				exit();
			}
			else
			{
				print 'SET-HEADER::location:'.(($makeURL)?$this->makeURL($location,$protocol):$location).'';
			}
		}
		return false;
	}

	/**
	 * Serial checking method.
	 *
	 * Takes a string as its argument and
	 * checks to see if it can unserialize
	 * it. If it fails, its not a serialized
	 * array.
	 *
	 * @access public
	 * @return bool
	 */

	public function is_serial($theString=null)
	{
		return (@is_array(@unserialize($theString)))?true:false;
	}
	
	/**
	 * Sets a template var according to the template handler that is being used.
	 * 
	 * By using the third parameter, you can specify the scope. Scope is managed and will
	 * be created automatically if it doesn't exist. 
	 * 
	 * @param string $key Name to reference the variable by. ({$:KEY}).
	 * @param string $value The value of the variable.
	 * @param string $scope Which scope this template variable exists in. Defaults to GLOBAL. ({$SCOPE:KEY})
	 * @return $this
	 */
	
	public function setTemplateVar($key,$value=null,$scope=null)
	{
		if (empty($key))
		{
			$this->exception('Unable to set template var. Invalid $key.');
		}
		else
		{
			if ($this->useSmarty)
			{
				$this->smarty->assign($key,$value);
			}
			else
			{
				if (empty($scope))
				{
					$this->component->template->{$key}=$value;
				}
				else
				{
					if (!$this->component->template->scopeExists($scope))
					{
						$this->component->template->newScope($scope);
					}
					$this->component->template->{$scope}->{$key}=$value;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Gets a template var according to the template handler that is being used.
	 * 
	 * By using the second parameter, you can specify the scope. Scope is managed and will
	 * be created automatically if it doesn't exist. 
	 * 
	 * @param string $key Name to reference the variable by. ({$:KEY}).
	 * @param string $scope Which scope this template variable exists in. Defaults to GLOBAL. ({$SCOPE:KEY})
	 * @return mixed
	 */
	public function getTemplateVar($key=null,$scope=null)
	{
		$return=false;
		if (empty($key))
		{
			$this->exception('Unable to get template var. Invalid $key.');
		}
		else
		{
			if ($this->useSmarty)
			{
//				$this->smarty->assign($key,$value);
				//TODO: finish this.
			}
			else
			{
				if (empty($scope))
				{
					$return=$this->component->template->{$key};
				}
				else
				{
					if (!$this->component->template->scopeExists($scope))
					{
						$this->exception('Unable to get template var. The scope "'.$scope.'" doesn\'t exist.');
					}
					else
					{
						$return=$this->component->template->{$scope}->{$key};
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * Same as setTemplateVar() but sets the value by reference.
	 * 
	 * This is so that you can bind a variable to a template variable and then keep modifiying
	 * the original and not have to worry about updating the template variable.
	 * 
	 * @param string $key Name to reference the variable by. ({$:KEY}).
	 * @param string $value The value of the variable.
	 * @param string $scope Which scope this template variable exists in. Defaults to GLOBAL. ({$SCOPE:KEY})
	 * @return boolean
	 */
	
	public function setBoundTemplateVar($key=null,&$value=null,$scope=null)
	{
		$return=false;
		if (empty($key))
		{
			$this->exception('Unable to set template var. Invalid $key.');
		}
		else
		{
			$return=true;
			if ($this->useSmarty)
			{
				$this->smarty->assign($key,$value);
			}
			else
			{
				if (empty($scope))
				{
					$this->component->template->global->setBound($key,$value);
				}
				else
				{
					if (!$this->component->template->scopeExists($scope))
					{
						$this->component->template->newScope($scope);
					}
					$this->component->template->{$scope}->setBound($key,$value);
				}
			}
		}
		return $return;
	}

//	/**
//	 * Application bridging method.
//	 *
//	 * This needs to be throughly tested...
//	 * @todo test test test!!!
//	 *
//	 * @param string $application The name of the application
//	 * @param array $matchAddress The address to match for execution of $application (assumes ? as last parameter).
//	 * @return bool
//	 * @depreciated
//	 */
//
//	public function bridge($application=null,$matchAddress=array())
//	{
//		$return=false;
//		if ($this->applicationExists($application))
//		{
//			$return=true;
//			$matchAddress[]='?';
//			//Loop through and validate the page path, grabbing the end array if there is one.
//			for ($i=0,$j=count($matchAddress); $i<$j; $i++)
//			{
//				if (is_string($matchAddress[$i]))
//				{
//					$thisNode=$this->node($i)?$this->node($i):'';
//					if ($matchAddress[$i]!=$thisNode)
//					{
//						//At this point, the page path may be invalid. We need to make sure it doesn't contain dynamic constructs.
//						if (strstr($matchAddress[$i],'|'))
//						{
//							$fragments=explode('|',$matchAddress[$i]);
//							$gotMatch=false;
//							for ($j=0,$k=count($fragments); $j<$k; $j++)
//							{
//								if ($fragments[$j]==$thisNode)
//								{
//									$gotMatch=true;
//									break;
//								}
//							}
//							if (!$gotMatch)
//							{
//								$return=false;
//								break;
//							}
//						}
//						elseif ($matchAddress[$i]=='?')
//						{
//							break;
//						}
//						else
//						{
//							$return=false;
//							break;
//						}
//					}
//				}
//				else
//				{
//					$return=false;
//					break;
//				}
//			}
//			if ($return)
//			{
//				$boundAddress=array();
//				for ($i=0,$j=$this->numNodes(); $i<$j; $i++)
//				{
//					if (!isset($matchAddress[$i]))
//					{
//						$boundAddress[]=$this->node($i);
//					}
//					elseif ($matchAddress[$i]!=$this->node($i))
//					{
//						$boundAddress[]=$this->node($i);
//					}
//				}
//				call_user_func(array($this->application->{$application},'bindToAddress'),$boundAddress);
//				$this->application->{$application}->execute();
//			}
//		}
//		else
//		{
//			$return=false;
//			$this->exception('Unable to bridge application "'.$this->parent->my->name.'" to "'.$application.'".'
//							.' Application "'.$application.'" does not exist.');
//		}
//		return $return;
//	}

	public function newForm($name=null,$forceMode=false)
	{
		return $this->newFMLInstance($name,$forceMode);
	}

	public function newFMLInstance($name=null,$forceMode=false)
	{
		$return=false;
		if ($this->component->fml->instanceExists($name))
		{
			$return=$this->component->fml->getInstance($name);
		}
		else
		{
			$return=$this->component->fml->newInstance($this,$name,$this->formManager[$name],$forceMode);
		}
		return $return;
	}
	
	public function bindApplication($toApp,Array $rootAddress,Array $boundAddress=array())
	{
		if ($this->applicationExists($toApp))
		{
			if (!count($boundAddress))
			{
				$boundAddress=$this->node();
				//NB: We do not cache the count because it changes.
				for ($i=0; $i<count($boundAddress); $i++)
				{
					if (!isset($rootAddress[$i]))break;
					if ($boundAddress[$i]==$rootAddress[$i])
					{
						array_shift($boundAddress);
					}
				}
			}
			$toApp			=$this->application->{$toApp}->dryRun($boundAddress);
			if ($this->sitemap instanceof sitemap)
			{
				$this->sitemap->bindToAddress($boundAddress);
			}
			if ($this->eventmap instanceof eventmap)
			{
				$this->eventmap->bindToAddress($boundAddress);
			}
			$toApp->isBound	=true;
			$toApp->binding	=array
			(
//				'boundTo'=>$this,
				'rootURL'=>array
				(
					'original'	=>$this->makeURL('',''),
					'root'		=>$this->makeURL($rootAddress,''),
					'bound'		=>$this->makeURL($boundAddress,'')
				)
			);
			$toApp->my->address					=&$toApp->binding['rootURL']['root'];
			$toApp->my->secureAddress			=str_replace('http','https',$toApp->my->address);
			$toApp->my->contentAddress			=$toApp->my->address.'applications/'.$this->my->name.'/';
			$toApp->my->secureContentAddress	=$toApp->my->secureAddress.'applications/'.$this->my->name.'/';
			if (!$toApp->initiate())
			{
				$toApp->exception('Application failed to initiate. A successful initiation must return true.');
			}
			else
			{
				$toApp->initiated=true;
				$toApp->execute();
			}
		}
		else
		{
			$this->exception('Unable to bind application "'.$this->parent->my->name.'" to "'.$toApp.'".'
							.' Application "'.$toApp.'" does not exist.');
		}
	}
	
	private function registerActiveRecords()
	{
		if (is_dir($this->my->dir.'activeRecords'._))
		{
			foreach (new DirectoryIterator($this->my->dir.'activeRecords'._) as $iteration)
			{
				if ($iteration->isFile())
				{
					$info=pathinfo($iteration->getFilename());
					if ($info['extension']=='xml')
					{
						$this->component->orm->register($info['filename'],$iteration->getPath()._.$iteration->getFilename());
					}
				}
			}
		}
		return $this;
	}
	
	private function loadPlugins()
	{
		foreach (new DirectoryIterator($this->my->dir.'plugins'._) as $iteration)
		{
			if ($iteration->isFile())
			{
				$info=pathinfo($iteration->getFilename());
				if ($info['extension']=='php')
				{
					
//					$this->formManager[$info['filename']]=$iteration->getPath()._.$iteration->getFilename();
				}
			}
		}
		return $this;
	}
	
	public function activateAdministration()
	{
		include_once($this->my->dir.'_admin'._.'adminControlPanel.php');
		include_once($this->my->dir.'admin.php');
		return new application_core_admin($this);
	}
}
?>