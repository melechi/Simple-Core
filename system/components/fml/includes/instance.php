<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_instance extends overloader
{
	public $xIncludeFolder='elements';
	
	private $fml=false;
	
	public $scope=false;
	private $version=false;
	public $local=false;
	public $mode=false;
	public $rawElement=false;
	public $elementName=false;
	public $valid=true;
	
	private $root=false;
	private $ids=array();
	private $queries=array();
	private $generatedIDs=array();
	
	public function __construct(component_fml $parent,application $scope,$theForm,$mode)
	{
		parent::__construct($parent);
		$this->scope=$scope;
		$this->mode=$mode;
		$this->my->dir=$this->parent->my->includeDir;
		$this->my->includeDir=$this->my->dir.$this->xIncludeFolder._;
		if (!is_file($theForm))
		{
			$this->exception('Unable to generate FML form. "'.$theForm.'" was not found.');
		}
		else
		{
			$elements=simplexml_load_file($this->parent->my->dir.'includeOrder.xml');
			foreach ($elements->children() as $file)
			{
				include_once($this->my->includeDir.(string)$file['element'].'.php');
			}
			$this->fml=simplexml_load_file($theForm);
			if (!isset($this->fml['version']))
			{
				$this->parent->error(0,0,'Missing version information.');
			}
//			elseif (!isset($this->fml['local']))
//			{
//				$this->parent->error(0,0,'Missing language information.');
//			}
			elseif (!isset($this->fml->form))
			{
				$this->parent->error(0,0,'Missing root element <form>.');
			}
			else
			{
				$this->version=(string)$this->fml['version'];
				//Load local definition file if it has been defined.
				if (isset($this->fml['local']))
				{
					$this->local=(string)$this->fml['local'];
					$this->parent->loadLocalDefinitionsFromFile($scope,(string)$this->fml['local']);
				}
				//Initiate the form creation and parsing process.
				$this->root=new fml_element_form($this,$this,$this->fml->form);
				$this->root->init();
			}
		}
		return true;
	}
	
	public function __toString()
	{
		return (string)$this->root;
	}
	
	public function error($file=null,$line=0,$message=null)
	{
		return $this->parent->error($file,$line,$message);
	}
	
	public function idRecorded($id=null)
	{
		return in_array($id,$this->ids);
	}
	
	public function recordId($id=null)
	{
		array_push($this->ids,$id);
		return true;
	}
	
	public function getRootElement()
	{
		return $this->root;
	}
	
	public function getElementById($id=null)
	{
		$return=false;
		if (!empty($id))
		{
			$return=$this->root->down('#'.$id);
		}
		return $return;
	}
	
	public function isValid()
	{
		return $this->valid;
	}
	
	public function isRegisteredQuery($id=null)
	{
		return isset($this->queries[$id]);
	}
	
	public function registerQuery($id=null,$query=null)
	{
		$return=false;
		if (!empty($id) && !empty($query))
		{
			$this->queries[$id]=$query;
			$return=true;
		}
		return $return;
	}
	
	public function unregisterQuery($id=null)
	{
		$return=false;
		if ($this->isRegisteredQuery($id))
		{
			unset($this->queries[$id]);
			$return=true;
		}
		return $return;
	}
	
	public function getRegisteredQuery($id=null)
	{
		$return=false;
		if ($this->isRegisteredQuery($id))
		{
			$return=$this->queries[$id];
		}
		return $return;
	}
	
	public function generateID($elementType=null)
	{
		$return='';
		if (!empty($elementType))
		{
			if (!isset($this->generatedIDs[$elementType]))
			{
				$this->generatedIDs[$elementType]=array('index'=>0,'ids'=>array());
			}
			$this->generatedIDs[$elementType]['ids'][]=$elementType.'_'.$this->generatedIDs[$elementType]['index'];
			$this->generatedIDs[$elementType]['index']++;
			$return=end($this->generatedIDs[$elementType]['ids']);
		}
		if (empty($return))$return='random_'.mt_rand(1000000,10000000);
		return $return;
	}
	
	public function mapFieldValues(Array $values)
	{
		foreach($values as $key=>$val)
		{
			$element=$this->getElementById($key);
			if ($element && ($element->isChildOf('field') || $element instanceof fml_element_form))
			{
				$element->setValue($val);
			}
		}
		return $this;
	}
}
?>