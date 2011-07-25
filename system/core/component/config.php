<?php
/*
 * Simple Core 2.0
 * Copyright(c) 2010, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * 
 * 
 * NOTE: This was backported from Simple Core 3 though is now heavily modified
 * and will likely be ported back to Simple Core 3.
 * @author Timothy Chandler <tim@s3.net.au>
 *
 */
class config implements ArrayAccess,SeekableIterator,Countable
{
	/*** [START Implementation of Array Access] ***/
	
	private $config		=null;
	private $attributes	=array();
	
	/**
	 * 
	 * @param $offset
	 */
	public function offsetExists($offset)
	{
		return ((isset($this->attributes[$offset])) || (is_int($offset) && isset($this->{$offset})));
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 */
	public function offsetGet($offset)
	{
		if (isset($this->attributes[$offset]))
		{
			return $this->attributes[$offset];
		}
		elseif (is_int($offset) && isset($this->{$offset}))
		{
			return $this->{$offset};
		}
		else
		{
			//TODO: Throw user notice.
			return null;
		}
//		else
//		{
//			throw new Exception('E04 - offset "'.$offset.'" doesn\'t exist.');
//		}
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 * @param unknown_type $value
	 */
	public function offsetSet($offset,$value)
	{
		$this->attributes[$offset]=$value;
	}
	
	/**
	 * 
	 * @param unknown_type $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->attributes[$offset]);
	}
	
	/*** [END Implementation of Array Access] ***/
	
	
	/*** [START Implementation of Seekable Iterator] ***/
	
	private $INDEX			=array();
	private $POSITION		=0;
	private $CURRENT		=null;
	
	public function current()
	{
		return $this->config->{$this->CURRENT};
	}
	
	public function key()
	{
		return $this->CURRENT;
	}
	
	public function next()
	{
		$this->POSITION++;
		if (isset($this->config->{$this->POSITION}))
		{
			$this->CURRENT=$this->INDEX[$this->POSITION];
		}
		else
		{
			$this->CURRENT=null;
		}
		return $this;
	}
	
	public function rewind()
	{
		$this->POSITION=0;
		if (isset($this->INDEX[0]))
		{
			$this->CURRENT=$this->INDEX[0];
		}
		else
		{
			$this->CURRENT=null;
		}
		return $this;
	}
	
	public function valid()
	{
		return !is_null($this->CURRENT);
	}
	
	public function seek($position)
	{
		$this->POSITION=$position;
		if (isset($this->config->{$this->POSITION}))
		{
			$this->CURRENT=$this->INDEX[$this->POSITION];
		}
		else
		{
			$this->CURRENT=null;
		}
		if (!$this->valid())
		{
			 throw new OutOfBoundsException('Invalid seek position ('.$position.').');
		}
	}
	
	/*** [END Implementation of Seekable Iterator] ***/
	
	/*** [START Implementation of Countable] ***/
	
	public $length=0;
	
	public function count()
	{
		return $this->length;
	}
	
	/*** [END Implementation of Countable] ***/
	
	//Methods for config
	
	/**
	 * 
	 * @param unknown_type $object
	 */
	public function __construct($object=null)
	{
		$this->config	=new stdClass;
		$XML			=null;
		if (is_file($object))
		{
			$XML=simplexml_load_file($object);
		}
		elseif (is_string($object))
		{
			$XML=simplexml_load_string($object);
		}
		else if ($object instanceof SimpleXMLElement)
		{
			$XML=$object;
		}
		if ($XML)$this->normalizeSimpleXML($this->config,$XML);
		unset($XML);
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 */
	public function __get($key)
	{
		if (isset($this->config->{$key}))
		{
			return $this->config->{$key};
		}
		else
		{
//			throw new Exception('Config parameter "'.$key.'" has not been set.');
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $val
	 */
	public function __set($key,$val)
	{
		$this->config->{$key}=$val;
//		$this->INDEX[]=$this->config->{$key};
//		$this->length++;
//		if (is_null($this->CURRENT))
//		{
//			$this->CURRENT=$this->config->{$key};
//		}
	}
	
	public function __isset($key)
	{
		return isset($this->config->{$key});
	}
	
	public function __unset($key)
	{
		if (isset($this->config->{$key}))
		{
			$this->resetIndex($this->config->{$key});
			unset($this->config->{$key});
			$this->length--;
//			if ($this->CURRENT===$this->config->{$key})
//			{
//				
//				
//			}
//			else
//			{
//				
//			}
		}
	}
	
	private function resetIndex($exclude=null)
	{
		$newIndex=array();
		for ($i=0,$j=count($this->INDEX); $i<$j; $i++)
		{
			if ($this->INDEX[$i]!==$exclude)
			{
				$newIndex[]=$this->INDEX[$i];
			}
		}
		$this->INDEX=$newIndex;
		if (isset($this->INDEX[$this->POSITION]))
		{
			$this->CURRENT=$this->INDEX[$this->POSITION];
		}
		else
		{
			$this->CURRENT=null;
		}
	}
	
	/**
	 * 
	 */
	public function __toString()
	{
		return 'SimpleCore Config {}';
	}
	
	/**
	 * 
	 * @param SimpleXMLElement $config
	 */
	private function normalizeSimpleXML(&$parentConfig,SimpleXMLElement $config)
	{
		$tmp	=(array)$config;
		foreach ($tmp as $key=>$val)
		{
			if (is_array($val))
			{
				if ($key!='@attributes')
				{
					$index=0;
					$parentConfig->{$key}=new self;
					foreach ($val as $aKey=>$aVal)
					{
						if ($aVal instanceof SimpleXMLElement)
						{
							$parentConfig->{$key}->{$index}=new self($aVal);
							$attributes=$aVal->attributes();
							if (count($attributes))
							{
								foreach ($attributes as $attrKey=>$attrVal)
								{
									$parentConfig->{$key}->{$index}->attributes[$attrKey]=$this->parseValue($attrVal);
								}
							}
							else
							{
								unset($parentConfig->{$key}->{$index});
								$parentConfig->{$key}->{$index}='';
							}
							unset($parentConfig->{'@attributes'});
						}
						else
						{
							$parentConfig->{$key}->{$index}=$this->parseValue($aVal);
						}
						if ($parentConfig->{$key} instanceof self)
						{
//							$parentConfig->{$key}->length++;
//							$parentConfig->{$key}->INDEX[]=$parentConfig->{$key}->{$index};
//							if (is_null($parentConfig->{$key}->CURRENT))
//							{
//								$parentConfig->{$key}->CURRENT=$parentConfig->{$key}->{$index};
//							}
							$parentConfig->{$key}->length++;
							$parentConfig->{$key}->INDEX[]=$index;
							if (is_null($parentConfig->{$key}->CURRENT))
							{
								$parentConfig->{$key}->CURRENT=$index;
							}
						}
						$index++;
					}
				}
			}
			elseif ($val instanceof SimpleXMLElement)
			{
				$parentConfig->{$key}=new self($val);
				$attributes=$val->attributes();
				if (count($attributes))
				{
					foreach ($attributes as $attrKey=>$attrVal)
					{
						$parentConfig->{$key}->attributes[$attrKey]=$this->parseValue($attrVal);
					}
				}
				elseif (!count($val->children()))
				{
					unset($parentConfig->{$key});
					$parentConfig->{$key}='';
				}
				unset($parentConfig->{'@attributes'});
			}
			else
			{
				$parentConfig->{$key}=$this->parseValue($val);
			}
			if ($key!='@attributes')
			{
//				$this->INDEX[]=$parentConfig->{$key};
//				$this->length++;
//				if (is_null($this->CURRENT))
//				{
//					$this->CURRENT=$parentConfig->{$key};
//				}
				$this->INDEX[]=$key;
				$this->length++;
				if (is_null($this->CURRENT))
				{
					$this->CURRENT=$key;
				}
				
			}
		}
		return $parentConfig;
	}
	
	/**
	 * 
	 * @param mixed $value
	 */
	private function parseValue($value)
	{
		if (!is_array($value))
		{
			settype($value,'string');
			if (is_numeric($value))
			{
				settype($value,'integer');
			}
			$matches=array();
			if (preg_match_all('/\{(\$\w*)\}/i',$value,$matches))
			{
				for ($i=0, $j=count($matches[1]); $i<$j; $i++)
				{
					switch ($matches[1][$i])
					{
						case '$ROOT':
						{
							$value=str_replace($matches[0][$i],$GLOBALS['CORE']->my->dir,$value);
							break;
						}
					}
				}
			}
		}
		else
		{
			foreach ($value as $key=>$val)
			{
				$value[$key]=$this->parseValue($val);
			}
		}
		return $value;
	}
	
	public function toArray()
	{
		return new ArrayObject($this->config);
	}
}
?>