<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_radio extends fml_element_field_type_select
{
	public $template='<label><input id="{ID}" name="{NAME}" type="{TYPE}" class="{CLASS}" style="{STYLE}" value="{VALUE}"{READONLY}{DISABLED}{SELECTED} />{LABEL}</label>';
	public $selectedTemplate=' checked="checked"';
	
	public function initiate()
	{
		$this->parent->setAttribute('layout','horizontal',FML_DATATYPE_STRING);
		$this->parent->unsetAttribute('disabled');
		return true;
	}
	
	public function fieldTemplate()
	{
		$this->optionTemplate=$this->template;
		$return=$this->compileOptions();
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
		$layout=&$this->parent->rawAttributes['layout'];
		if ($layout=='horizontal')
		{
			for ($i=0,$j=count($this->options); $i<$j; $i++)
			{
				/*
				 * NOTE:
				 * $this->options was being accessed as an array BUT it is an object.
				 * So this has been corrected to be accessed as an object.
				 */
				$checked=$this->getSelected($this->options[$i]->value,$value);
				$fNr=array
				(
					'{ID}'			=>(!is_null($this->options[$i]->id))?$this->options[$i]->id:'',
					'{VALUE}'		=>$this->options[$i]->value,
					'{SELECTED}'	=>$checked,
					'{LABEL}'		=>$this->options[$i]->label,
					'{DISABLED}'	=>$this->options[$i]->disabled,
					'{ITERATION}'	=>$i
				);
				$return.=str_replace(array_keys($fNr),array_values($fNr),$this->optionTemplate);
			}
		}
		elseif ($layout=='vertical')
		{
			for ($i=0,$j=count($this->options); $i<$j; $i++)
			{
				$checked=$this->getSelected($this->options[$i]->value,$value);
				$fNr=array
				(
					'{ID}'			=>(!is_null($this->options[$i]->id))?$this->options[$i]->id:'',
					'{VALUE}'		=>$this->options[$i]->value,
					'{SELECTED}'	=>$checked,
					'{LABEL}'		=>$this->options[$i]->label,
					'{DISABLED}'	=>$this->options[$i]->disabled,
					'{ITERATION}'	=>$i
				);
				$return.=str_replace(array_keys($fNr),array_values($fNr),$this->optionTemplate.'<br />');
			}
		}
		else
		{
			$matches=array();
			// Match digits at beginning of $layout and/or digits prefixed by non-digit char(s),
			// where match[1] represents desired columns and match[2] represents optional maximum
			// elements/column (i.e. if second option is present it overrides first when exceeded).
			preg_match('/(^\d{1,3})?\D*?(\d{1,3})?$/',$layout,$matches);
			if (!empty($matches[1]) || !empty($matches[2]))
			{// $matches[1] = [optional] number of specified columns; $matches[2] = [optional] maximum elements/column
				$j=count($this->options);		//number of (option) elements
				// Determine number of columns ($n) to be parsed.
				if (empty($matches[1]))
				{//...then number of columns determined by putting $matches[2] elements into each column
					$n=ceil($j/$matches[2]);	//number of columns by $matches[2]/column
				}
				elseif (empty($matches[2]))
				{//...then use specified but make sure that $n <= $j
					$n=min($matches[1],$j);		//number of columns as specified by $matches[1]
				}
				else
				{//...then number of columns determined by the greater of preceding expressions.
					$n=min(max($matches[1],ceil($j/$matches[2])),$j);
				}
				$l=max(1, ceil($j/$n));		//number of elements/column (at least 1 element/column)
				$return.='<div class="input group">';
				for ($i=0,$m=0; $i<$j && $m<$n;$m++)
				{// Loop through $n columns using iterator $m
					$return.='<div class="column">';
					for($k=0;$i<$j && $k<$l;$k++, $i++)
					{// Loop through $l elements for current $m column using interator $k
						$checked=$this->getSelected($this->options[$i]->value,$value);
						$fNr=array
						(
							'{ID}'			=>(!is_null($this->options[$i]->id))?$this->options[$i]->id:'',
							'{VALUE}'		=>$this->options[$i]->value,
							'{SELECTED}'	=>$checked,
							'{LABEL}'		=>$this->options[$i]->label,
							'{DISABLED}'	=>$this->options[$i]->disabled,
							'{ITERATION}'	=>$i
						);
						$return.=str_replace(array_keys($fNr),array_values($fNr),$this->optionTemplate.'<br />');
					}
					$return.='</div>';
				}
				$return.='</div>';
			}
		}
		return $return;
	}
}
?>