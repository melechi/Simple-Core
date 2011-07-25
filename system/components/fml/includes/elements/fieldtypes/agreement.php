<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_field_type_agreement extends fml_element_field_type
{
	public $template='';
	
	public function initiate()
	{
		//Set valid attributes.
		$this->parent->setRequiredAttribute('file',null,FML_DATATYPE_STRING);
		
		//Load file.
		$this->parent->registerTemplateToken('agreement',file_get_contents($this->parent->instance->scope->my->dir.$this->parent->rawAttributes['file']));
		$this->parent->template=<<<TEMPLATE
<div id="{ID}" class="agreement{REQUIRED}">
	<div class="label {CLASS}">{LABEL}</div>
	<div style="width:600px;height:150px;overflow:auto;" class="container scrollable">
		{AGREEMENT}
	</div>
	{FIELD}
	<label>{REQUIRED_NODE} {CHECKBOX_LABEL}</label>
	<label><input type="checkbox" value="1" name="{NAME}"{READONLY}{DISABLED} />Yes</label>
</div>
TEMPLATE;
		return true;
	}
}
?>