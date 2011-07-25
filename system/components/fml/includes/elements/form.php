<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_form extends fml_element
{
	public function initiate()
	{
		if (isset($this->rawAttributes['action']))
		{
			$protocol='http';
			if (isset($this->rawAttributes['secure']))
			{
				if ($this->rawAttributes['secure']=='true')
				{
					$protocol='https';
				}
				else
				{
					$protocol='http';
				}
			}
			if (!empty($this->rawAttributes['action']))$this->rawAttributes['action']=$this->instance->scope->makeURL($this->rawAttributes['action'],$protocol);
		}
	}
	
	public function parents()
	{
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute
		(
			'method',
			'post',
			FML_DATATYPE_SET,
			array
			(
				'POST'	=>1,
				'GET'	=>2,
				'AJAX'	=>4
			)
		);
		$this->setAttribute('action',	null,		FML_DATATYPE_STRING);
		$this->setAttribute('secure',	null,		FML_DATATYPE_BOOL);
		$this->setAttribute('class',	null,		FML_DATATYPE_STRING);
		$this->setAttribute('style',	null,		FML_DATATYPE_STRING);
		$this->setAttribute('target',	'_self',	FML_DATATYPE_STRING);
		$this->setAttribute('enctype','application/x-www-form-urlencoded',FML_DATATYPE_STRING);
		return true;
	}
	
	public function validateAttribute($name=null,$value=null)
	{
		if ($name=='method')
		{
			if (strtolower($value)=='ajax')
			{
				$this->parent->error(0,0,'Invalid attribute value "'.$value.'".');
			}
		}
		return true;
	}
	
	public function template()
	{
		return <<<TEMPLATE
<form id="{ID}" method="{METHOD}" action="{ACTION}" class="{CLASS}" style="{STYLE}" target="{TARGET}" enctype="{ENCTYPE}">
	{CHILDREN}
</form>
TEMPLATE;
	}
}
?>