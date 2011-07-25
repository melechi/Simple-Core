<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_option extends fml_element
{
	private $field=null;
	
	public function parents()
	{
		//Set parents.
		$this->setParent('field');
		$this->setParent('eachQueryResult');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		
		//Do some special validation for option.
		$this->field=$this->up('field');
		if (!preg_match('@select|check|radio@',$this->field->rawAttributes['type']))
		{
			$this->instance->error(0,0,'Element field type "'.$this->field->rawAttributes['type'].'" cannot use element type "option" as a child.');
		}
		else
		{
			
			if (isset($this->rawAttributes['selected']))
			{
				$selected=($this->rawAttributes['selected']==='true')?true:false;
			}
			else
			{
				$selected=false;
			}
			$id=(isset($this->rawAttributes['id']))?(string)$this->rawAttributes['id']:null;
			$this->field->type->setOption($id,(string)$this->rawAttributes['label'],(string)$this->rawAttributes['value'],$selected);
		}
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('label',null,FML_DATATYPE_ANY);
		$this->setRequiredAttribute('value',null,FML_DATATYPE_ANY);
		$this->setAttribute('selected',null,FML_DATATYPE_BOOL);
		$this->setAttribute('disabled',false,FML_DATATYPE_BOOL);
		return true;
	}
	
	public function template(){}
	
	public function setID($id=null)
	{
		$this->field->type->changeOption($this->rawAttributes['id'],array('id'=>$id));
		return $this;
	}
	
	public function setValue($value='')
	{
		$this->field->type->changeOption($this->rawAttributes['id'],array('value'=>$value));
		return $this;
	}
	
	public function setLabel($label=null)
	{
		$this->field->type->changeOption($this->rawAttributes['id'],array('label'=>$label));
		return $this;
	}
	
	public function setDisabled($disabled=true)
	{
		$this->field->type->changeOption($this->rawAttributes['id'],array('disabled'=>$disabled));
		return $this;
	}
	
	public function setSelected($selected=true)
	{
		$field->type->changeOption($this->rawAttributes['id'],array('selected'=>$selected));
		return $this;
	}
	
	public function setAttributeValue($attribute=null,$value='')
	{
		if (!isset($this->rawAttributes[$attribute]))
		{
			$this->instance->error(0,0,'"'.$attribute.'" is an invalid attribute.');
		}
		else
		{
			$field->type->changeOption($this->rawAttributes['id'],array($attribute=>$value));
		}
		return $this;
	}
}
?>