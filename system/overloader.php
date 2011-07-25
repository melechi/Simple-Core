<?php
/*
 * Simple Core 2.2.0
 * Copyright(c) 2004-2010, Simple Site Solutions
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Core overloader class.
 * 
 * This is one of the most important classes in the Framework. It's job is to
 * bridge the gap between all classes in the system. Taking full advantage of
 * PHP5's new object model, it acts as a global overloader - overloading the
 * entire framework to give seemless access to everything in the framework from
 * anywhere in the framework.
 *
 * @abstract
 * @author Timothy Chandler
 * @version 1.0
 * @copyright Simple Site Solutions 2010
 */
abstract class overloader
{
	public $branchFolder			='branch';
	public $xIncludeFolder			='includes';
	private $branchContainer		=array();
	/**
	 * @var core_my - Meta data about this object.
	 */
	public $my						=null;
	/**
	 * @var array - Restricted variables. DO NOT use these as class level variables.
	 */
	public $reservedVars			=array('parent','my','component','application','config','global');
	/**
	 * @var array - a node of the URI - ie. /foo/bar/=(node[0]=foo,node[1]=bar).
	 */
	private $node					=array();
	/**
	 * @var int - The total number of nodes in the URI.
	 */
	private $numNodes				=0;
	/**
	 * @var int - A container for nodes - internal use only.
	 */
	private $checkedNodes			=array();
	/**
	 * @var mixed - The first node in the URI.
	 */
	private $firstNode				=0;
	/**
	 * @var mixed - The last node in the URI.
	 */
	private $lastNode				=0;
	/**
	 * @var overloader - This references the parent object to this one.
	 */
	public $parent					=null;
	/**
	 * @var core_logger - This logger is available in every class.
	 */
	public $logger					=null;
	
	
	/**
	 * Constructor. This base constructor MUST be run by all extending
	 * classes for them to operate correctly.
	 * 
	 * @param overloader $parent - Every instance of overloader has a parent.
	 * It must be declared upon construction.
	 * @return void
	 * @access public
	 */
	public function __construct($parent=null)
	{
		if (is_null($parent))
		{
			$this->exception('Construction of class "'.get_class($this).'" failed because parent mapping was configured incorrectly.');
		}
		else
		{
			global ${CORE};
			$this->bindParent($parent);
			$this->my				=new stdClass;
			$this->my->name			=substr(strrchr(get_class($this),'_'),1);
			$this->my->dir			='';
			if (method_exists($this,'setMyDir'))$this->setMyDir();
			$this->my->branchDir	=realpath($this->my->dir.$this->branchFolder)._;
			$this->my->includeDir	=realpath($this->my->dir.$this->xIncludeFolder)._;
			//Set the node properties.
			$this->node				=&${CORE}->nodes;
			if (!isset($this->node[0]))$this->node[0]='';
			$this->numNodes			=count($this->node);
			$this->lastNode			=end($this->node);
			$this->firstNode		=reset($this->node);
			
			//Initialize logging.
			if ((bool)(int)${CORE}->config->logging['enabled'])
			{
				//Logger for applications.
				if ($this instanceof application)
				{
					if ((bool)${CORE}->config->logging->applications)
					{
						$this->logger=new core_logger('application_'.$this->my->name);
					}
					else
					{
						$this->logger=new core_logger_dummy;
					}
				}
				//Logger for components.
				elseif ($this instanceof component)
				{
					if ((bool)(int)${CORE}->config->logging->components)
					{
						$this->logger=new core_logger('component_'.$this->my->name);
					}
					else
					{
						$this->logger=new core_logger_dummy;
					}
				}
				//Logger for events.
				elseif ($this instanceof event)
				{
				if ((bool)(int)${CORE}->config->logging->components)
					{
						$this->logger=new core_logger('event_'.$this->my->name);
					}
					else
					{
						$this->logger=new core_logger_dummy;
					}
				}
				//Logger for pages.
				elseif ($this instanceof page)
				{
				if ((bool)(int)${CORE}->config->logging->components)
					{
						$this->logger=new core_logger('page_'.$this->my->name);
					}
					else
					{
						$this->logger=new core_logger_dummy;
					}
				}
				//Logger for branches.
				elseif ($this instanceof branch)
				{
					$parent=$this->parent;
					while (
					!(
						$parent instanceof application
						|| $parent instanceof component
						|| $parent instanceof core)
					)
					{
						$parent=$parent->parent;
					}
					if ($parent instanceof application)
					{
						$this->logger=new core_logger('application_branch_'.$this->my->name);
					}
					elseif ($parent instanceof component)
					{
						$this->logger=new core_logger('component_branch_'.$this->my->name);
					}
				}
				//Generic logger for anything not covered by the above.
				else
				{
					$this->logger=new core_logger('general');
				}
			}
			else
			{
				$this->logger=new core_logger_dummy;
			}
		}
		return true;
	}
	
