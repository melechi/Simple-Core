<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
/**
 * Hash table class.
 * 
 * 
 * @implements SeekableIterator
 * @author Timothy Chandler
 * @copyright Simple Site Solutions
 */
class hash implements SeekableIterator
{
	private $object=null;
	private $__POINTER__=0;
	private $__LOOKUPTABLE__=array();
	private $__SIZE__=0;
	
	/**
	 * Class constructor. Can accept a key value to start the hash table's values.
	 * 
	 * Privately sets up the internal hash table.
	 * 
	 * @access public
	 * @param $key string Value lookup key.
	 * @param $value mixed Value to store in $key.
	 * @return bool
	 */
	
	public function __construct($key=null,$value=null)
	{
		$this->object=new stdClass;
		if ($key)
		{
			$this->set($key,$value);
		}
		return true;
	}
	
	/**
	 * Used internally to keep the lookup table up-to-date.
	 * 
	 * Adds keys to the lookup table.
	 * 
	 * @access private
	 * @param string $key An existing key set in the hash table.
	 * @return object;
	 */
	
	private function addToLookupTable($key)
	{
		if (!$this->findKey($key))
		{
			$this->__LOOKUPTABLE__[]=$key;
		}
		return $this;
	}
	
	/**
	 * Used internally to keep the lookup table up-to-date.
	 * 
	 * Removes keys from the lookup table.
	 * 
	 * @access private
	 * @param string $key An existing key set in the hash table.
	 * @return object
	 */
	
	private function removeFromLookupTable($key)
	{
		unset($this->__LOOKUPTABLE__[$this->findKey($key)]);
		sort($this->__LOOKUPTABLE__);
		return $this;
	}
	
	/**
	 * Used internally to find and return a key in the lookup table.
	 * 
	 * @access private
	 * @param string $key An existing key set in the hash table.
	 * @return int
	 */
	
	private function findKey($key)
	{
		return array_search($key,$this->__LOOKUPTABLE__);
	}
	
	/**
	 * Sets a value in the hash table.
	 * 
	 * @access public
	 * @param string $key An existing key set in the hash table.
	 * @param mixed $value Value to store in the $key.
	 * @return object
	 */
	
	public function set($key=null,$value=null)
	{
		if (!empty($key))
		{
			$this->object->{$key}=$value;
			$this->addToLookupTable($key);
			++$this->__SIZE__;
		}
		return $this;
	}
	
	/**
	 * Returns a value from the hash table.
	 * 
	 * @access public
	 * @param string $key An existing key set in the hash table.
	 * @return mixed
	 */
	
	public function get($key=null)
	{
		$return=null;
		if (!empty($key))
		{
			if (isset($this->object->{$key}))
			{
				$return=$this->object->{$key};
			}
		}
		return $return;
	}
	
	/**
	 * Returns the hash table as an array.
	 * 
	 * @access public
	 * @return array
	 */
	
	public function toArray()
	{
		return (array)$this->object;
	}
	
	/**
	 * Checks if a key is set in the hash table.
	 * 
	 * @access public
	 * @param string $key An existing key set in the hash table.
	 * @return boolean
	 */
	
	public function is_set($key)
	{
		return isset($this->object->{$key});
	}
	
	/**
	 * Removes a value from the hash table.
	 * 
	 * @access public
	 * @param string $key An existing key set in the hash table.
	 * @return object
	 */
	
	public function un_set($key)
	{
		unset($this->object->{$key});
		$this->removeFromLookupTable($key);
		--$this->__SIZE__;
		return $this;
	}
	
	/**
	 * Returns the value from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function current()
	{
		return ($this->__SIZE__)?$this->get($this->__LOOKUPTABLE__[$this->__POINTER__]):null;
	}
	
	/**
	 * Returns the key from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function key()
	{
		return $this->__LOOKUPTABLE__[$this->__POINTER__];
	}
	
	/**
	 * Increments the internal pointer and returns the value from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function next()
	{
		$return=null;
		++$this->__POINTER__;
		if ($this->valid())
		{
			$return=$this->current();
		}
		return $return;
	}
	
	/**
	 * Decrements the internal pointer and returns the value from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function back()
	{
		if ($this->__POINTER__!=0)--$this->__POINTER__;
		return $this->current();
	}
	
	/**
	 * Rewinds the internal pointer and returns the value from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function rewind()
	{
		$this->__POINTER__=0;
		return $this->current();
	}
	
	/**
	 * Checks if the pointer is pointing to a valid hash table entry.
	 * 
	 * @access public
	 * @return mixed
	 */
	
	public function valid()
	{
		return $this->__POINTER__<count($this->__LOOKUPTABLE__);
	}
	
	/**
	 * Moves the internal pointer to $position and returns the value from the hash table which the internal pointer is pointing to.
	 * 
	 * @access public
	 * @param int $position The position to set the internal pointer to.
	 * @return mixed
	 */
	
	public function seek($position)
	{
		$return=null;
		if (isset($this->__LOOKUPTABLE__[$position]))
		{
			$this->__POINTER__=$position;
			$return=$this->current();
		}
		return $return;
	}
}
/**
 * Converts an array into a hash.
 * 
 * @global
 * @param array $array The array to be converted.
 * @param boolean $recursive Makes the parser recursively convert arrays to hashs.
 * @return object
 */
function array2hash($array=array(),$recursive=false)
{
	$return=new hash;
	if (!$recursive)
	{
		foreach ($array as $key=>$val)
		{
			$return->set($key,$val);
		}	
	}
	else
	{
		foreach ($array as $key=>$val)
		{
			if (!is_array($val))
			{
				$return->set($key,$val);
			}
			else
			{
				$return->set($key,array2hash($val,true));
			}
		}
	}
	return $return;
}
/**
 * Converts an object into a hash.
 * 
 * @global
 * @param object $object The object to be converted.
 * @param boolean $recursive Makes the parser recursively convert objects to hashs.
 * @return object
 */
function object2hash($object=null,$recursive=false)
{
	$return=false;
	if (is_object($object))
	{
		if (!$recursive)
		{
			$return=array2hash(get_object_vars($object));
		}
		else
		{
			$return=new hash;
			$vars=get_object_vars($object);
			foreach ($vars as $key=>$val)
			{
				if (!is_object($val))
				{
					$return->set($key,$val);
				}
				else
				{
					$return->set($key,object2hash($val,true));
				}
			}
		}
	}
	return $return;
}
?>