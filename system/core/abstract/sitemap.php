<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Sitemap class for simple creation and management of pages.
 * 
 * This class assits in the simple management of pages. It handles
 * loading pages based on URI and manages templates appropriately.
 * 
 * 
 * @author Timothy Chandler
 * @version 2.0
 * @copyright Simple Site Solutions 17/11/2007
 */
class sitemap extends overloader
{
	public $parent=				false;
	private $outputComplete=	false;
	
	public $template=array();
	
	/**
	 * Constructor - Maps attributes.
	 * 
	 * @access public
	 * @return bool
	 */
	
	final public function __construct($parent)
	{
		parent::__construct($parent);
		$this->template=array
		(
			'path'		=>'templates'._,
			'compiled'	=>'_compiled',
			'cached'	=>'_cached',
			'config'	=>'_config',
			'head'		=>'head',
			'content'	=>'templates'._.'content'._,
			'shell'		=>'shell',
			'header'	=>'header',
			'body'		=>'body',
			'footer'	=>'footer',
			'object'	=>false,
			'function'	=>false
		);
		return true;
	}
	
	/**
	 * Merge Template Settings
	 * 
	 * Performs top level merging of template settings.
	 * 
	 * @access public
	 * @return array
	 */
	
	public function mergeTemplateSettings($theSettings=null)
	{
		$return=false;
		if (@is_array($theSettings))
		{
			foreach ($theSettings as $key=>&$val)
			{
				$this->template[$key]=$val;
			}
			$return=true;
		}
		return $return;
	}
	
	/** 
	 * Page registering method.
	 * 
	 * This method is used extencively to create a registry of pages and their mapped urls.
	 * This method is extremely flexible, allowing to splice the templates requred for
	 * a particular registered page.
	 * 
	 * @access public
	 * @return bool
	 */
	