	/**
	 * Parent binder.
	 * 
	 * This method allows you to bind a new parent to the object.
	 * 
	 * @param overloader $parent
	 * @return mixed - false for a failed binding or the newly
	 * bound parent object if successful.
	 * @access protected
	 */
	protected function bindParent($parent=null)
	{
		$return=false;
		if (is_object($parent))
		{
			$this->parent=$parent;
			$return=$parent;
		}
		return $return;
	}
	
	/**
	 * Throws an exception which is caught by the core exception handler
	 * and displayed neatly with debugging output.
	 * 
	 * @param string $exceptionMessage
	 * @param mixed $exceptionCode
	 * @return void - Does not return;
	 * @access public
	 */
	public function exception($exceptionMessage=null,$exceptionCode=null)
	{
		if ($this->debug)
		{
			new core_exception($this,$exceptionMessage);
		}
		exit();
	}
	
	/**
	 * This method cleverly constructs URLS for the framework.
	 * 
	 * @todo This still breaks in some edge-cases... Investigate further.
	 * @param string $URL
	 * @param string $protocol
	 * @access public
	 * @return string - The generated URL.
	 */
	public function makeURL($URL='',$protocol='http')
	{
		global ${CORE};
		if (is_array($URL))
		{
			$URL='/'.implode('/',$URL).'/';
		}
		$URL=trim($URL,'/');
//		if (empty($protocol))$protocol='http';
//		if (!empty($protocol))$protocol.='://';
		if ($this instanceof application && $this->isBound)
		{
			$basePath=$this->binding['rootURL']['root'];
		}
		else
		{
			$basePath=${CORE}->basePath;
		}
		if (empty($this->node[0]))
		{
			if (!strstr($URL,'?') && (!empty($URL)))$URL.='/';
			if (strstr($URL,'?'))
			{
				list($prefix,$suffix)=explode('?',$URL,2);
				$URL=$basePath.trim($prefix,'/');
				$URL.=(empty($prefix)?'?':'/?').$suffix;
			}
			else
			{
				$URL=$basePath.(implode('/',$this->node())).$URL;
//				var_dump($this->node());
//				var_dump($URL);
			}
		}
		else
		{
			if (empty($URL))
			{
				$URL=$basePath.'/';
			}
			elseif (strstr($URL,'?'))
			{
				list($prefix,$suffix)=explode('?',$URL,2);
				$URL=$basePath.'/'.trim($prefix,'/');
				$URL.=(empty($prefix)?'?':'/?').$suffix;
			}
			else
			{
				$URL=$basePath.'/'.$URL.'/';
			}
		}
		if (strstr($URL,'//'))$URL=str_replace('//','/',$URL);
//		return '/'.$URL;
		return $protocol.'://'.$URL;
	}
	
	/**
	 * Determines weather or not a variable is reserved by this
	 * overloader class.
	 * 
	 * @param string $theVar
	 * @access protected
	 */
	protected function isReservedVar($theVar=null)
	{
		return in_array($theVar,$this->reservedVars);
	}
	
	/**
	 * Method connector.
	 * 
	 * @param string $theMethod
	 * @param mixed $theArgs
	 * @access public
	 * @return mixed
	 */
	
	public function __call($theMethod,$theArgs)
	{
		global ${CORE};
		$return=false;
		if (method_exists(${CORE},$theMethod))
		{
			$return=call_user_func_array(array(${CORE},$theMethod),$theArgs);
		}
		else
		{
			$this->exception('Overloader __call() exception! Method "'.$theMethod.'" was not found.');
		}
		return $return;
	}
	
	/**
	 * Variable getting connector.
	 * 
	 * @param string $theVar
	 * @access public
	 * @return mixed
	 */
	
	public function __get($theVar)
	{
		global ${CORE};
		$return=null;
		if ($this->branchContainer_exists($theVar))
		{
			$return=$this->branchContainer_initiate($theVar);
		}
		elseif (isset(${CORE}->{$theVar}))
		{
			$return=&${CORE}->{$theVar};
		}
		return $return;
	}
	
