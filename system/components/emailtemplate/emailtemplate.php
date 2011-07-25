<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/***********************************
 * SIMPLE SITE SOLUTIONS
 **- SIMPLE CORE
 *** - EMAIL TEMPLATE HANDLER
 *** - Version 2.0
 ***********************************/
class component_emailtemplate extends component
{
	const EXCEPTION_NAME='Email Template Exception';
	
	private $template=array();
	private $input=array();
	
	public function injectInput($input=null)
	{
		$return=false;
		if (!@is_null($input))
		{
			$this->input=$input;
			$this->cleanInput();
			$return=true;
		}
		return $return;
	}
	
	public function cleanInput()
	{
		$this->input=array_map('striptags',$this->input);
		$this->input=array_map('trim',$this->input);
		$this->input=array_map('addslashes',$this->input);
		$this->input[]=base64_encode('__clean__');
		return true;
	}
	
	public function isInputClean()
	{
		return @in_array($this->input,base64_encode('__clean__'));
	}
	
	public function getInput($inputItem=null)
	{
		$return=false;
		if (@is_null($inputItem))
		{
			$return=$this->input;
		}
		else
		{
			if (isset($this->input[$inputItem]))
			{
				$return=$this->input[$inputItem];
			}
		}
		return $return;
	}
	
	public function setInput($inputItem=null,$value=null)
	{
		$return=false;
		if (!@is_null($inputItem))
		{
			if (isset($this->input[$inputItem]))
			{
				if (!@is_null($value))
				{
					$this->input[$inputItem]=$value;
					$return=true;
				}
				else
				{
					unset($this->input[$inputItem]);
					$return=true;
				}
			}
		}
		return $return;
	}
	
	public function add()
	{
		$query=<<<SQL
		INSERT INTO `[PREFIX]emailtemplates`
		`emailtemplates_active`,
		`emailtemplates_name`,
		`emailtemplates_description`,
		`emailtemplates_from`,
		`emailtemplates_subject`,
		`emailtemplates_text`,
		`emailtemplates_html`
		)VALUES(
		'{$this->getInput('emailtemplates_active')}',
		'{$this->getInput('emailtemplates_name')}',
		'{$this->getInput('emailtemplates_description')}',
		'{$this->getInput('emailtemplates_from')}',
		'{$this->getInput('emailtemplates_subject')}',
		'{$this->getInput('emailtemplates_text')}',
		'{$this->getInput('emailtemplates_html')}'
SQL;
		return $this->component->database->c('core')->query($query);
	}
	
	public function edit($templateID=null)
	{
		$return=false;
		if ($templateID)
		{
			
		}
		return $return;
	}
	
	public function delete($templateID=null)
	{
		$return=false;
		if ($templateID)
		{
			$query=<<<SQL
			DELETE FROM `[PREFIX]emailtemplates`
			WHERE `emailtemplates_id`='{$templateID}';
SQL;
			if ($this->component->database->c('core')->query($query))$return=true;
		}
		return $return;
	}
	
	public function loadByID($templateID=null)
	{
		$return=false;
		if ($templateID)
		{
			$query=<<<SQL
			SELECT 	`emailtemplates_from`,
					`emailtemplates_subject`,
					`emailtemplates_text`,
					`emailtemplates_html`
			FROM `[PREFIX]emailtemplates`
			WHERE `emailtemplates_id`='{$templateID}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$template=$this->component->database->result();
				$this->template['from']=$template['emailtemplates_from'];
				$this->template['subject']=$template['emailtemplates_subject'];
				$this->template['text']=$template['emailtemplates_text'];
				$this->template['html']=$template['emailtemplates_html'];
			}
		}
		return $return;
	}
	
	public function loadByName($templateName=null)
	{
		$return=false;
		if ($templateName)
		{
			$query=<<<SQL
			SELECT 	`emailtemplates_from`,
					`emailtemplates_subject`,
					`emailtemplates_text`,
					`emailtemplates_html`
			FROM `[PREFIX]emailtemplates`
			WHERE `emailtemplates_name`='{$templateName}'
			LIMIT 1;
SQL;
			if ($this->component->database->c('core')->query($query))
			{
				$template=$this->component->database->result();
				$this->template['from']=$template['emailtemplates_from'];
				$this->template['subject']=$template['emailtemplates_subject'];
				$this->template['text']=$template['emailtemplates_text'];
				$this->template['html']=$template['emailtemplates_html'];
				$return=true;
			}
		}
		return $return;
	}
	
	public function fNr($find=null,$replace=null)
	{
		$return=false;
		if ($find
		&& !empty($this->template['text'])
		&& !empty($this->template['html']))
		{
			if (!$replace)$replace='';
			$this->template['text']=@str_replace($find,$replace,$this->template['text']);
			$this->template['html']=@str_replace($find,$replace,$this->template['html']);
		}
		return $return;
	}
	
	public function from()
	{
		$return=false;
		if (isset($this->template['from']))
		{
			$return=$this->template['from'];
		}
		return $return;
	}
	
	public function subject()
	{
		$return=false;
		if (isset($this->template['subject']))
		{
			$return=$this->template['subject'];
		}
		return $return;
	}
	
	public function bodyText()
	{
		$return=false;
		if (isset($this->template['text']))
		{
			$return=$this->template['text'];
		}
		return $return;
	}
	
	public function bodyHTML()
	{
		$return=false;
		if (isset($this->template['html']))
		{
			$return=$this->template['html'];
		}
		return $return;
	}
}