	public function page()
	{
		$return=true;
		if (!$this->outputComplete)
		{
			$args=func_get_args();
			$numArgs=func_num_args();
			$templateOverride=false;
			$containsConditional=false;
			$beforeDisplay=false;
			//Check if there are enough nodes to match this request.
			if ($numArgs<$this->numNodes())
			{
				if (!is_array(end($args)))
				{
					if (isset($args[($numArgs-2)]))
					{
						if ($args[($numArgs-2)]!='?')$return=false;
					}
					else
					{
						$return=false;
					}
				}
				elseif (end($args)!='?')
				{//die('999');
					$return=false;//die('111');
				}
			}
			if ($return)
			{
				//Loop through and validate the page path, grabbing the end array if there is one.
				for ($i=0; $i<$numArgs; $i++)
				{
					if (is_string($args[$i]))
					{
						//$thisNode=(isset($this->node[$i])?$this->node[$i]:'');
						$thisNode=$this->node($i)?$this->node($i):'';
						if ($args[$i]!=$thisNode)
						{
							//At this point, the page path may be invalid. We need to make sure it doesn't contain dynamic constructs.
							if (strstr($args[$i],'|'))
							{
								$fragments=explode('|',$args[$i]);
								$gotMatch=false;
								for ($j=0,$k=count($fragments); $j<$k; $j++)
								{
									if ($fragments[$j]==$thisNode)
									{
										$gotMatch=true;
										$containsConditional=true;
										break;								
									}
								}
								if (!$gotMatch)
								{
									$return=false;
									break;
								}
							}
							elseif ($args[$i]=='?')
							{
								$containsConditional=true;
								if (is_array(end($args)))
								{
									$end=end($args);
									$templateOverride=&$end;
									if (isset($end['function']))
									{
										$beforeDisplay=array('type'=>'function','call'=>&$end['function']);
									}
									elseif (isset($end['object']))
									{
										if (!isset($end['folder']))$end['folder']=false;
										$beforeDisplay=array('type'=>'object','call'=>&$end['object'],'folder'=>&$end['folder']);
									}
								}
								break;
							}
							else
							{
								$return=false;
								break;
							}
						}
					}
					elseif (is_array($args[$i]))
					{
						if ($this->node($i)===false)
						{
							if ($args[$i]===end($args))
							{
								$templateOverride=&$args[$i];
								if (isset($args[$i]['function']))
								{
									$beforeDisplay=array('type'=>'function','call'=>&$args[$i]['function']);
								}
								elseif (isset($args[$i]['object']))
								{
									if (!isset($args[$i]['folder']))$args[$i]['folder']=false;
									$beforeDisplay=array('type'=>'object','call'=>&$args[$i]['object'],'folder'=>&$args[$i]['folder']);
								}
							}
							else
							{
								$this->exception('Array found as argument when calling '.__METHOD__.'. Only the END argument can be an array.');
							}
						}
						else
						{
							$return=false;
							break;
						}
					}
					else
					{
						$return=false;
						break;
					}
				}
			}
			//At this stage, we know if the page path is valid or not.
			if ($return)
			{
				//Handle the templates.
				$templateVar=array();
				//$templateVar['content']	=(isset($templateOverride['content'])	?$templateOverride['content']	:false);
				$templateVar['content']		=(isset($templateOverride['content'])	?$templateOverride['content']	:$this->template['content']);
				$templateVar['path']		=(isset($templateOverride['path'])		?$templateOverride['path']		:$this->template['path']);
				$templateVar['compiled']	=(isset($templateOverride['compiled'])	?$templateOverride['compiled']	:$this->template['compiled']);
				$templateVar['cached']		=(isset($templateOverride['cached'])	?$templateOverride['cached']	:$this->template['cached']);
				$templateVar['config']		=(isset($templateOverride['config'])	?$templateOverride['config']	:$this->template['config']);
				$templateVar['shell']		=(isset($templateOverride['shell'])		?$templateOverride['shell']		:$this->template['shell']);
				$templateVar['head']		=(isset($templateOverride['head'])		?$templateOverride['head']		:$this->template['head']);
				$templateVar['header']		=(isset($templateOverride['header'])	?$templateOverride['header']	:$this->template['header']);
				$templateVar['body']		=(isset($templateOverride['body'])		?$templateOverride['body']		:$this->template['body']);
				$templateVar['footer']		=(isset($templateOverride['footer'])	?$templateOverride['footer']	:$this->template['footer']);
				
				
				//Create set template structure variables.
				$this->parent->setTemplateVar('HEAD',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['head']);
				$this->parent->setTemplateVar('HEADER',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['header']);
				$this->parent->setTemplateVar('BODY',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['body']);
				$this->parent->setTemplateVar('FOOTER',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['footer']);
				if ($containsConditional)
				{
					/* The rules to determin the path and template are as follows:
					 * 
					 * Iterate over the args, breaking on any arrays.
					 * If the arg contains a pipe conditional:
					 *	* If it is the last arg:
					 *		* Iterate over the possible matches, searching in a
					 *		  file next iteration pattern.
					 *		  If a possible match is blank, it is skipped.
					 *		  If a match is found, break.
					 *	* Else
					 * 		* Iterate over the possible matches, searching in a
					 * 		  folder, next iteration pattern.
					 *		  If a possible match is blank, it is skipped.
					 *		  If a match is found, break.
					 * 
					 * If the arg contains a random conditional:
					 *	* This is considered the end node for the path. Anything
					 *	  following this node is truncated.
					 *	  This allows for an unlimited number of dynamic nodes
					 *	  following the previous valid node.
					 *	  The previous node must be valid or it will fail.
					 *    NOTE: There can be an array with parameters acting as
					 *    config as the last arg, so we need to be able to deal
					 *    with this situation as well.
					 */
					if ($templateVar['content'])
					{
						$path=realpath($this->parent->my->dir)._.$templateVar['content'];
					}
					else
					{
						$path=realpath($this->parent->my->dir.$templateVar['path'])._.'content';
					}
					for ($i=0; $i<$numArgs; $i++)
					{
						if (is_array($args[$i]))break;
						if (strstr($args[$i],'|'))
						{//print hit10;var_dump($templateOverride);
							$fragments=explode('|',$args[$i]);
							for ($j=0,$k=count($fragments); $j<$k; $j++)
							{//print hit11;
								if (empty($fragments[$j]))continue;
								//TODO: Look into this further - looks very buggy.
								$lastArg=($templateOverride)?($numArgs-1):$numArgs;
//								$lastArg=($templateOverride)?($numArgs-2):$numArgs;
//								print '<br />'.$numArgs.':'.$lastArg.':'.($i+1).'<br />';
//								print_r($args[($numArgs-1)]);
								//Is the last arg.
								if (($i+1)==$lastArg || $args[$lastArg]=='?')
								{
									//Only search for a file.
//									var_dump($path._.$fragments[$j]);exit();
									if (is_file($path._.$fragments[$j].'.tpl'))
									{
//										$path.=_.$fragments[$j].'.tpl';
										$path.=_.$fragments[$j];
										break 2;
									}
								}
								//Is not the last arg.
								else
								{//print hit12;
									//Only search for a dir.
									if (is_dir($path._.$fragments[$j]))
									{
										$path.=_.$fragments[$j];
										break;
									}
								}
							}
						}
						elseif ($args[$i]=='?')
						{
							$lastArg=($templateOverride)?($numArgs-2):($numArgs-1);
							if (is_file($path.$args[$lastArg].'.tpl'))
							{
								$path.=_.$args[$lastArg];
								break;
							}
							//Is the last arg.
							if (($i+1)==$lastArg || $beforeDisplay!==false)
							{
								break;
							}
							//Is not last arg.
							else
							{
								$this->exception('The ? conditional can only be used as the last parameter when using '.__METHOD__.'().');
							}
						}
						else
						{
							$path.=_.$args[$i];
						}
					}
					$this->parent->setTemplateVar('CONTENT',$path);
				}
				else
				{
					$this->parent->setTemplateVar('CONTENT',realpath($this->parent->my->dir.$templateVar['content'])._.implode(_,$this->node()));
					//print $this->parent->my->dir.$templateVar['content'];
				}
				if ($this->parent->useSmarty)
				{
					$this->parent->smarty->template_dir=realpath($this->parent->my->dir.$templateVar['path']);
					$this->parent->smarty->compile_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['compiled']);
					$this->parent->smarty->cache_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['cached']);
					$this->parent->smarty->config_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['config']);
				}
				if ($beforeDisplay)
				{
					switch ($beforeDisplay['type'])
					{
						case 'function':
						{
							if (method_exists($this->parent,$beforeDisplay['call']))
							{
								$this->parent->{$beforeDisplay['call']}();
							}
							else
							{
								$this->exception('Unable to execute "before display" function. The function has not been defined!');
							}
							break;
						}
						case 'object':
						{
							$dir=$this->parent->my->dir.'pages'._;
							if (isset($templateVar['folder']))
							{
								$dir.=$templateVar['folder']._;
							}
							$file=$dir.(string)$beforeDisplay['folder']._.$beforeDisplay['call'].'.php';
							//Check if the file exists.
							if (is_file($file))
							{
								//Load the file into memory.
								include_once($file);
								$className=$this->parent->my->name.'_page_'.$beforeDisplay['call'];
								//Check that the file contains the class we expect according to naming convention.
								if (class_exists($className))
								{
									//Before we create the object, lets make sure it has been extended from the page abstract class.
									if (is_subclass_of($className,'page'))
									{
										//$this->config->path->publicapplications.$this->my->name
//										$address=$args;
//										if (is_array(end($address)))array_pop($address);
//										if (strstr($args[$i],'|'))
//										{
//											
//										}
//										elseif ($args[$i]=='?')
										//Construct the page handler.
										$this->page=new $className
										(
											$this->parent,
											$this->node(),
											$dir,
											$this->parent->my->dir.$templateVar['path'],
											$this->parent->my->dir.$templateVar['content']
										);
										//Initiate the page handler.
										$this->page->initiate();
									}
									else
									{
										$this->exception('Attemp to load page handler class failed. Pages must be extended'
														.' from the base "page" class.');
									}
								}
								else
								{
									$this->exception('Attempt to load page handler class failed. Simple Core requires the a'
													.' class with the name "'.$className.'" to be defined at'
													.' "'.$this->parent->my->dir._.'pages'._.'" within a file named'
													.' "'.$beforeDisplay['call'].'.php".');
								}
							}
							else
							{
								$this->exception('Attempt to load page handler class failed. '.$file.' doesn\'t exist.');
							}
						}
					}
				}
				//Create a set of useful template variables.
				$this->createUsefulTemplateVariables($templateVar['path']);
				print $this->parseTemplate(realpath($this->parent->my->dir.$templateVar['path'].$templateVar['shell'].'.tpl'));
				$this->outputComplete=true;
				$return=true;
			}
		}
		return $return;
	}
	
	/**
	 * Forces given content location to be used as the content output.
	 * 
	 * @param string $thePage
	 * @param array $templateOverride
	 * @return bool
	 */
	
	public function forcePage($thePage=null,$templateOverride=array())
	{
		$return=false;
		if (!$this->outputComplete)
		{
			if (empty($thePage) && (!isset($templateOverride['noException'])))
			{
				$this->exception('First parameter was empty.');
			}
			else
			{
				$templateVar=array();
				//$templateVar['content']	=(isset($templateOverride['content'])	?$templateOverride['content']	:false);
				$templateVar['content']		=(isset($templateOverride['content'])	?$templateOverride['content']	:$this->template['content']);
				$templateVar['path']		=(isset($templateOverride['path'])		?$templateOverride['path']		:$this->template['path']);
				$templateVar['compiled']	=(isset($templateOverride['compiled'])	?$templateOverride['compiled']	:$this->template['compiled']);
				$templateVar['cached']		=(isset($templateOverride['cached'])	?$templateOverride['cached']	:$this->template['cached']);
				$templateVar['config']		=(isset($templateOverride['config'])	?$templateOverride['config']	:$this->template['config']);
				$templateVar['shell']		=(isset($templateOverride['shell'])		?$templateOverride['shell']		:$this->template['shell']);
				$templateVar['head']		=(isset($templateOverride['head'])		?$templateOverride['head']		:$this->template['head']);
				$templateVar['header']		=(isset($templateOverride['header'])	?$templateOverride['header']	:$this->template['header']);
				$templateVar['body']		=(isset($templateOverride['body'])		?$templateOverride['body']		:$this->template['body']);
				$templateVar['footer']		=(isset($templateOverride['footer'])	?$templateOverride['footer']	:$this->template['footer']);
				
				if (strstr($thePage,'.tpl'))$thePage=str_replace('.tpl','',$thePage);
				if (!is_file($this->parent->my->dir.$templateVar['content']._.$thePage.'.tpl'))
				{
					if (!isset($templateOverride['noException']))
					{
						$this->exception('Attempt to forcePage failed because the template' 
										.' "'.$this->parent->my->dir.$templateVar['content']._.$thePage.'.tpl" could not be found.');
					}
				}
				else
				{
					//Create a set of useful template variables.
					$this->createUsefulTemplateVariables($templateVar['path']);
					
					//Create set template structure variables.
					$this->parent->setTemplateVar('HEAD',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['head']);
					$this->parent->setTemplateVar('HEADER',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['header']);
					$this->parent->setTemplateVar('BODY',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['body']);
					$this->parent->setTemplateVar('FOOTER',		realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['footer']);
					$this->parent->setTemplateVar('CONTENT',	realpath($this->parent->my->dir.$templateVar['content'])._.$thePage);
					if ($this->parent->useSmarty)
					{
						$this->parent->smarty->template_dir=realpath($this->parent->my->dir.$templateVar['path']);
						$this->parent->smarty->compile_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['compiled']);
						$this->parent->smarty->cache_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['cached']);
						$this->parent->smarty->config_dir=realpath($this->parent->my->dir.$templateVar['path'].$templateVar['config']);
					}
					print $this->parseTemplate(realpath($this->parent->my->dir.$templateVar['path'])._.$templateVar['shell'].'.tpl');
					$this->outputComplete=true;
					$return=true;
				}
			}
		}
		return $return;
	}
	
	/**
	 * Returns true if the sitemap has outputted page content. False otherwise.
	 * 
	 * @access public
	 * @return void
	 */
	public function outputComplete()
	{
		return $this->outputComplete;
	}
	
	/**
	 * A wrapper method for capturing 404 errors.
	 * 
	 * Use this method to capture and assess what is returned from multiple
	 * calls of {@link page()}.
	 * 
	 * This method returns true if a page is found and false if all calls to {@link page()}
	 * return false.
	 * 
	 * @access public
	 * @return bool
	 */
	
	public function check404()
	{
		$return=false;
		$args=@func_get_args();
		$numArgs=count($args);
		for ($i=0;$i<$numArgs; $i++)
		{
			if ($args[$i]===true)
			{
				$return=true;
				break;
			}
		}
		return $return;
	}
	
	public function parseTemplate($theTemplate=null)
	{
		$return=false;
		if (empty($theTemplate))
		{
			$this->exception('Unable to parse Template. No template path was given.');
		}
		elseif (!is_file($theTemplate))
		{
			$this->exception('Unable to parse Template. Template "'.$theTemplate.'" does not exist.');
		}
		else
		{
			if ($this->parent->useBreadcrumbs)
			{
				$this->parent->setTemplateVar('BREADCRUMBS',$this->parent->breadcrumbs);
			}
			if ($this->parent->useSmarty)
			{
				$return=$this->parent->smarty->fetch($theTemplate);
			}
			else
			{
				$return=file_get_contents($theTemplate);
				$return=$this->component->template->parseFunctions($return);
				$return=$this->component->template->parseTemplateTags($return);
				$return=$this->component->template->parseVariables($return);
				$return=$this->component->template->parseLoops($return);
//				$return=$this->component->template->parseURLs($return);
				$return=$this->component->template->parseURLs($return,$this->parent);
				$return=$this->component->template->parseResourceTags($return);
				$return=$this->component->template->parseResouceDump($return);
			}
		}
		return $return;
	}
	
	public function createUsefulTemplateVariables($path)
	{
		$this->parent->setTemplateVar('COREVERSION',	$this->version);
		$this->parent->setTemplateVar('TEMPLATEDIR',	realpath($this->parent->my->dir.$path));
		$this->parent->setTemplateVar('COREROOT',		$this->config->path->publicroot);
		$this->parent->setTemplateVar('APPSROOT',		$this->config->path->publicroot.'applications/');
		$this->parent->setTemplateVar('PATH',			$this->config->path->toArray());
		$this->parent->setTemplateVar('PUBLICROOT',		$this->config->path->publicroot.'applications/'.$this->parent->my->name.'/');
		$this->parent->setTemplateVar('NODE',			$this->node());
		//Make sure everything is okay to use the feedback handler.
		if (isset($this->config->component->session->active)
		&& (bool)(int)$this->config->component->session->active
		&& $this->component->session->sessionStarted()
		&& isset($this->config->component->database->connection))
		{
			$this->parent->setTemplateVar
			(
				'FEEDBACK',
				array
				(
					'messages'	=>$this->component->feedback->isMessage()?$this->component->feedback->getMessages():array(),
					'errors'	=>$this->component->feedback->isError()?$this->component->feedback->getErrors():array()
				)
			);
		}
		//Deal with HTTPS related stuff.
		if (!empty($_SERVER['HTTPS']))
		{
			$this->parent->setTemplateVar('SECUREMODE',true);
		}
		else
		{
			$this->parent->setTemplateVar('SECUREMODE',false);
		}
		$this->parent->setTemplateVar('HTTPSROOT','https://'.$_SERVER['HTTP_HOST'].$this->config->path->publicroot.'applications/'.$this->parent->my->name.'/');
		$this->parent->setTemplateVar('THIS_HTTPS_ADDRESS',$this->makeURL(implode('/',$this->node()).'/','https'));
		return true;
	}
}
?>