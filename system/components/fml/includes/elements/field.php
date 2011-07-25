<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field extends fml_element
{
	public $xIncludeFolder='fieldtypes';

	public $template='';
	public $type=false;
	private $fieldTypes=array();
	private $message=false;
	public $parameters=array();

	public function initiate()
	{
		$iterationNum=1;
		foreach (new DirectoryIterator($this->my->includeDir) as $iteration)
		{
			if ($iteration->isFile())
			{
				$this->fieldTypes[basename($iteration->getFilename(),'.php')]=$iterationNum;
				$iterationNum++;
			}
		}
		unset($iterationNum);
		if (!isset($this->rawAttributes['type']))
		{
			$this->instance->error(0,0,'No element type defined on field element.');
		}
		else
		{
			$className='fml_element_field_type_'.$this->rawAttributes['type'];
			if (!class_exists($className))
			{
				$this->instance->error(0,0,'"'.$this->rawAttributes['type'].'" is an invalid field element type.');
			}
			else
			{
				//Handle parameters.
				$parameters=array();
				foreach ($this->rawElement->children() as $child)
				{
					if ($child->getName()=='parameter')
					{
						$parameters[]=$child;
					}
				}
				foreach ($parameters as $param)
				{
					$this->parameters[(string)@$param['key']]=(string)@$param['value'];
					$this->registerTemplateToken((string)@$param['key'],(string)@$param['value']);
				}
				//Set template.
				$this->template=<<<TEMPLATE
<ul class="row{REQUIRED}">
	<li class="col_1">{REQUIRED_NODE}</li>
	<li class="col_2">{LABEL}</li>
	<li class="col_3">{MESSAGE}{PREFIX}{FIELD}</li>
	<li class="col_4">{SUFFIX}</li>
</ul>
{CHILDREN}
TEMPLATE;
				//Handle field names.
				if (!isset($this->rawAttributes['name']))
				{
					$this->rawAttributes['name']=$this->rawAttributes['id'];
					$this->registerTemplateToken('name',$this->rawAttributes['id']);
				}
				//Handle required tokens for the template before potentially destroying the rules.
				$this->registerTemplateToken('required','');
				$this->registerTemplateToken('required_node','&nbsp;');
//				$result=$this->rawElement->xpath('.//rule[type=required]');
//				if (count($result))
//				{
//					$this->registerTemplateToken('required',' required');
//					$this->registerTemplateToken('required_node','*');
//				}
				//Handle field values.
				if ($this->instance->mode==FML_MODE_VALIDATE)
				{
					$this->rawAttributes['value']=$this->global->post($this->rawAttributes['name']);
					$this->registerTemplateToken('value',$this->global->post($this->rawAttributes['name']));
				}
//				else
//				{
//					$this->destroyChildElementsByElementName('rule');
//				}
				$this->type=new $className($this);
			}
		}
		return true;
	}

	public function parents()
	{
		$this->setParent('include');
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		$this->setParent('eachQueryResult');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('elseif');
		$this->setParent('conditional');
		return true;
	}

	public function attributes()
	{
		$this->setRequiredAttribute('type',	'text',		FML_DATATYPE_ENUM,$this->fieldTypes);
		$this->setAttribute('name',			null,		FML_DATATYPE_STRING);
		$this->setAttribute('label',		'&nbsp;',	FML_DATATYPE_STRING);
		$this->setAttribute('message',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('prefix',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('suffix',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('value',		'',			FML_DATATYPE_ANY);
		$this->setAttribute('size',			null,		FML_DATATYPE_STRING);
		$this->setAttribute('class',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('title',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('style',		null,		FML_DATATYPE_STRING);
		$this->setAttribute('disabled',		false,		FML_DATATYPE_BOOL);
		$this->setAttribute('readonly',		false,		FML_DATATYPE_BOOL);
		return true;
	}

	public function template()
	{
		//Handle disabled attribute.
		if ($this->rawAttributes['disabled']==='true' || $this->rawAttributes['disabled']===true)
		{
			$this->registerTemplateToken('disabled',' disabled="disabled"');
		}
		else
		{
			$this->registerTemplateToken('disabled','');
		}
		if ($this->rawAttributes['readonly']==='true' || $this->rawAttributes['readonly']===true)
		{
			$this->registerTemplateToken('readonly',' readonly="readonly"');
		}
		else
		{
			$this->registerTemplateToken('readonly','');
		}
		$this->registerTemplateToken('label',nl2br($this->rawAttributes['label']));
		$this->templateTokens['{VALUE}']=stripslashes($this->templateTokens['{VALUE}']);
		$this->registerTemplateToken('field',$this->type->template());
		return $this->template;
	}

	public function setMessage($message=false)
	{
//		$this->message=$message;
		$this->rawAttributes['message']=$message;
		$this->registerTemplateToken('message','<label class="message"><div class="message">'.$message.'</div></label><br />');
		return $this;
	}

	public function getMessage()
	{
//		return $this->message
		return $this->rawAttributes['message'];
	}

	public function setValue($value=null)
	{
		$this->clean($value);
		if ($this->rawAttributes['type']=='select'
		|| $this->rawAttributes['type']=='checkbox'
		|| $this->rawAttributes['type']=='radio')
		{
			$this->type->setValue=$value;
		}
		$this->rawAttributes['value']=$value;
		$this->registerTemplateToken('value',$this->rawAttributes['value']);
		return $this;
	}
}
?>
