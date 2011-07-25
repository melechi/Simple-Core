<?php
class ext_direct_server extends overloader
{
	public $application		=null;
	public $scope			=null;
	private $server			=null;
	private $providerDir	=null;
	private $JSTemplate		='Ext.namespace(\'{NAMESPACE}\');\n{NAMESPACE}.DIRECT_API={JSON};';
	private $URLTemplate	='{APPLICATION_NAME}/{URLSPACE}/{NAMESPACE}{HANDLER}/';
	private $URLSpace		='direct/API';
	
	
	public function __construct(ext_direct $parent,application $application,$scope,$serverDefPath,$namespace='Ext.app')
	{
		parent::__construct($parent);
		$this->application		=$application;
		$this->scope			=$scope;
		$this->namespace		=$namespace;
		$this->my->dir			=realpath($this->parent->my->includeDir)._;
		$this->my->branchDir	=realpath($this->my->dir.$this->branchFolder)._;
		$this->my->includeDir	=realpath($this->my->dir.$this->xIncludeFolder)._;
		if (empty($serverDefPath) || !is_file($serverDefPath))
		{
			//Error
			$this->exception('Invalid server definition path.');
		}
		else
		{
			$this	->xInclude('provider')
					->xInclude('console');
			$this	->branch('responder','now');
			$this->server		=new config($serverDefPath);
			$this->providerDir	=$this->scope->my->dir.(string)$this->server->providerDir._;
		}
	}
	
	public function generateJavaScriptDefinition()
	{
		if (isset($this->server->providers->provider))
		{
			$api=array();
			foreach ($this->server->providers->provider as $provider)
			{
				if ((string)$provider['type']=='polling')
				{
					$url=str_replace
					(
						array
						(
							'{APPLICATION_NAME}',
							'{URLSPACE}',
							'{NAMESPACE}',
							'{HANDLER}'
						),
						array
						(
							$this->application->my->name,
							$this->URLSpace,
							'',
							(string)$provider['handler'],
						),
						$this->URLTemplate
					);
					$thisProvider=array
					(
						'type'		=>(string)$provider['type'],
//						'url'		=>$this->makeURL($this->application->my->name.'/direct/API/'.(string)$provider['handler'].'/?event=true'),
						'url'		=>$this->makeURL($url),
						'interval'	=>(isset($provider['interval']))?(string)$provider['interval']:3000,
					);
				}
				else
				{
					$url=str_replace
					(
						array
						(
							'{APPLICATION_NAME}',
							'{URLSPACE}',
							'{NAMESPACE}',
							'{HANDLER}'
						),
						array
						(
							$this->application->my->name,
							$this->URLSpace,
							(string)$provider['namespace'],
							''
						),
						$this->URLTemplate
					);
					$thisProvider=array
					(
						'type'		=>(string)$provider['type'],
//						'enableBuffer'=>false,
//						'url'		=>$this->makeURL($this->application->my->name.'/direct/API/'.(string)$provider['namespace'].'/'),
						'url'		=>$this->makeURL($url),
						'namespace'	=>$this->namespace.'.'.$provider['namespace'],
						'actions'	=>array()
					);
				}
				if (isset($provider->module[0]))
				{
					foreach ($provider->module as $providerModule)
					{
						$this->fetchModuleInfo($thisProvider,$providerModule);
					}
				}
				elseif (isset($provider->module))
				{
					$this->fetchModuleInfo($thisProvider,$provider->module);
				}
				$api[]=$thisProvider;
			}
			header('content-type:application/javascript');
			$JSON=json_encode($api);
			str_replace(array(),array(),$this->JSTemplate);
			exit(str_replace(array('{NAMESPACE}','{JSON}'),array($this->namespace,$JSON),$this->JSTemplate));
		}
	}
	
