<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_template extends fml_element
{
	public $template=false;
	public $templateContents='';
	
	public function initiate()
	{
		if (!isset($this->rawAttributes['file']))
		{
			$this->instance->error(0,0,'Parse error. Element <template> is missing required attribute "file".');
		}
		else
		{
			if (isset($this->instance->scope->templateSettings['path']))
			{
				$this->template=$this->instance->scope->my->dir.$this->instance->scope->template['path'].(string)$this->rawAttributes['file'];
			}
			else
			{
				$this->template=$this->instance->scope->my->dir.'templates'._.(string)$this->rawAttributes['file'];
			}
			if (!is_file($this->template))
			{
				$this->instance->error(0,0,'Parse error. Unable to load template. Template "'.$this->template.'" could not be found.');
			}
		}
		return true;
	}
	
	public function parents()
	{
		$this->setParent('form');
		$this->setParent('fieldset');
		$this->setParent('vbox');
		$this->setParent('hbox');
		$this->setParent('field');
		$this->setParent('rule');
		$this->setParent('if');
		$this->setParent('elseif');
		$this->setParent('else');
		$this->setParent('include');
		$this->setParent('eachQueryResult');
		return true;
	}
	
	public function attributes()
	{
		$this->setRequiredAttribute('file',null,FML_DATATYPE_ANY);
		return true;
	}
	
	public function template()
	{
		if ($this->template)
		{
			//TODO: split for smarty.
			$this->templateContents=file_get_contents($this->template);
			$this->templateContents=$this->component->template->parseConditionals	($this->templateContents);
			$this->templateContents=$this->component->template->parseFunctions		($this->templateContents);
			$this->templateContents=$this->component->template->parseTemplateTags	($this->templateContents);
			$this->templateContents=$this->component->template->parseVariables		($this->templateContents);
			$this->templateContents=$this->component->template->parseLoops			($this->templateContents);
			$this->templateContents=$this->component->template->parseURLs			($this->templateContents);
			$this->templateContents=$this->component->template->parseResourceTags	($this->templateContents);
			$this->templateContents=$this->component->template->parseResouceDump	($this->templateContents);
		}
		return $this->templateContents;
	}
}
?>