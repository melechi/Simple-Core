<?php
class sandbox_event_API extends event
{
	public $server=null;
	
	public function initiate()
	{
		$this->server=$this->component->ext->direct->initServer($this->parent,$this,$this->parent->my->dir.'directServer.xml');
		if (!$this->node(2))
		{
			$this->server->generateJavaScriptDefinition();
		}
		elseif ($this->server->providerExists($this->node(2)))
		{
			$this->server->processRequest();
		}
		exit();
	}
	
	public function getRecentMessages($ids=false)
	{
		if (!isset($this->component->session->messageCache))
		{
			$messageCache=$this->component->session->messageCache=array(0);
		}
		else
		{
			$messageCache=$this->component->session->messageCache;
		}
		$messages=array();
		$time=(time()-300);
		if (!$ids)
		{
			$query=<<<SQL
			SELECT chat_log.*,user_name
			FROM [PREFIX]log
			LEFT JOIN [PREFIX]user ON log_user_id=user_id
			WHERE log_timestamp>{$time}
			AND log_status=1
			ORDER BY log_timestamp ASC;
SQL;
		}
		else
		{
			$query=<<<SQL
			SELECT chat_log.*,user_name
			FROM [PREFIX]log
			LEFT JOIN [PREFIX]user ON log_user_id=user_id
			WHERE log_id IN ({$ids})
			ORDER BY log_timestamp ASC;
SQL;
		}
		if ($this->component->database->c('chat')->query($query))
		{
			$result=$this->component->database->result();
			if (count($result))
			{
				foreach ($result as $result)
				{
					$messageCache[]=$result['log_id'];
					$result['log_timestamp']=date('g:i:s a',$result['log_timestamp']);
					$messages[]=$result;
				}
				$this->component->session->messageCache=$messageCache;
			}
		}
		return $messages;
	}
}
//class sandbox_event_API extends event
//{
//	private $server			=null;
//	private $providerFolder	=null;
//	private $namespace		='Ext.app';
//	
//	public function initiate()
//	{
//		$this->providerFolder=$this->my->dir.'providers'._;
//		$this->server=simplexml_load_file($this->parent->my->dir.'directServer.xml');
//		if (!$this->node(2))
//		{
//			if ($this->global->get('namespace'))
//			{
//				$this->namespace=$this->global->get('namespace');
//			}
//			if(isset($this->server->providers->provider))
//			{
//				$api=array();
//				foreach ($this->server->providers->provider as $provider)
//				{
//					if ((string)$provider['type']=='polling')
//					{
//						$thisProvider=array
//						(
//							'type'		=>(string)$provider['type'],
//							'url'		=>$this->makeURL($this->parent->my->name.'/direct/API/'.(string)$provider['handler'].'/?event=true'),
//							'interval'	=>(isset($provider['interval']))?(string)$provider['interval']:3000,
//						);
//					}
//					else
//					{
//						$thisProvider=array
//						(
//							'type'		=>(string)$provider['type'],
//							'url'		=>$this->makeURL($this->parent->my->name.'/direct/API/'.(string)$provider['namespace'].'/'),
//							'namespace'	=>$this->namespace.'.'.$provider['namespace'],
//							'actions'	=>array()
//						);
//					}
//					if (isset($provider->module[0]))
//					{
//						for ($i=0,$j=count($provider->module); $i<$j; $i++)
//						{
//							foreach ($provider->module[$i] as $providerModule)
//							{
//								$this->fetchModuleInfo($thisProvider,$provider->module[$i]);
//							}
//						}
//					}
//					elseif (isset($provider->module))
//					{
//						foreach ($provider->module as $providerModule)
//						{
//							$this->fetchModuleInfo($thisProvider,$provider->module);
//						}
//					}
//					$api[]=$thisProvider;
//				}
//				header('content-type:application/javascript');
//				$JSON=json_encode($api);
//				die
//				(
//					<<<JS
//Ext.namespace('{$this->namespace}');
//{$this->namespace}.DIRECT_API={$JSON};
//JS
//				);
//			}
//		}
//		elseif ($this->providerExists($this->node(2)))
//		{
//			$this->xInclude('remotingBranch');
//			//Remoting Request
//			if ($this->global->post())
//			{
//				$request=json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
////				if (is_array($request))
////				{
////					for ($i=0,$j=count($request); $i<$j; $i++)
////					{
////						$this->handleRequest($request[$i]);
////					}
////				}
////				else
////				{
////					$this->handleRequest($request);
////				}
//				$this->handleRequest($this->global->post());
//			}
//			//Polling Request
//			else
//			{
//				$this->handleRequest();
//			}
//		}
//		exit();
//	}
//	
//	private function fetchModuleInfo(&$provider,&$module)
//	{
//		if (isset($module['name']))
//		{
//			$provider['actions'][(string)$module['name']]=array();
//			if (isset($module->method))
//			{
//				foreach ($module->method as $method)
//				{
//					array_push
//					(
//						$provider['actions'][(string)$module['name']],
//						array
//						(
//							'name'	=>(string)$method['name'],
//							'len'	=>(string)$method['args']
//						)
//					);
//				}
//			}
//		}
//	}
//	
//	private function handleRequest(&$request=false)
//	{
//		if ($request)
//		{
//			if (!$this->moduleExists($this->node(2),$request['action']))
//			{
//				//error
//				print 'error1';
//			}
//			else
//			{
//				$this->my->branchDir=realpath($this->my->dir.'providers'._.$this->node(2))._;
//				$this->branch($request['action'],'now');
//				if (!method_exists($this->{$request['action']},$request['method']))
//				{
//					//error
//					print 'error2';
//				}
//				else
//				{
//					call_user_func_array(array($this->{$request['action']},$request['method']),$request['data']);
//				}
//			}
//		}
//		else
//		{
//			$this->my->branchDir=realpath($this->my->dir.'providers')._;
//			$this->branch($this->node(2),'now');
//		}
//	}
//	
//	private function providerExists($provider=false)
//	{
//		//Remoting Provider
//		if (is_dir($this->providerFolder.$provider))
//		{
//			foreach ($this->server->providers->provider as $thisProvider)
//			{
//				if (isset($thisProvider['namespace']) && $thisProvider['namespace']==$provider)
//				{
//					return true;
//				}
//			}
//		}
//		//Polling Provider
//		elseif (is_file($this->providerFolder.$provider.'.php'))
//		{
//			return true;
//		}
//		return false;
//	}
//	
//	private function moduleExists(&$provider,&$module)
//	{
//		if (is_file($this->providerFolder.$provider._.$module.'.php'))
//		{
//			foreach ($this->server->providers->provider as $thisProvider)
//			{
//				if (isset($thisProvider['namespace']) && $thisProvider['namespace']==$provider)
//				{
//					if (is_array($thisProvider->module))
//					{
//						for ($i=0,$j=count($thisProvider->module); $i<$j; $i++)
//						{
//							foreach ($thisProvider->module[$i] as $providerModule)
//							{
//								if (isset($providerModule['name']) && $providerModule==$module)
//								{
//									return true;
//								}
//							}
//						}
//					}
//					else
//					{
//						foreach ($thisProvider->module as $providerModule)
//						{
//							if (isset($providerModule['name']) && $providerModule['name']==$module)
//							{
//								return true;
//							}
//						}
//					}
//				}
//			}
//		}
//		return false;
//	}
//	
//	public function getRecentMessages($ids=false)
//	{
//		if (!isset($this->component->session->messageCache))
//		{
//			$messageCache=$this->component->session->messageCache=array(0);
//		}
//		else
//		{
//			$messageCache=$this->component->session->messageCache;
//		}
//		$messages=array();
//		$time=(time()-300);
//		if (!$ids)
//		{
//			$query=<<<SQL
//			SELECT chat_log.*,user_name
//			FROM [PREFIX]log
//			LEFT JOIN [PREFIX]user ON log_user_id=user_id
//			WHERE log_timestamp>{$time}
//			AND log_status=1
//			ORDER BY log_timestamp ASC;
//SQL;
//		}
//		else
//		{
//			$query=<<<SQL
//			SELECT chat_log.*,user_name
//			FROM [PREFIX]log
//			LEFT JOIN [PREFIX]user ON log_user_id=user_id
//			WHERE log_id IN ({$ids})
//			ORDER BY log_timestamp ASC;
//SQL;
//		}
//		if ($this->component->database->c('chat')->query($query))
//		{
//			$result=$this->component->database->result();
//			if (count($result))
//			{
//				foreach ($result as $result)
//				{
//					$messageCache[]=$result['log_id'];
//					$result['log_timestamp']=date('g:i:s a',$result['log_timestamp']);
//					$messages[]=$result;
//				}
//				$this->component->session->messageCache=$messageCache;
//			}
//		}
//		return $messages;
//	}
//}
?>