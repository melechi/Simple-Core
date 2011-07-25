<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class fml_element extends overloader
{
	abstract function parents();
	abstract function attributes();
	abstract function template();
	
	public $instance=false;
	public $scope=false;
	
	public $rawElement=false;
	public $rawAttributes=array();
	public $children=array();
	public $templateTokens=array();
	
	public $validParents=array(false);
	public $validAttributes=array
	(
		array('attribute'=>'id','default'=>null,'required'=>false,'dataType'=>FML_DATATYPE_ID),
		array('attribute'=>'scope','default'=>false,'required'=>false,'dataType'=>FML_DATATYPE_ID)
	);
	
	public function __construct($parent,$instance,$rawElement)
	{
		parent::__construct($parent);
		$this->instance=$instance;
		$this->rawElement=$rawElement;
		$this->my->dir=$this->instance->my->includeDir;
		$this->my->includeDir=$this->my->dir.$this->xIncludeFolder._;
		//Extract element attributes.
		$this->scope=$this;
		$this->extractAttributes();
		return true;
	}
	
	public function init()
	{
		if (method_exists($this,'initiate'))$this->initiate();
		$this->parents();
		$this->attributes();
		//Parse the element.
		$this->parse($this->rawElement);
		//Check non-required attributes for blanks which have defaults defined.
		$this->setDefaultValuesOnEmptyAttributes();
		//Auto-register all attributes as template tokens.
		$this->attributesToTokens();
		//Add children to this element if it has any.
		foreach ($this->rawElement->children() as $childElement)
		{
			$this->addChild($childElement);
		}
	}
	
	public function __toString()
	{
		$children='';
		foreach ($this->children as $child)
		{
			$children.=(string)$child;
		}
		$this->registerTemplateToken('children',$children);
		$template=$this->template();
		return @str_replace(array_keys($this->templateTokens),array_values($this->templateTokens),$template);//Suppress array to string conversion error.
	}
	
	private function extractAttributes()
	{
		$attrs=$this->rawElement->attributes();//NOTE: needs to be done this way or PHP throws a strict error.
		$attributes=reset($attrs);
		foreach ($attributes as $attributeName=>$attributeValue)
		{
			//Handle Sub-FML expressions.
			if ($attributeName!='pattern')//TODO: Find a better solution for this. This is a hack more than anything.
			{
				if ($this->instance->parent->subfml->containsExpression($attributeValue))
				{
					$attributeValue=$this->instance->parent->subfml->doSubEvaluation($this->scope,$attributeValue);
				}
			}
			//Save the attribute.
			$this->rawAttributes[$attributeName]=$attributeValue;
		}
	}
	
	public function setScope()
	{
		if (isset($this->rawAttributes['scope']))
		{
			$this->scope=$this->up('#'.$this->rawAttributes['scope']);
			if (!is_object($this->scope))
			{
				$this->instance->error(0,0,'Invalid conditional scope "'.$this->rawAttributes['scope'].'".');
			}
		}
		else
		{
			$this->scope=$this;
		}
		return $this;
	}
	
	public function addChild($childElement=null)
	{
		$return=false;
		if (is_object($childElement))
		{
			$className='fml_element_'.$childElement->getName();
			if (!class_exists($className))
			{
				$this->instance->error(0,0,$childElement->getName().' is an invalid element.');
			}
//			elseif (!isset($childElement['id']))
//			{
//				$this->instance->error(0,0,'Required attribute "id" was not defined.');
//			}
			else
			{
				if (!isset($childElement['id']))
				{
					$childElement['id']=$this->instance->generateID($childElement->getName());
				}
//				print $childElement->getName().'<br />';
				$this->children[(string)$childElement['id']]=new $className($this,$this->instance,$childElement);
				$this->children[(string)$childElement['id']]->init();
				$return=true;
			}
		}
		return $return;
	}
	
	public function getChildrenByElementName($elementName=null)
	{
		$return=array();
		if (!empty($elementName) && count($this->children))
		{
			foreach ($this->children as $child)
			{
				if ($child->rawElement->getName()==$elementName)
				{
					$return[]=$child;
				}
			}
			if (!count($return))$return=false;
		}
		return $return;
	}
	
	private function parse()
	{
		$parentElement=$this->getParentElement();
		if (is_object($parentElement))
		{
			if (!$this->validatePosition($parentElement->getName()))
			{
				$this->instance->error(0,0,$this->rawElement->getName().' cannot be a child of '.$parentElement->getName().'.');
			}
			elseif(!$this->validateAttributes())
			{
				$this->instance->error(0,0,$this->rawElement->getName().' has invalid attributes.');
			}
			elseif ($this->instance->idRecorded((string)$this->rawElement['id']))
			{
				$this->instance->error(0,0,'Duplicate ID "'.$this->rawElement['id'].'".');
			}
			else
			{
				$this->instance->recordId((string)$this->rawElement['id']);
			}
		}
		return true;
	}
	
	public function validatePosition($parent=false)
	{
		return in_array($parent,$this->validParents);
	}
	
	protected function validateAttributes()
	{
		$return=false;
		if (!empty($this->rawElement))
		{
			$requiredAttributes=$this->getRequiredAttributes();
			if (!count($this->rawAttributes))
			{
				$this->instance->error(0,0,'Required attribute "id" was not defined.');
			}
			else
			{
				$attributes=array_keys($this->rawAttributes);
				//Loop through required attributes.
				for ($i=0,$j=count($requiredAttributes); $i<$j; $i++)
				{
					//If required attribute has not been defined, throw an error.
					if (!in_array($requiredAttributes[$i]['attribute'],$attributes))
					{
						$this->instance->error(0,0,'Required attribute "'.$requiredAttributes[$i]['attribute'].'" was not defined.');
					}
					//If required attribute is empty, throw an error.
					elseif (empty($attributes))
					{
						$this->instance->error(0,0,'Required attribute "'.$requiredAttributes[$i]['attribute'].'" cannot be blank.');
					}
				}
				//Loop through element defined attributes.
				foreach ($this->rawAttributes as $attributeName=>$attribute)
				{
					//Grab the attribute out of the element definition.
					$thisAttribute=$this->getAttribute($attributeName);
					//If it does not exist, throw an error because this element is not allowed to define it.
					if (!$thisAttribute)
					{
						$this->instance->error(0,0,'Invalid attribute "'.$attributeName.'".');
					}
					//If it does exist, validate it.
					else
					{
						//The data type validator will throw an error if it encounters any problem.
						$this->instance->parent->datatype->validate($thisAttribute,$attribute);
						if (method_exists($this,'validateAttribute'))
						{
							$this->validateAttribute($attributeName,$attribute);
						}
					}
				}
			}
			$return=true;
		}
		return $return;
	}
	
	protected function validateManualAttribute($attributeName=null,$attributeValue=null)
	{
		$return=false;
		if (!empty($attributeName) && !is_null($attributeValue))
		{
			//Grab the attribute out of the element definition.
			$thisAttribute=$this->getAttribute($attributeName);
			//If it does not exist, throw an error because this element is not allowed to define it.
			if (!$thisAttribute)
			{
				$this->instance->error(0,0,'Invalid attribute "'.$attributeName.'".');
			}
			//If it does exist, validate it.
			else
			{
				//See if this attribute is a required one and if it is, check that it isn't empty.
				if ($thisAttribute['required']===true && empty($attributeValue))
				{
					$this->instance->error(0,0,'Required attribute "'.$attributeName.'" cannot be blank.');
				}
				else
				{
					$this->instance->parent->datatype->validate($thisAttribute,$attributeValue);
					if (method_exists($this,'validateAttribute'))
					{
						$this->validateAttribute($attributeName,$attributeValue);
					}
					$return=true;
				}
			}
		}
		return $return;
	}
	
	protected function setDefaultValuesOnEmptyAttributes()
	{
		for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
		{
//			if (!is_null($this->validAttributes[$i]['default']))//NOTE: Changed by Tim to fixed issue with being unable to set values on attributes that default to null.
//			{
//				
//			}
			if (isset($this->rawAttributes[$this->validAttributes[$i]['attribute']]))
			{
				$attribute=&$this->rawAttributes[$this->validAttributes[$i]['attribute']];
				if (empty($attribute) && $attribute!=='0' && $attribute!==0)
				{
					$attribute=$this->validAttributes[$i]['default'];
				}
			}
			else
			{
				$this->rawAttributes[$this->validAttributes[$i]['attribute']]=$this->validAttributes[$i]['default'];
			}
		}
		return $this;
	}
	
	protected function attributesToTokens()
	{
		for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
		{
			$this->registerTemplateToken($this->validAttributes[$i]['attribute'],(isset($this->rawAttributes[$this->validAttributes[$i]['attribute']])?$this->rawAttributes[$this->validAttributes[$i]['attribute']]:''));
		}
		return $this;
	}
	
	protected function setParent($parent=null)
	{
		if (!empty($parent))
		{
			if ($this->validParents[0]===false)$this->validParents=array();
			array_push($this->validParents,$parent);
		}
		return $this;
	}
	
	public function setAttribute($attribute=null,$default=null,$dataType=null,$dataDefinitions=null)
	{
		if (!empty($attribute) && is_int($dataType))
		{
			$newAttribute=array
			(
				'attribute'	=>$attribute,
				'required'	=>false,
				'default'	=>$default,
				'dataType'	=>$dataType
			);
			if (is_array($dataDefinitions))
			{
				$newAttribute['dataDefinitions']=array_flip(array_map('strtolower',array_flip($dataDefinitions)));
			}
			array_push($this->validAttributes,$newAttribute);
		}
		return $this;
	}
	
	public function unsetAttribute($attribute=null)
	{
		$return=false;
		if (isset($this->validAttributes[$attribute]))
		{
			unset($this->validAttributes[$attribute]);
			$return=true;
		}
		return $return;
	}
	
	public function setRequiredAttribute($attribute=null,$default=null,$dataType=null,$dataDefinitions=null)
	{
		if (!empty($attribute) && is_int($dataType))
		{
			$newAttribute=array
			(
				'attribute'		=>$attribute,
				'required'		=>true,
				'default'		=>$default,
				'dataType'		=>$dataType
			);
			if (is_array($dataDefinitions))
			{
				$newAttribute['dataDefinitions']=array_flip(array_map('strtolower',array_flip($dataDefinitions)));
			}
			array_push($this->validAttributes,$newAttribute);
		}
		return $this;
	}
	
	protected function getAttribute($name=null)
	{
		$return=false;
		if (!is_null($name))
		{
			for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
			{
				if ($this->validAttributes[$i]['attribute']==$name)
				{
					$return=$this->validAttributes[$i];
					break;
				}
			}
		}
		return $return;
	}
	
	protected function getAttributes($key=false)
	{
		$return=array();
		if ($key)
		{
			for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
			{
				$return[]=$this->validAttributes[$i][$key];
			}
		}
		else
		{
			$return=$this->validAttributes;
		}
		return $return;
	}
	
	protected function getRequiredAttributes()
	{
		$return=array();
		for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
		{
			if ($this->validAttributes[$i]['required'])
			{
				$return[]=$this->validAttributes[$i];
			}
		}
		return $return;
	}
	
	protected function getOptionalAttributes()
	{
		$return=array();
		for ($i=0,$j=count($this->validAttributes); $i<$j; $i++)
		{
			if (!$this->validAttributes[$i]['required'])
			{
				$return[]=$this->validAttributes[$i];
			}
		}
		return $return;
	}
	
	public function getParentElement()
	{
		return $this->parent->rawElement;
	}
	
	public function registerTemplateToken($token=null,$value=null)
	{
		if (!empty($token))
		{
			$this->templateTokens['{'.strtoupper($token).'}']=$value;
		}
		return $this;
	}
	
	public function id()
	{
		return $this->rawAttributes['id'];
	}
	
	public function name()
	{
		return $this->rawElement->getName();
	}
	
	/* Element Query API */
	
	public function up($query=false)
	{
		$return=false;
		//If there is no query, get the parent element.
		if (!$query)
		{
			$return=$this->parent;
		}
		//Else do a search based on the query.
		else
		{
			//Search for tokens within the query.
			if ($this->hasQueryToken($query))
			{
				//Perform an advanced search.
				if (strstr($query,'#'))
				{
					if (!strpos($query,0,1)!='#')
					{
						$this->instance->exception('Invalid FML query "'.$query.'".');
					}
					else
					{
						list(,$id)=explode('#',$query);
						$element=$this->parent;
						while (is_object($element))
						{
							if ($element->rawAttributes['id']==$id)
							{
								$return=$element;
								break;
							}
							else
							{
								$element=$element->parent;
							}
						}
					}
				}
				else
				{
					$this->instance->exception('Invalid FML query "'.$query.'".');
				}
			}
			else
			{
				//Perform a simple search.
				$element=$this->parent;
				while (is_object($element))
				{
					if ($element->name()==$query)
					{
						$return=$element;
						break;
					}
					else
					{
						$element=$element->parent;
					}
				}
			}
		}
		if (!$return)
		{
			$this->instance->exception('Invalid FML query "'.$query.'".');
		}
		return $return;
	}
	
	public function down($query=false)
	{
		$return=array();
		//If there is no query, get the child elements.
		if (!$query)
		{
			$return=$this->children;
		}
		//Else do a search based on the query.
		else
		{
			//Search for tokens within the query.
			if ($this->hasQueryToken($query))
			{
				//Perform an advanced search.
				if (strstr($query,'#'))
				{
					if (!strpos($query,0,1)!='#')
					{
						$this->instance->exception('Invalid FML query "'.$query.'".');
					}
					else
					{
						list(,$id)=explode('#',$query);
						$return=$this->_down($this,$id);
					}
				}
				else
				{
					$this->instance->exception('Invalid FML query "'.$query.'".');
				}
			}
			else
			{
				//Perform a simple search.
				$return=array();
				foreach ($this->children as $child)
				{
					if ($child->name()==$query)
					{
						$return[]=$child;
						break;
					}
				}
				if (!count($return))$return=false;
			}
		}
//		if (!$return)
//		{
//			$this->instance->exception('Invalid FML query "'.$query.'".');
//		}
		return $return;
	}
	
	private function _down($element,$id)
	{
		$return=false;
		foreach ($element->children as $child)
		{
			if ($child->rawAttributes['id']==$id)
			{
				$return=$child;
				break;
			}
			elseif (count($child->children))
			{
				$return=$child->_down($child,$id);
				if (is_object($return))break;
			}
		}
		return $return;
	}
	
	//NOTE: Will probably extend this later.
	private function hasQueryToken($query=false)
	{
		$return=false;
		if ($query)
		{
			if (strstr($query,'#'))
			{
				$return=true;
			}
		}
		return $return;
	}
	
	public function destroyChildElements()
	{
		$fml=dom_import_simplexml($this->rawElement);
		$dom=new DOMDocument();
		$fml=$dom->importNode($fml,true);
		$fml=$dom->appendChild($fml);
		$condition=$dom->firstChild;
		while ($condition->hasChildNodes())
		{
			$condition->removeChild($condition->childNodes->item(0));
		}
		$this->rawElement=simplexml_import_dom($condition);
		return true;
	}
	
	public function destroyChildElementsByElementName($elementName=null)
	{
		$return=false;
		if (!empty($elementName))
		{
			$fml=dom_import_simplexml($this->rawElement);
			$dom=new DOMDocument();
			$fml=$dom->importNode($fml,true);
			$fml=$dom->appendChild($fml);
			$element=$dom->firstChild;
			$xPath=new DOMXPath($dom);
			$result=$xPath->query($elementName);
			foreach ($result as $node)
			{
				$element->removeChild($node);
			}
			$this->rawElement=simplexml_import_dom($element);
			$return=true;
		}
		return $return;
	}
	
	public function setAttributeValue($attribute=null,$value='')
	{
		if (!isset($this->rawAttributes[$attribute]) && !is_null($this->rawAttributes[$attribute]))
		{
			$this->instance->error(0,0,'"'.$attribute.'" is an invalid attribute.');
		}
		else
		{
			$this->rawAttributes[$attribute]=$value;
			$this->validateManualAttribute($attribute,$value);
			$this->registerTemplateToken($attribute,$value);
		}
		return $this;
	}
	
	public function getAttributeValue($attribute=null)
	{
		if (!isset($this->rawAttributes[$attribute]) && !is_null($this->rawAttributes[$attribute]))
		{
			$this->instance->error(0,0,'"'.$attribute.'" is an invalid attribute.');
		}
		else
		{
			return $this->rawAttributes[$attribute];
		}
	}
	
	public function clean(&$value=null)
	{
		if (is_array($value))
		{
			foreach ($value as &$val)
			{
				$this->clean($val);
			}
		}
		else
		{
			$value=addslashes($value);
		}
	}
	
	public function isChildOf($elementType='')
	{
		$return=false;
		$element=$this;
		while (!($element instanceof fml_element_form))
		{
			if ($element->rawElement->getName()==$elementType)
			{
				$return=true;
				break;
			}
			else
			{
				$element=$this->parent;//Same as "$this->up()", but without function overhead.
			}
		}
		return $return;
	}
}
?>