	public function processRequest()
	{
		if ($this->global->post())
		{
			//Non-Batch
			if (!$this->global->post(0))
			{
				$this->handleRequest($this->global->post());
			}
			//Batch
			else
			{
				for ($i=0,$j=count($this->global->post()); $i<$j; $i++)
				{
					$this->handleRequest($this->global->post($i));
				}
			}
		}
		//Polling Request
		else
		{
//			$this->my->branchDir=realpath($this->my->dir.'providers')._;
			include_once($this->providerDir.$this->node(2).'.php');
			$moduleName=$this->scope->constructBranchName($this->node(2));
			$module=new $moduleName($this->scope,$this->responder,null);
//			$module=$this->node(2)
		}
		$this->responder->send();
	}
	
	private function handleRequest($request)
	{
		if ($this->moduleExists($this->node(2),$request['action']))
		{
			include_once($this->providerDir.$this->node(2)._.$request['action'].'.php');
			$moduleName=$this->scope->constructBranchName($this->node(2).'_'.$request['action']);
			$module=new $moduleName($this->scope,$this->responder,$request);
			if (method_exists($module,$request['method']))
			{
				if (!is_array($request['data']))
				{
					$request['data']=array();
				}
				call_user_func_array(array($module,$request['method']),$request['data']);
			}
			else
			{
				die('Module "'.$this->node(2).'::'.$request['action'].'" method "'.$request['method'].'" is invalid.');
			}
		}
		else
		{
			die('Module "'.$this->node(2).'::'.$request['action'].'" could not be found.');
		}
	}
	
	public function providerExists($provider=false)
	{
		//Remoting Provider
		if (is_dir($this->providerDir.$provider))
		{
			foreach ($this->server->providers->provider as $thisProvider)
			{
				if (isset($thisProvider['namespace']) && $thisProvider['namespace']==$provider)
				{
					return true;
				}
			}
		}
		//Polling Provider
		elseif (is_file($this->providerDir.$provider.'.php'))
		{
			return true;
		}
		return false;
	}
	
	private function fetchModuleInfo(&$provider,&$module)
	{
		if (isset($module['name']))
		{
			$provider['actions'][$module['name']]=array();
			if (isset($module->method))
			{
				if (isset($module->method[0]))
				{
					foreach ($module->method as $method)
					{
						array_push
						(
							$provider['actions'][$module['name']],
							array
							(
								'name'	=>$method['name'],
								'len'	=>$method['args']
							)
						);
					}
				}
				elseif (isset($module->method))
				{
					array_push
					(
						$provider['actions'][$module['name']],
						array
						(
							'name'	=>$module->method['name'],
							'len'	=>$module->method['args']
						)
					);
				}
			}
		}
	}
	
	private function moduleExists(&$provider,&$module)
	{
		if (is_file($this->providerDir.$provider._.$module.'.php'))
		{
			foreach ($this->server->providers->provider as $thisProvider)
			{
				if (isset($thisProvider['namespace']) && $thisProvider['namespace']==$provider)
				{
					if (isset($thisProvider->module[0]))
					{
						for ($i=0,$j=count($thisProvider->module); $i<$j; $i++)
						{
							if (isset($thisProvider->module[$i]['name']) && $thisProvider->module[$i]['name']==$module)
							{
								return true;
							}
						}
					}
					else
					{
						if (isset($thisProvider->module['name']) && $thisProvider->module['name']==$module)
						{
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	public function setJSTemplate($template)
	{
		$this->JSTemplate=$template;
		return $this;
	}
	
	public function getJSTemplate()
	{
		return $this->JSTemplate;
	}
	
	public function setURLTemplate($template)
	{
		$this->URLTemplate=$template;
		return $this;
	}
	
	public function getURLTemplate()
	{
		return $this->URLTemplate;
	}
	
	public function setURLSpace($urlSpace)
	{
		$this->URLSpace=$urlSpace;
		return $this;
	}
	
	public function getURLSpace()
	{
		return $this->URLSpace;	
	}
}
?>