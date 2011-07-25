<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class account_authentication extends branch
{
	private $settings=false;
	
	public function useSettings($theSettings=null)
	{
		$return=false;
		if (!$theSettings instanceof SimpleXMLElement)
		{
			$this->exception('Parameter passed in was not a valid SimpleXMLElement object.');
		}
		else
		{
			$this->settings=$theSettings;
		}
		return $return;
	}
	
	public function haveSettings()
	{
		return ($this->settings instanceof SimpleXMLElement)?true:false;
	}
	
	public function login($namespace='')
	{
		$return=false;
		if (($this->global->post('account_username') || $this->global->post('account_email'))
		&& $this->global->post('account_password'))
		{
			$field=$this->global->post('account_username')?'account_username':'account_email';
			$query=<<<SQL
			SELECT account_id,{$field},account_status,account_privs,account_namespace
			FROM [PREFIX]account
			WHERE account_email='{$this->global->post($field)}'
			AND account_password=MD5('{$this->global->post('account_password')}')
			AND account_status='1' OR account_status='2'
			LIMIT 1;
SQL;
			if ($this->parent->component->database->c('core')->query($query))
			{
				$validNamespace=true;
				$details=$this->parent->component->database->result();
				if (!empty($namespace))
				{
					$namespaces=explode(',',$details['account_namespace']);
					if (!in_array($namespace,$namespaces))
					{
						$validNamespace=false;
					}
				}
				if ($validNamespace)
				{
					if (!isset($this->parent->component->session->authenticatedNamepsaces))
					{
						$authNS=array();
					}
					else
					{
						$authNS=$this->parent->component->session->authenticatedNamepsaces;
					}
					$authNS[]=$namespace;
					$this->parent->component->session->authenticatedNamepsaces=$authNS;
					$this->parent->component->session->authenticated=1;
					$this->parent->component->session->account_id=$details['account_id'];
					$this->parent->component->session->{$field}=$details[$field];
					$this->parent->component->session->account_status=$details['account_status'];
					$this->parent->component->session->account_privs=$details['account_privs'];
					$this->parent->component->session->namespace=$details['account_namespace'];
					$return=true;
				}
				$this->parent->updateLastLogin($details['account_id']);
			}
		}
		return $return;
	}
	
	public function logout($namespace='')
	{
		if (!empty($namespace))
		{
			if (!in_array($namespace,explode(',',$this->parent->component->session->namespaces)))
			{
				$this->component->session->destroy();
			}
			else
			{
				$namespaces=explode(',',$this->parent->component->session->authenticatedNamepsaces);
				for ($i=0,$j=count($namespaces); $i<$j; $i++)
				{
					if ($namespaces[$i]==$namespace)
					{
						unset($namespaces[$i]);
						sort($namespaces);
						break;
					}
				}
				if (!count($namespaces))
				{
					$this->component->session->destroy();
				}
				else
				{
					$this->parent->component->session->namespace=$namespaces;
				}
			}
		}
		else
		{
			$this->component->session->destroy();
		}
		return true;
	}
	
	public function isAuthenticated($namespace='')
	{
		$return=false;
		if (!empty($namespace))
		{
			$return=in_array($namespace,$this->parent->component->session->authenticatedNamepsaces);
		}
		else
		{
			$return=$this->component->session->authenticated;
		}
		return $return;
	}
	
	public function challenge()
	{
		$return=false;
		if (!$this->haveSettings())
		{
			$this->exception('Unable to perform challenge. Settings have not been defined. Use the "useSettings()" method'
									.' and pass it the account component settings in your settings.xml file.');
		}
		else
		{
			$privilege=func_get_args();
			//Proceed only if we have a left and right arg.
			if (func_num_args()>=2)
			{
				$return=true;
				$set=reset($privilege);
				array_shift($privilege);
				$numArgs=count($privilege);
				//You can challenge more then one privilege
				for ($i=0; $i<$numArgs; $i++)
				{
					$queryResult=$this->settings->privileges->xpath('definitions/definition[@name="'.$privilege[$i].'"]/@placement');
					if (!isset($queryResult[0]['placement']))
					{
						$this->exception('Unable to perform challenge. The XPath query failed. This means that the definitions'
												.' table is not configured properly in the application\'s settings.xml file.');
					}
					else
					{
						$privilege[$i]=(int)$queryResult[0]['placement'];
						if (($privilege[$i] & $set)!=$privilege[$i])
						{
							$return=false;
							break;
						}
					}
				}
			}
		}
		return $return;
	}
	
	public function challengeSet($challange=0,$theSet=0)
	{
		$return=true;
		$queryResult=$this->settings->privileges->xpath('sets/set[@name="'.$theSet.'"]/@value');
		if (!isset($queryResult[0]['value']))
		{
			$this->exception('Unable to perform set challenge. The XPath query failed. This means that the definitions'
									.' table is not configured properly in the application\'s settings.xml file.');
		}
		else
		{
			$theSet=(int)$queryResult[0]['value'];
			if (($theSet & $challange)!=$theSet)
			{
				$return=false;
			}
		}
		return $return;
	}
}
?>