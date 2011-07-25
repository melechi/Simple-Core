<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class ext_json extends branch
{
	private $_var=array();
	private $encoded='{}';
	
	public function __set($theVar,$theValue)
	{
		if ($theVar!='parent')
		{
			$this->_var[$theVar]=$theValue;
		}
		else
		{
			$this->{$theVar}=$theValue;
		}
		return true;
	}
	
	public function encode()
	{
		$this->encoded=@json_encode($this->_var);
		return $this->encoded;
	}
	
	public function flush()
	{
		$this->encode();
		header('Content-Type:application/json');
		header('Cache-Control:no-cache');
		die($this->encoded);
	}
	
	/**
	 * JSON FORMAT
	 * Code taken from php.net PHP manual.
	 */
	public function format($json)
	{
	    $tab = "  ";
	    $new_json = "";
	    $indent_level = 0;
	    $in_string = false;
	
	    $json_obj = json_decode($json);
	
	    if($json_obj === false)
	        return false;
	
	    $json = json_encode($json_obj);
	    $len = strlen($json);
	
	    for($c = 0; $c < $len; $c++)
	    {
	        $char = $json[$c];
	        switch($char)
	        {
	            case '{':
	            case '[':
	                if(!$in_string)
	                {
	                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
	                    $indent_level++;
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case '}':
	            case ']':
	                if(!$in_string)
	                {
	                    $indent_level--;
	                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case ',':
	                if(!$in_string)
	                {
	                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case ':':
	                if(!$in_string)
	                {
	                    $new_json .= ": ";
	                }
	                else
	                {
	                    $new_json .= $char;
	                }
	                break;
	            case '"':
	                if($c > 0 && $json[$c-1] != '\\')
	                {
	                    $in_string = !$in_string;
	                }
	            default:
	                $new_json .= $char;
	                break;                   
	        }
	    }
	
	    return $new_json;
	}
}
?>