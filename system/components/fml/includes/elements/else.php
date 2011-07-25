<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_element_else extends fml_element_condition
{
	public function initiate()
	{
		$results=$this->up()->down('if');
		$if=reset($results);
		if ($if->result===true)
		{
			$this->destroyChildElements();
		}
		else
		{
			$this->result=true;
			$elseif=$this->up()->down('elseif');
			if (is_array($elseif) && count($elseif))
			{
				for ($i=0,$j=count($elseif); $i<$j; $i++)
				{
					if ($elseif[$i]->result===true)
					{
						$this->destroyChildElements();
						$this->result=false;
						break;
					}
				}
			}
		}
		return true;
	}
	
	public function attributes()
	{
		return true;
	}
}
?>