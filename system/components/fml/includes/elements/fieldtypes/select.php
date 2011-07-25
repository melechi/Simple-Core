<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_select extends fml_element_field_type
{
	public $options=array();
	public $selected=array();
	public $setValue=false;
	public $optionTemplate='<option value="{VALUE}"{SELECTED}{DISABLED}>{LABEL}</option>';
	public $selectedTemplate=' selected="selected"';
	//public $template='';
	
	public function initiate()
	{
		$this->parent->setAttribute('multiple',	'false',	FML_DATATYPE_BOOL);
		$this->parent->setAttribute('size',		1,			FML_DATATYPE_INT);
		//Set the name to an array name if multiple is true.
		if (isset($this->parent->rawAttributes['multiple']) && $this->parent->rawAttributes['multiple']==='true')
		{
			$this->parent->rawAttributes['name'].='[]';
		}
		return true;
	}
	
	public function fieldTemplate()
	{
		//Handle multiple attribute.
		if (isset($this->parent->rawAttributes['multiple']) && $this->parent->rawAttributes['multiple']==='true')
		{
			$multiple=' multiple="multiple"';
		}
		else
		{
			$multiple='';
		}
		$return=<<<TEMPLATE
{$this->template}
<select id="{ID}" name="{NAME}" class="{CLASS}" style="{STYLE}" size="{SIZE}"{$multiple}{READONLY}{DISABLED}>
{$this->compileOptions()}
</select>
TEMPLATE;
		unset($this->parent->rawAttributes['value']);
		return $return;
	}
	
	public function setOption($id=null,$label=null,$value='',$selected=false,$disabled=false)
	{
		$index=count($this->options);
		$this->options[$index]=new stdClass;
		$this->options[$index]->id=$id;
		$this->options[$index]->label=$label;
		$this->options[$index]->value=$value;
		$this->options[$index]->disabled=($disabled?' disabled="disabled"':'');
		$this->options[$index]->selected=$selected;
		if ($selected===true)$this->selected[]=$value;
		return $this;
	}
	
	public function changeOption($id,$params=array())
	{
		$return=false;
		if (isset($id))
		{
			$option=$this->getOptionById($id);
			if ($option)
			{
				//If selected is defined, then remove from selected array.
				if (isset($params['selected']))
				{
					for ($i=0,$j=count($this->selected); $i<$j; $i++)
					{
						if ($this->selected[$i]==$option['value'])
						{
							//Found it, now remove it.
							unset($this->selected[$i]);
							//Sort so that the array keys reset.
							sort($this->selected);
							//We're done - break out of the loop.
							break;
						}
					}
					$option->selected=$params['selected'];
					if ($params['selected']===true)$this->selected[]=(isset($params['value']))?$params['value']:$option['value'];
				}
				if (isset($params['id']))		$option->id			=$params['id'];
				if (isset($params['label']))	$option->label		=$params['label'];
				if (isset($params['value']))	$option->value		=$params['value'];
				if (isset($params['disabled']))	$option->disabled	=($params['disabled']?' disabled="disabled"':'');
			}
		}
		return $return;
	}
	
	public function getOptionById($id)
	{
		$return=false;
		for ($i=0,$j=count($this->options); $i<$j; $i++)
		{
			if ($this->options[$i]->id==$id)
			{
				$return=$this->options[$i];
				break;
			}
		}
		return $return;
	}
	
	public function compileOptions()
	{
		$value=&$this->parent->rawAttributes['value'];
		if (is_array($value))
		{
			$value=$this->getArrayValue($value);
		}
		$return='';
		for ($i=0,$j=count($this->options); $i<$j; $i++)
		{
			$selected=$this->getSelected($this->options[$i]->value,$value);
			$fNr=array
			(
				'{VALUE}'		=>$this->options[$i]->value,
				'{SELECTED}'	=>$selected,
				'{LABEL}'		=>$this->options[$i]->label,
				'{DISABLED}'	=>$this->options[$i]->disabled
			);
			$return.=str_replace(array_keys($fNr),array_values($fNr),$this->optionTemplate);
		}
		return $return;
	}
	
	public function getArrayValue($value=array())
	{
		//Here we are constructing a pointer which will give us the submitted value of $this->rawAttributes['name'];
		$evalMe='$value=$value';
		$matches=array();
		preg_match_all('@\[(\w*)\]@',$this->parent->rawAttributes['name'],$matches);
		for ($i=0,$depth=count($matches[1]); $i<$depth; $i++)
		{
			if (empty($matches[1][$i]))
			{
				break;
			}
			else
			{
				$evalMe.='['.$matches[1][$i].']';
			}
		}
		eval($evalMe.';');
		return $value;
	}
	
	public function getSelected($option,$value)
	{
		$selected='';
		if ($this->parent->instance->mode==FML_MODE_VALIDATE)
		{
			if ($this->setValue!==false)
			{
				if (is_array($this->setValue))
				{
					$selected=in_array($option,$this->setValue)?$this->selectedTemplate:'';
				}
				else
				{
					$selected=$this->isSelected($option,$this->setValue)?$this->selectedTemplate:'';
				}
			}
			else
			{
				$selected=$this->isSelected($option,$value)?$this->selectedTemplate:'';
			}
		}
		else
		{
			if ($this->setValue!==false)
			{
				if (is_array($this->setValue))
				{
					$selected=in_array($option,$this->setValue)?$this->selectedTemplate:'';
				}
				else
				{
					$selected=$this->isSelected($option,$this->setValue)?$this->selectedTemplate:'';
				}
			}
			else
			{
				$selected=in_array($option,$this->selected)?$this->selectedTemplate:'';
			}
		}
		return $selected;
	}
	
	public function isSelected($left=null,$right=null)
	{
		$return=false;
		if (is_array($right))
		{
			$return=in_array($left,array_values($right));
		}
		else
		{
			$return=($left==$right);
		}
		return $return;
	}
}
?>