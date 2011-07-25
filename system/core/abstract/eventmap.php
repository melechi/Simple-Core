<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Eventmap class for simple creation and management of application events.
 * 
 * This class assits in the simple management of events. It handles
 * executing events based on URI.
 * 
 * The goal of this class is to define a set of solid conventions for simple core
 * application developers to follow, while at the same time making the development
 * process as simple and streemlined as possible.
 * 
 * @author Timothy Chandler
 * @version 2.0
 * @copyright Simple Site Solutions 21/11/2007
 */
class eventmap extends overloader
{
	public $module=false;
	public $completeSuffix='complete/';
	public $moduleName=false;
	
	/** 
	 * Event managing method.
	 * 
	 * This method is used extencively to create a registry of events and their mapped urls.
	 * This method is extremely flexible, allowing mapping to local application functions
	 * or to an event object.
	 * 
	 * @access public
	 * @return bool
	 */
	
	public function event()
	{
		$return=true;
		$args=func_get_args();
		$numArgs=func_num_args();
		$containsConditional=false;
		//A valid event always contains at least 2 arguments.
		if ($numArgs>1)
		{
			$endArg=end($args);
			//Loop through and validate the event path.
			for ($i=0; $i<($numArgs-1); $i++)
			{
				if (is_string($args[$i]))
				{
					//$thisNode=(isset($this->node[$i])?$this->node[$i]:'');
					$thisNode=$this->node($i)?$this->node($i):'';
					if ($args[$i]!=$thisNode)
					{//print_r($args);var_dump($thisNode);var_dump($i);
						//At this point, the event path may be invalid. We need to make sure it doesn't contain dynamic constructs.
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
					$this->exception('Array found as argument when calling '.__METHOD__.'. Only the END argument can be an array.');
				}
			}
			//If the path is valid, then we need to validate and handle the last argument.
			if ($return)
			{
				//If it is a string, then we use a application-local method to handle the event.
				if (is_string($endArg))
				{
					if (method_exists($this->parent,$endArg))
					{
						$this->parent->{$endArg}();
					}
					else
					{
						$this->exception('Unknown event handler "'.$endArg.'".');
					}
				}
				//If it is an array, then we need to determin what to do based on parameters.
				elseif (is_array($endArg))
				{
					//Handle as an object request.
					if (isset($endArg['object']))
					{
						$dir=$this->parent->my->dir.'events'._;
						if (isset($endArg['folder']))
						{
							$dir.=$endArg['folder']._;
						}
						$file=$dir.$endArg['object'].'.php';
						//Check if the file containing the event's handler class exists.
						if (is_file($file))
						{
							//Load the file into memory.
							include_once($file);
							//Check if the class now exists in memory according to convention.
							$className=$this->parent->my->name.'_event_'.$endArg['object'];
							if (class_exists($className))
							{
								//Before we create the object, lets make sure it has been extended from the event abstract class.
								if (is_subclass_of($className,'event'))
								{
									//Construct the class.
									$this->event=new $className($this->parent,$this->node(),$dir);
//									$this->event->my->name='event_'.$this->event->my->name;
//									$this->event->my->dir=$dir;
//									$this->event->my->branchDir=$this->event->my->dir.$this->parent->branchFolder._;
//									$this->event->my->includeDir=$this->event->my->dir.$this->parent->xIncludeFolder._;
//									//Perform address binding
//									$this->event->bindToAddress();
									//Finally, determin how the object should be initiated.
									if (isset($endArg['args']))
									{
										if (is_array($endArg['args']))
										{
											if (method_exists($this->event,'beforeEvent'))call_user_func_array(array($this->event,'beforeEvent'),$endArg['args']);
											$return=call_user_func_array(array($this->event,'initiate'),$endArg['args']);
											if (method_exists($this->event,'afterEvent'))call_user_func_array(array($this->event,'afterEvent'),$endArg['args']);
										}
										else
										{
											if (method_exists($this->event,'beforeEvent'))$this->event->beforeEvent($endArg['args']);
											$this->event->initiate($endArg['args']);
											if (method_exists($this->event,'afterEvent'))$this->event->afterEvent($endArg['args']);
										}
									}
									else
									{
										if (method_exists($this->event,'beforeEvent'))$this->event->beforeEvent();
										$return=$this->event->initiate();
										if (method_exists($this->event,'afterEvent'))$this->event->afterEvent();
									}
								}
								else
								{
									$this->exception('Attemp to load event handler class failed. Events must be extended'
													.' from the base "event" class.');
								}
							}
							//Error if it doesn't.
							else
							{
								$this->exception('Attempt to load event handler class failed. Simple Core requires the a'
												.' class with the name "'.$className.'" to be defined at'
												.' "'.$this->parent->my->dir._.'events'._.'" within a file named'
												.' "'.$endArg['object'].'.php".');
							}
						}
						//Error if it doesn't.
						else
						{
							$this->exception('Attempt to load event handler class failed. '.$file.' doesn\'t exist.');
						}
					}
					//Handle as a function request.
					elseif (isset($endArg['function']))
					{
						//Check if the method exists.
						if (method_exists($this->parent,$endArg['function']))
						{
							//If the args parameter is set, then handle calling the function a little differently.
							if (isset($endArg['args']))
							{
								//if the args parameter is an array, then use the call_user_func_array function to call the function.
								if (is_array($endArg['args']))
								{
									$return=call_user_func_array(array($this->parent,$endArg['function']),$endArg['args']);
								}
								//Else call the method normally.
								else
								{
									$return=$this->parent->{$endArg['function']}($endArg['args']);
								}
							}
							//Else call the method normally.
							else
							{
								$return=$this->parent->{$endArg['function']}();
							}
						}
						//Error
						else
						{
							$this->exception('Unknown event handler "'.$endArg['function'].'".');
						}
					}
					//Error
					else
					{
						$this->exception('Invalid event configuration. An array was given as the last parameter of an event'
										.' but did not contain an "object" or "function" parameter.');
					}
				}
			}
		}
		return $return;
	}
	
	//TODO: fix
	private function doComplete($completeAddress=null,$useSuffix=true)
	{
		if ($completeAddress)
		{
			if ($useSuffix)
			{
				@header('location: '.$this->myURI.$completeAddress.$this->completeSuffix);
			}
			else
			{
				@header('location: '.$this->myURI.$completeAddress);
			}
		}
		else
		{
			@header('location: '.$this->completeSuffix);
		}
		//TODO: fix this so that no output gets sent during the shutdown process.
		exit();
		#$this->shutdown();
	}
	
	public function forceObjectEvent($theEvent=null,$args=false,$folder=false)
	{
		$return=false;
		if (empty($theEvent))
		{
			$this->exception('First parameter was empty.');
		}
		else
		{
			$dir=$this->parent->my->dir.'events'._;
			if (isset($folder))
			{
				$dir.=$folder._;
			}
			$file=$dir.$theEvent.'.php';
			//Check if the file containing the event's handler class exists.
			if (is_file($file))
			{
				//Load the file into memory.
				include_once($file);
				//Check if the class now exists in memory according to convention.
				$className=$this->parent->my->name.'_event_'.$theEvent;
				if (class_exists($className))
				{
					//Before we create the object, lets make sure it has been extended from the event abstract class.
					if (is_subclass_of($className,'event'))
					{
						//Construct the class.
						$this->event=new $className($this->parent,false);
						//Perform address binding
						$this->event->bindToAddress($this->node());
						//Finally, determin how the object should be initiated.
						if (isset($args))
						{
							if (is_array($args))
							{
								if (method_exists($this->event,'beforeEvent'))call_user_func_array(array($this->event,'beforeEvent'),$args);
								$return=call_user_func_array(array($this->event,'initiate'),$args);
								if (method_exists($this->event,'afterEvent'))call_user_func_array(array($this->event,'afterEvent'),$args);
							}
							else
							{
								if (method_exists($this->event,'beforeEvent'))$this->event->beforeEvent($args);
								$this->event->initiate($args);
								if (method_exists($this->event,'afterEvent'))$this->event->afterEvent($args);
							}
						}
						else
						{
							if (method_exists($this->event,'beforeEvent'))$this->event->beforeEvent();
							$return=$this->event->initiate();
							if (method_exists($this->event,'afterEvent'))$this->event->afterEvent();
						}
					}
					else
					{
						$this->exception('Attemp to load event handler class failed. Events must be extended'
										.' from the base "event" class.');
					}
				}
				//Error if it doesn't.
				else
				{
					$this->exception('Attempt to load event handler class failed. Simple Core requires the a'
									.' class with the name "'.$className.'" to be defined at'
									.' "'.$this->parent->my->dir._.'events'._.'" within a file named'
									.' "'.$endArg['object'].'.php".');
				}
			}
			//Error if it doesn't.
			else
			{
				$this->exception('Attempt to load event handler class failed. '.$file.' doesn\'t exist.');
			}
		}
		return $return;
	}
}
?>