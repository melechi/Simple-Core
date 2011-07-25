<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
abstract class ext_grid_getSetOptions
{
	private $options=array();
	private $blacklist=array();
	
	public function setOption()
	{
		$args=func_get_args();
		$numArgs=func_num_args();
		if (!$numArgs)
		{
			//ERROR
		}
		elseif ($numArgs<2)
		{
			if (!is_array($args[0]))
			{
				//ERROR
			}
			else
			{
				reset($args[0]);
				while(list($key,$val)=each($args[0]))
				{
					if (in_array($key,$this->blacklist))
					{
						//ERROR
						break;
					}
					else
					{
						$this->options[$key]=$val;
					}
				}
			}
		}
		elseif ($numArgs==2)
		{
			if (in_array($args[0],$this->blacklist))
			{
				//ERROR
			}
			else
			{
				$this->options[$args[0]]=$args[1];
			}
		}
		return $this;
	}
	
	public function getOption($theOption=null)
	{
		$return=false;
		if (!isset($this->options[$theOption]))
		{
			//ERROR
		}
		else
		{
			$return=$this->options[$theOption];
		}
		return $return;
	}
	
	public function getOptions()
	{
		return $this->options;
	}
}
?>