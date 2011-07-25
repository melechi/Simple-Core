<?php
/*
 * Simple __core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3__core.com/SCPL
 */
/**
 * The __core object container and autoloader class.
 * 
 * This magical class assists the __core overloader by
 * helping to connect the overloading request to the
 * appropriate container, autoloading the class if
 * required and returning it.
 *
 * @final
 * @author Timothy Chandler
 * @version 1.0
 * @copyright Simple Site Solutions 06/12/2007
 */
final class objectContainer
{
	private $__core=false;
	private $type=false;
	
	public function __construct(core $__core,$type=null)
	{
		$return=false;
		if (!is_null($type))
		{
			$this->__core=$__core;
			$this->type=$type;
			$return=true;
		}
		return $return;
	}
	
	public function __call($theFunction,$arguments)
	{
		//print '__call() is requesting "'.$theFunction.'".';
		$this->__core->exception('Function  "'.$theFunction.'" is not a valid __core function.','__core Object Handler');
	}
	
	public function __get($theVar)
	{
		$return=false;
		switch($this->type)
		{
			case 'component':
			{
				if (is_dir($this->__core->config->path->components.$theVar._)
				&& (is_file($this->__core->config->path->components.$theVar._.$theVar.'.php')))
				{
					include_once($this->__core->config->path->components.$theVar._.$theVar.'.php');
					$className='component_'.$theVar;
					if (!class_exists($className))
					{
						$this->__core->exception('Unable to initiate component. Conventional class name "'.$className.'" was not found.');
					}
					else
					{
						$return=$this->{$theVar}=new $className($this->__core); 
					}
				}
				else
				{
					$this->__core->exception('Unable to find component "'.$theVar.'".','core Object Handler');	
				}
				break;
			}
			case 'application':
			{
				//TODO: This does not work in PHP version 5.3.0 >
				if (is_dir($this->__core->config->path->applications.$theVar._)
				&& (is_file($this->__core->config->path->applications.$theVar._.$theVar.'.php')))
				{
					include_once($this->__core->config->path->applications.$theVar._.$theVar.'.php');
					$className='application_'.$theVar;
//					var_dump($className);
					if (class_exists($className))
					{
						$return=$this->{$theVar}=new $className($this->__core);
					}
					else 
					{
						$this->__core->exception('Application "'.$theVar.'" has an invalid class name. Expecting "'.$className.'".','core Object Handler');
					}
				}
				else
				{
//					var_dump(is_link($this->__core->config->path->applications.$theVar._));
//					var_dump($this->__core->config->path->applications.$theVar._.$theVar.'.php');
//					var_dump(is_file($this->__core->config->path->applications.$theVar._.$theVar.'.php'));
					$this->__core->exception('Unable to find application "'.$theVar.'".','core Object Handler');	
				}
				break;
			}
			default:
			{
				$this->__core->exception('"'.$this->type.'" is an unknown object type!','core Object Handler');
			}
		}
		return $return;
	}
}
?>