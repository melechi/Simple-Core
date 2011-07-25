<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class component_fml extends component
{
	const EXCEPTION_NAME='Form Markup Language Exception';
	
	const MODE_CREATE=1;
	const MODE_VALIDATE=2;
	
	private $instances=null;
	public $var=null;
	public $local=array();
	private $fileCache=array();
	
	public function initiate()
	{
		//Set definitions.
		define('FML_MODE_CREATE',	self::MODE_CREATE);
		define('FML_MODE_VALIDATE',	self::MODE_VALIDATE);
		//Set instance container.
		$this->instances=new stdClass;
		//Set var container.
		$this->var=new hash;
		//Includes.
		$this->xInclude('instance');
		$this->xInclude('element');
		$this->xInclude('fieldtype');
		$this->xInclude('ruletype');
		//Branches.
		$this->branch('exception');
		$this->branch('datatype','now');
		$this->branch('subfml','now');
		return true;
	}
	
	public function error($file=null,$line=0,$message=null)
	{
		$this->exception($message);
	}
	
	public function newInstance(application $scope,$referenceName=null,$theForm=null,$forceMode=false)
	{
		$return=false;
		if (empty($referenceName))
		{
			$this->exception('Unable to generate FML form. Form reference cannot be blank.');
		}
		elseif ($this->instanceExists($referenceName))
		{
			$this->exception('Unable to generate FML form. A form by the given reference already exists.');
		}
		elseif (!is_file($theForm))
		{
			$this->exception('Unable to generate FML form. "'.$theForm.'" was not found.');
		}
		else
		{
			if ($forceMode===self::MODE_CREATE || $forceMode===self::MODE_VALIDATE)
			{
				$mode=$forceMode;
			}
			else
			{
				$mode=(!count($this->global->post()))?self::MODE_CREATE:self::MODE_VALIDATE;
			}
			$this->instances->{$referenceName}=new fml_instance($this,$scope,$theForm,$mode);
			$return=$this->instances->{$referenceName};
		}
		return $return;
	}
	
	public function instanceExists($reference=null)
	{
		return isset($this->instances->{$reference});
	}
	
	public function getInstance($reference=null)
	{
		$return=false;
		if (empty($reference))
		{
			$this->exception('Unable to get instance. Instance reference cannot be blank.');
		}
		elseif (!$this->instanceExists($reference))
		{
			$this->exception('Unable to get instance. Instance "'.$reference.'" does not exist.');
		}
		else
		{
			$return=$this->instances->{$reference};
		}
		return $return;
	}
	
	public function fileIsCached($file=null)
	{
		return isset($this->fileCache[$file]);
	}
	
	public function cacheFile($file=null,$contents=false)
	{
		$return=false;
		if (!is_file($file))
		{
			$this->exception('Unable to cache FML document. "'.$file.'" was not found.');
		}
		elseif ($contents===false)
		{
			$this->fileCache[$file]=file_get_contents($file);
		}
		else
		{
			$this->fileCache[$file]=$contents;
		}
		return $return;
	}
	
	/**
	 * 
	 * NOTE: Auto attempts to load and cache a non-cached file.
	 * 
	 * @param string $file
	 * @return string
	 */
	
	public function loadCachedFile($file=null)
	{
		if (!$this->fileIsCached($file))
		{
			$this->cacheFile($file);
		}
		return $this->fileCache[$file];
	}
	
	public function setVar($key,$val)
	{
		$this->var->set($key,$val);
		return $this;
	}
	
	public function getVar($key)
	{
		return $this->var->get($key);
	}
	
	public function isLocalDefined($local=null)
	{
		return isset($this->local[$local]);
	}
	
	public function createLocal($local=null)
	{
		$return=false;
		if (!empty($local))
		{
			$this->local[$local]=new hash;
			$return=true;
		}
		return $return;
	}
	
	public function loadLocalDefinitionsFromFile(application $scope,$local=null)
	{
		$return=false;
		$file=$scope->my->dir.'forms'._.'locals'._.$local.'.xml';
		if (!is_file($file))
		{
			$this->exception('Unable to locate FML definition file. "'.$file.'" was not found.');
		}
		else
		{
			$definitions=simplexml_load_file($file);
			if ((string)$definitions['local']!=$local)
			{
				$this->exception('FML definition local type mismatch. Expected "'.$local.'" but got "'.$definitions['local'].'".');
			}
			else
			{
				foreach ($definitions->children() as $definition)
				{
					$this->setLocalDefinition($local,(string)$definition['key'],(string)$definition);
				}
			}
		}
		return $return;
	}
	
	public function setLocalDefinition($local=null,$key=null,$val='')
	{
		$return=false;
		if (!empty($local) && !empty($key))
		{
			if (!$this->isLocalDefined($local))$this->createLocal($local);
			$this->local[$local]->set($key,$val);
			$return=true;
		}
		return $return;
	}
	
	public function getLocalDefinition($local=null,$key=null)
	{
		$return=null;
		if (!empty($key) && $this->isLocalDefined($local))
		{
			$return=$this->local[$local]->get($key);
		}
		return $return;
	}
	
	public function registerCustomRule($filePath=null)
	{
		if (empty($filePath))
		{
			$this->exception('Unable to load custom FLM rule. Invalid file path.');
		}
		elseif (!file_exists($filePath))
		{
			$this->exception('Unable to load custom FLM rule. "'.$filePath.'" could not be found.');
		}
		else
		{
			if (!class_exists('fml_element_rule_type'))include_once($this->my->includeDir.'ruletype.php');
			include_once($filePath);
		}
		return true;
	}
}
?>