<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class fml_datatype extends branch
{
	const ANY		=0;
	const STRING	=1;
	const INT		=2;
	const SET		=3;
	const ENUM		=4;
	const ID		=5;
	const BOOL		=6;
	
	public function initiate()
	{
		//Make some global aliases of this classes constants for easy access.
		define('FML_DATATYPE_ANY',		self::ANY);
		define('FML_DATATYPE_STRING',	self::STRING);
		define('FML_DATATYPE_INT',		self::INT);
		define('FML_DATATYPE_SET',		self::SET);
		define('FML_DATATYPE_ENUM',		self::ENUM);
		define('FML_DATATYPE_ID',		self::ID);
		define('FML_DATATYPE_BOOL',		self::BOOL);
		return true;
	}
	
	public function typeNumToString($type=0)
	{
		$return='';
		if (is_numeric($type))
		{
			switch ($type)
			{
				case self::ANY:		$return='Any';		break;
				case self::STRING:	$return='String';	break;
				case self::INT:		$return='Integer';	break;
				case self::SET:		$return='Set';		break;
				case self::ENUM:	$return='Enum';		break;
				case self::ID:		$return='ID';		break;
				case self::BOOL:	$return='Boolean';	break;
			}
		}
		return $return;
	}
	
	public function validate($attribute=null,$data=null)
	{
		//Check for empty $data because an empty $data is invalid.
		if (is_array($attribute) && !empty($data))
		{
			if ($attribute['dataType']==self::SET || $attribute['dataType']==self::ENUM)
			{
				if (!isset($attribute['dataDefinitions']))
				{
					$this->parent->error(0,0,'Attribute of type "'.$this->typeNumToString($attribute['dataType']).'" did not have data definition.');
				}
				elseif (!is_array($attribute['dataDefinitions']))
				{
					$this->parent->error(0,0,'Attribute of type "'.$this->typeNumToString($attribute['dataType']).'" did not have a valid data definition.');
				}
				else
				{
					switch ($attribute['dataType'])
					{
						case self::SET:
						{
							if (strstr($data,'|'))
							{
								$data=explode('|',$data);
								$data=array_map('strtolower',$data);
								for ($i=0,$j=count($data); $i<$j; $i++)
								{
									if (!isset($attribute['dataDefinitions'][$data[$i]]))
									{
										$this->parent->error(0,0,'Invalid attribute value "'.$data[$i].'". Should be SET.');
									}
								}
							}
							else
							{
								if (is_numeric($data))
								{
									$total=array_sum($attribute['dataDefinitions']);
									if (($data & $total)!=$data)
									{
										$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be SET.');
									}
								}
								elseif (!isset($attribute['dataDefinitions'][$data]))
								{
									$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be SET.');
								}
							}
							break;
						}
						case self::ENUM:
						{
							if (!isset($attribute['dataDefinitions'][$data]))
							{
								$this->parent->error(0,0,'Invalid attribute value "'.$data.'".');
							}
							break;
						}
					}
				}
			}
			else switch ($attribute['dataType'])
			{
				case self::ANY:break;
				case self::STRING:
				{
					if (!preg_match('/^.+$/i',$data))
					{
						$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be STRING.');
					}
					break;
				}
				case self::INT:
				{
					if (!preg_match('/^-?\d+$/',$data))
					{
						$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be INT.');
					}
					break;
				}
				case self::ID:
				{
					if (!preg_match('/^[a-z]\w*$/i',$data))
					{
						$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be ID.');
					}
					break;
				}
				case self::BOOL:
				{
					if (gettype($data)!='boolean' && !preg_match('/^(true|false)$/i',$data))
					{
						$this->parent->error(0,0,'Invalid attribute value "'.$data.'". Should be BOOL.');
					}
					break;
				}
				default:
				{
					$this->parent->error(0,0,'Invalid datatype definition associated with element.');
				}
			}
		}
		return true;
	}
}
?>