	/**
	 * Returns a URI node by its index.
	 * 
	 * @param int $node
	 * @return string - The node in string format.
	 * @access protected
	 */
	protected function &node($node=false)
	{
		$return=false;
		if ($node!==false)
		{
			if (is_numeric($node))
			{
				//if ($node<0)$node=(($this->numNodes-1)-$node-1);
				if ($node<0)
				{
					$node=abs($node)-1;
					end($this->node);
					while ($node)
					{
						if (!isset($this->node[$node]))break;
						prev($this->node);
						$node--;
					}
					$return=&current($this->node);
				}
				elseif (isset($this->node[$node]))
				{
					$return=&$this->node[$node];
				}
			}
			else
			{
				if (isset($this->checkedNodes[$node]))
				{
					$return=$this->checkedNodes[$node];
				}
				else
				{
					for ($i=0; $i<$this->numNodes; $i++)
					{
						if ($this->node[$i]==$node)
						{
							$return=&$this->node[$i];
							$this->checkedNodes[$this->node[$i]]=true;
							break;
						}
					}
				}
			}
		}
		else
		{
			$return=$this->node;
		}
		return $return;
	}
	
	/**
	 * Returns this first URI node.
	 * 
	 * @access protected
	 * @return string.
	 */
	protected function &firstNode()
	{
		return $this->firstNode;
	}
	
	/**
	 * Returns the last URI node.
	 * 
	 * @access protected
	 * @return string.
	 */
	protected function &lastNode()
	{
		return $this->lastNode;
	}
	
	/**
	 * Returns the total number of URI nodes.
	 * 
	 * @access protected
	 * @return int
	 */
	protected function &numNodes()
	{
		return $this->numNodes;
	}
	
	/**
	 * Tests to see if the node index is empty.
	 * 
	 * @access protected
	 * @return bool
	 */
	protected function emptyNode($node)
	{
		return empty($this->node[$node]);
	}
	
	/**
	 * Returns all nodes in their URI form as a whole string.
	 * 
	 * @access protected
	 * @return string
	 */
	protected function getURI()
	{
		return '/'.implode('/',$this->node).'/'.(!empty($_GET)?'?':'').http_build_query($this->global->get());
	}
	
