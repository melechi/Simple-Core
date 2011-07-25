<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class breadcrumbs_crumb
{
	public $parent=			false;
	private $destroyed=		false;
	private $originalText=	'';
	private $text=			'';
	private $url=			'';
	private $first=			false;
	private $last=			false;
	private $class=			'';
	private $template=		'<li style="float:left;clear:none;" class="{CLASS}"><a href="{URL}">{TEXT}</a></li>';

	public function __construct($parent=null,$text='',$url='',$position=false)
	{
		$this->parent=$parent;
		$this->originalText=$text;
		$this->setText($text);
		$this->setURL($url);
		if ($position)
		{
			if ($position=='first' || $position=='last')
			{
				$this->{$position}=true;
			}
			elseif ($position=='first&last' || $position=='last&first')
			{
				$this->first=true;
				$this->last=true;
			}
		}
		return true;
	}

	public function __toString()
	{
		if ($this->isLast())
		{
			$this->template=preg_replace('@<a[^>]*?>.*?</a>@i','{TEXT}',$this->template);
		}
		return str_replace
		(
			array
			(
				'{TEXT}',
				'{URL}',
				'{CLASS}',
			),
			array
			(
				$this->text,
				$this->url,
				$this->class
			),
			$this->template
		);
	}

	public function setDestroyed()
	{
		$this->destroyed=true;
		return $this;
	}

	public function unsetDestroyed()
	{
		$this->destroyed=false;
		return $this;
	}

	public function isDestroyed()
	{
		return $this->destroyed;
	}

	public function getOriginalText()
	{
		return $this->originalText;
	}

	public function setText($text='')
	{
		$this->text=$this->parent->useTextFormat(str_replace('_',' ',$text));
		return $this;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setUrl($url='')
	{
		$this->url=$url;
		return $this;
	}

	public function setFirst()
	{
		$this->first=true;
		return $this;
	}

	public function unsetFirst()
	{
		$this->first=false;
		return $this;
	}

	public function isFirst()
	{
		return $this->first;
	}

	public function setLast()
	{
		$this->last=true;
		return $this;
	}

	public function unsetLast()
	{
		$this->last=false;
		return $this;
	}

	public function isLast()
	{
		return $this->last;
	}

	public function addClass($class='')
	{
		if (!$this->hasClass($class))
		{
			if ($this->class && substr($this->class,-1,1)!=' ')$this->class.=' ';
			$this->class.=$class;
		}
		return $this;
	}

	public function removeClass($class=false)
	{
		if ($class)
		{
			$this->class=str_replace($class,'',$this->class);
		}
		return $this;
	}

	public function hasClass($class=false)
	{
		$return=false;
		if ($class)
		{
			$return=strstr($this->class,$class);
		}
		return $return;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function setTemplate($template='')
	{
		$this->template=$template;
		return $this;
	}

	public function getTemplate()
	{
		return $this->template;
	}
}
?>
