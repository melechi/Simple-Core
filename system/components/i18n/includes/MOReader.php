<?php
class MOReader
{
	
	
	private $magicNumbers=array
	(
		0x950412de,	//Big Endian
		0xde120495	//Little Endian
	);
	private $file				=null;
	private $byteOrder			=null;
	private $charset			=null;
	private $stream				=null;
	private $version			=null;
	private $numStrings			=null;
	private $stringTable		=null;
	private $translationTable	=null;
	private $strings			=null;
	private $headers			=array();
	private $pluralNumber		=null;
	private $pluralFormat		=null;
	
	public function __construct($file,$charset)
	{
		$this->file		=fopen($file,'rb');
		$this->charset	=$charset;
		//Type cast magic numbers to STRINGS - weird eh? :)
		settype($this->magicNumbers[0],'string');
		settype($this->magicNumbers[1],'string');
		
		$this	->getByteOrder()
				->getVersion()
				->extractStrings()
				->extractHeaders()
				->extractPluralFormat();
	}
	
	public function __destruct()
	{
		@fclose($this->file);
	}
	
	public function getStringDef($string)
	{
		if (isset($this->strings[$string]))
		{
			if (($thisEncoding=mb_detect_encoding($this->strings[$string]['translation']))!=$this->charset)
			{
				$this->strings[$string]['translation']=mb_convert_encoding($this->strings[$string]['translation'],$this->charset,$thisEncoding);
				if (!is_null($this->strings[$string]['pluralTranslation']))
				{
					$this->strings[$string]['pluralTranslation']=mb_convert_encoding($this->strings[$string]['pluralTranslation'],$this->charset,$thisEncoding);
				}
			}
			return $this->strings[$string];
		}
		else
		{
			throw new Exception('MOReader Error. String not found. String: "'.$string.'".');
		}
	}
	
	public function getPluralNumber()
	{
		if (is_null($this->pluralNumber))$this->extractPluralFormat();
		return $this->pluralNumber;
	}
	
	public function getPluralFormat()
	{
		if (is_null($this->pluralFormat))$this->extractPluralFormat();
		return $this->pluralFormat;
	}
	
	private function getByteOrder()
	{
		$magicNumber=sprintf('%u',$this->unpack(0));
		if ($magicNumber===$this->magicNumbers[0])//Big Endian
		{
			$this->byteOrder=1;
		}
		else if ($magicNumber===$this->magicNumbers[1])//Little Endian
		{
			$this->byteOrder=0;
		}
		else
		{
			throw new Exception('Invalid MO file.');
		}
		return $this;
	}
	
	private function getVersion()
	{
		$this->version=$this->unpack(4);
		return $this;
	}
	
	private function extractStrings()
	{
		//Get the number of strings.
		$this->numStrings=$this->unpack(8);
		
		//Get the table positions.
		$table=array
		(
			'original'		=>$this->unpack(12),
			'translation'	=>$this->unpack(16)
		);
		
		//Pull out the original and translation positions.
		$this->stringTable		=$this->unpack($table['original'],$this->numStrings*2);
		$this->translationTable	=$this->unpack($table['translation'],$this->numStrings*2);
		
		//Remove the first items as they're always blank.
		array_shift($this->stringTable);
		array_shift($this->stringTable);
		array_shift($this->translationTable);
		array_shift($this->translationTable);
		
		$length=array(0,0);
		$defaultString=array
		(
			'normal'			=>null,
			'plural'			=>null,
			'translation'		=>null,
			'pluralTranslation'	=>null
		);
		for ($i=0,$j=count($this->stringTable); $i<$j; $i++)
		{
			//Position
			if ($i%2)
			{
				$thisString['normal']=$this->extractString($this->stringTable[$i],$length[0]);
				$plural=explode("\0",$thisString['normal']);
				if (isset($plural[1]))
				{
					$thisString['normal']=$plural[0];
					$thisString['plural']=$plural[1];
				}
				$thisString['translation']=$this->extractString($this->translationTable[$i],$length[1]);
				$plural=explode("\0",$thisString['translation']);
				if (isset($plural[1]))
				{
					$thisString['translation']=$plural[0];
					$thisString['pluralTranslation']=$plural[1];
				}
				$this->strings[$thisString['normal']]=$thisString;
			}
			//Length
			else
			{
				$length[0]		=$this->stringTable[$i];
				$length[1]		=$this->translationTable[$i];
				$thisString		=$defaultString;
			}
		}
		return $this;
	}
	
	private function extractHeaders()
	{
		$count	=count($this->stringTable);
		$start	=($this->stringTable[($count-1)]+$this->stringTable[($count-2)]+1);
		$end	=$this->translationTable[1]-3;
		
		fseek($this->file,$start);
		$data	=explode("\n",fread($this->file,($end-$start)));
		for ($i=0,$j=count($data); $i<$j; $i++)
		{
			$split=explode(':',$data[$i]);
			$this->headers[trim($split[0])]=trim($split[1]);
		}
		return $this;
	}
	
	private function extractPluralFormat()
	{
		if (!count($this->headers))$this->extractHeaders();
		if (!empty($this->headers['Plural-Forms']))
		{
			$split=explode(';',$this->headers['Plural-Forms']);
			$this->pluralNumber=(int)trim(str_replace('nplurals=','',$split[0]));
			$this->pluralFormat=trim(str_replace(array('plural=','n'),array('','$n'),$split[1]));
		}
		return $this;
	}
	
	private function unpack($from,$length=0)
	{
		//Big Endian
		if ($this->byteOrder===1 || is_null($this->byteOrder))
		{
			if ($length===0)
			{
				return array_shift(unpack('N',$this->read($from)));
			}
			else
			{
				return unpack('N'.$length,$this->read($from,4*$length));
			}
		}
		//Little Endian
		else if ($this->byteOrder===0)
		{
			if ($length===0)
			{
				return array_shift(unpack('V',$this->read($from)));
			}
			else
			{
				return unpack('V'.$length,$this->read($from,4*$length));
			}
		}
	}
	
	private function extractString($from,$length)
	{
		fseek($this->file,$from);
		$data=fread($this->file,$length);
		return (string)$data;
	}
	
	private function read($from,$length=4)
	{
		fseek($this->file,$from);
		return fread($this->file,$length);
	}
	
}
?>