	/**
	 * Tests if the URL is considered empty (meaning there are no nodes to process).
	 * 
	 * @access protected
	 * @return bool
	 */
	protected function emptyAddress()
	{
		$return=false;
		if (!$this->numNodes ||($this->numNodes==1 && empty($this->node[0])))
		{ 
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Tests if the URI is the home address.
	 * 
	 * @return bool;
	 */
	protected function homeAddress()
	{
		$return=false;
		if ($this->emptyAddress() || $this->node[0]=='home')
		{
			$return=true;
		}
		return $return;
	}
	
	/**
	 * Cleverly binds an application to a new address, reconstructing the
	 * nodes in the process.
	 * 
	 * @param string $address
	 * @return overloader
	 */
	protected function bindToAddress($address='')
	{
		if (is_array($address))
		{
			$this->node=$address;
		}
		else
		{
			$this->node=explode('/',$address);
		}
		if (!end($this->node))array_pop($this->node);
		if (!reset($this->node))array_shift($this->node);
		if (!isset($this->node[0]))$this->node[0]='';
		$this->numNodes=count($this->node);
		$this->lastNode=end($this->node);
		$this->firstNode=reset($this->node);
		return $this;
	}
	
	
	/**
	 * A thought-free object branching, virtual extention and dynamic loading method.
	 * 
	 * This method is magic. Give it the filename (which will also be part of the classname)
	 * and this method will load, construct, bind and extend the class. Ultimately, it allows
	 * the branched object to access the application it belongs to through the $this->parent
	 * attribute, instead of having to do a full reference all such as
	 * $this->application->myApplication->someOtherBranch->someMethod() (worst case scenario).
	 * 
	 * Additionally, this method takes a second optional parameter which allows you to define
	 * when the branching is to take place. Valid options are 'now' (default) and 'later'.
	 * If you define it as 'now', the branch will be loaded into memory immidiately.
	 * If you define it as 'later', the branch will be loaded only when it is FIRST used.
	 * 
	 * Note: Branches must live in the same directory as the root of the application. This is
	 * currently not configurable.
	 * 
	 * @param string $theExtention
	 * @param string $when
	 * @protected
	 * @access public
	 * @return bool
	 */
	 
	protected function branch($theBranch=null,$when='later')
	{
		if ($theBranch)
		{
			$theFile=$this->my->branchDir.$theBranch.'.php';
			if (!@is_file($theFile))
			{
				$this->exception('"'.get_class($this).'" tried to branch itself but the branch file "'.$theFile.'" was not found.');
			}
			elseif (!include_once($theFile))
			{
				$this->exception('"'.get_class($this).'" could not branch itself because the branch class "'.$theBranch.'" is not an instance of "branch".');
			}
			else
			{
				$branchName=$this->constructBranchName($theBranch);
				if ($when=='now')
				{
					if (!$this->{$theBranch}=new $branchName($this))
					{
						$this->exception('"'.get_class($this).'" could not branch itself because the branch file could not be initiated.');
					}
					elseif (!$this->{$theBranch} instanceof branch)
					{
						$this->exception('"'.get_class($this).'" could not branch itself because the branch class was not an instance of "branch".');
					}
					else
					{
						$this->{$theBranch}->bindToAddress($this->node());
//						$this->{$theBranch}->_construct($this->parent);
						if (method_exists($this->{$theBranch},'initiate'))$this->{$theBranch}->initiate();
					}
				}
				elseif ($when=='later')
				{
					$this->branchContainer_add($theBranch);
				}
				else
				{
					$this->exception('Unable to branch. $when parameter is invalid. Only "now" and "later" are valid branching options.');
				}
			}
		}
		return $this;
	}
	
	/**
	 * Adds a branch to be loaded at a later time.
	 * 
	 * @param string $theBranch
	 * @access protected
	 * @return bool
	 */
	protected function branchContainer_add($theBranch=null)
	{
		$return=false;
		if (empty($theBranch))
		{
			$this->exception('Unable to branch. Given branch name was empty.');
		}
		else
		{
			if ($this->branchContainer_exists($theBranch))
			{
				$this->exception('Unable to branch. Branch "'.$theBranch.'" already exists.');
			}
			else
			{
				$this->branchContainer[]=$theBranch;
				$return=true;
			}
		}
		return $return;
	}
	
	/**
	 * Checks if a branch exists.
	 * 
	 * @param string $theBranch
	 * @access private
	 * @return bool
	 */
	protected function branchContainer_exists($theBranch=null)
	{
		return (!empty($theBranch) && in_array($theBranch,$this->branchContainer))?true:false;
	}
	
	/**
	 * Initiates a sleeping branch.
	 * 
	 * @param string $theBranch
	 * @access protected
	 * @return bool
	 */
	protected function branchContainer_initiate($theBranch=null)
	{
		$return=false;
		if (empty($theBranch))
		{
			$this->exception('Unable to branch. Given branch name was empty.');
		}
		else
		{
			$branchName=$this->constructBranchName($theBranch);
			if (!$this->{$theBranch}=new $branchName($this))
			{
				$this->exception('"'.get_class($this).'" could not extend itself because the extention file could not be initiated.');
			}
			elseif (!$this->{$theBranch} instanceof branch)
			{
				$this->exception('"'.get_class($this).'" could not branch itself because the branch class "'.$theBranch.'" is not an instance of "branch".');
			}
			else
			{
				$this->{$theBranch}->bindToAddress($this->node());
//				$this->{$theBranch}->_construct($this->parent);
				if (method_exists($this->{$theBranch},'initiate'))$this->{$theBranch}->initiate();
				$return=$this->{$theBranch};
			}
		}
		return $return;
	}
	
	/**
	 * Branch name constructor.
	 * 
	 * This is used internally and should not need to be manually called.
	 * 
	 * @param string $newBranch Name of the new branch.
	 * @return string
	 * @access protected
	 */
	protected function constructBranchName($newBranch='')
	{
		$name=$this->my->name;
		$parent=$this->parent;
		while (!$parent instanceof core)
		{
			$name=$this->parent->my->name.'_'.$name;
			$parent=$parent->parent;
		}
		return $name.'_'.$newBranch;
	}
	
	/**
	 * X Include (Private include_once wrapper)
	 * 
	 * This method safely includes application/component specific
	 * classes. It does not construct the class as this is to be
	 * handled by whatever is using it.
	 * 
	 * NOTE: include_once is applied and there is no 'include always'
	 * alternative.
	 * 
	 * @param mixed $theClass An array of classes or a the string of a single class to be included.
	 * @access protected
	 * @return this
	 */
	protected function xInclude($theClass=null)
	{
		if (!is_string($theClass) && !is_array($theClass))
		{
			$this->exception(__FUNCTION__.' failed because $theClass was not an array or  string.');
		}
		else if (is_array($theClass))
		{
			for ($i=0,$j=count($theClass); $i<$j; $i++)
			{
				if (is_string($theClass[$i]))
				{
					$file=$this->my->dir.$this->xIncludeFolder._.$theClass[$i].'.php';
					if (is_file($file))
					{
						include_once($file);
					}
					else
					{
						$this->exception('Unable to include abstract class "'.$theClass[$i].'". File for class was not found ('.$file.').');
					}
				}
				else
				{
					$this->exception(__FUNCTION__.' failed because an invalid class name was given to be loaded.');
				}
			}
		}
		else
		{
			$file=$this->my->dir.$this->xIncludeFolder._.$theClass.'.php';
			if (is_file($file))
			{
				include_once($file);
			}
			else
			{
				$this->exception('Unable to include abstract class "'.$theClass.'". File for class was not found ('.$file.').');
			}
		}
		return $this;
	}
}
?>