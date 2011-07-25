<?php
/*
 * Simple Core 2.0.0
 * Copyright(c) 2004-2008, Simple Site Solutions Pty. Ltd.
 * 
 * http://www.s3core.com/SCPL
 */
class component_breadcrumbs extends component
{
	const EXCEPTION_NAME='Ext Component Exception';

	private $breadcrumbs=		array();
	private $homeText=			'Home';
	private $template=			'<ul style="list-style:none;" class="{CLASS}">{BREADCRUMBS}</ul>';
	private $separator=			'&raquo;';
	private $separatorTemplate=	'<li style="float:left;" class="separator">{SEPARATOR}</li>';
	private $class=				'breadcrumbs';
	private $textFormat=		'ucwords';

	public function initiate()
	{
		$this->xInclude('crumb');
		return true;
	}

	public function __toString()
	{
		$breadcrumbs='';
		for ($i=0,$j=count($this->breadcrumbs); $i<$j; $i++)
		{
			if ($i)$breadcrumbs.=$this->getCompiledSeparator();
			if ($this->breadcrumbs[$i]->isFirst())
			{
				$this->breadcrumbs[$i]->addClass('first');
			}
			if ($this->breadcrumbs[$i]->isLast())
			{
				$this->breadcrumbs[$i]->addClass('last');
			}
			$breadcrumbs.=(string)$this->breadcrumbs[$i];
		}
		return str_replace(array('{BREADCRUMBS}','{CLASS}'),array($breadcrumbs,$this->class),$this->template);
	}

	public function generateCrumbs()
	{
		if ($this->numNodes()===1)
		{
			if ($this->homeAddress())
			{
				$this->breadcrumbs[]=new breadcrumbs_crumb($this,$this->homeText,$this->url('home'),'first&last');
			}
			else
			{
				$this->breadcrumbs[]=new breadcrumbs_crumb($this,$this->homeText,$this->url('home'),'first');
				$this->breadcrumbs[]=new breadcrumbs_crumb($this,$this->firstNode(),$this->url($this->firstNode()),'last');
			}
		}
		else
		{
			$URL='/';
			$this->breadcrumbs[]=new breadcrumbs_crumb($this,$this->homeText,$this->url('home'),'first');
			for ($i=0,$j=$this->numNodes(); $i<$j; $i++)
			{
				$URL.=$this->node($i).'/';
				$this->breadcrumbs[]=new breadcrumbs_crumb($this,$this->node($i),$this->url($URL));
			}
			end($this->breadcrumbs)->setLast();
		}
		return $this;
	}
	
	public function appendBreadcrumb($name=null,$url=null)
	{
		$return=false;
		if (!empty($name))
		{
			$this->breadcrumbs[]=new breadcrumbs_crumb($this,$name,$url,'last');
			$return=end($this->breadcrumbs);
		}
		return $return;
	}
	
	public function regenerateBreadcrumbs()
	{
		$this->breadcrumbs=array();
		$this->generateCrumbs();
	}
	
	public function setTextFormat($format=null)
	{
		if (function_exists($format))
		{
			$this->textFormat=$format;
			$this->regenerateBreadcrumbs();
		}
		return $this;
	}
	
	public function getTextFormat()
	{
		return $this->textFormat;
	}
	
	public function useTextFormat($arg=null)
	{
		return call_user_func($this->textFormat,$arg);
	}

	private function url($url)
	{
		return $this->makeURL($url);
	}

	public function setHomeText($text='')
	{
		$this->homeText=$text;
		return $this;
	}

	public function getHomeText()
	{
		return $this->homeText;
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
	
	public function setSeparator($separator='')
	{
		$this->separator=$separator;
		return $this;
	}

	public function getSeparator()
	{
		return $this->separator;
	}

	public function getCompiledSeparator()
	{
		return str_replace('{SEPARATOR}',$this->separator,$this->separatorTemplate);
	}

	public function setSeparatorTemplate($template='')
	{
		$this->separatorTemplate=$template;
		return $this;
	}

	public function getSeparatorTemplate()
	{
		return $this->separatorTemplate;
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

	public function getBreadcrumbByIndex($index=0)
	{
		$return=false;
		if (isset($this->breadcrumbs[$index]))
		{
			$return=$this->breadcrumbs[$index];
		}
		return $return;
	}

	public function getBreadcrumbByNodeText($text=null)
	{
		$return=false;
		if (!empty($text))
		{
			for ($i=0,$j=count($this->breadcrumbs); $i<$j; $i++)
			{
				if ($this->breadcrumbs[$i]->getOriginalText()==$text)
				{
					$return=$this->breadcrumbs[$i];
					break;
				}
			}
		}
		return $return;
	}

	public function addClassToAllBreacrumbs($class=null)
	{
		$return=false;
		if (!empty($class))
		{
			for ($i=0,$j=count($this->breadcrumbs); $i<$j; $i++)
			{
				$this->breadcrumbs[$i]->addClass($class);
			}
		}
		return $return;
	}

	public function removeClassFromAllBreadcrumbs($class=null)
	{
		$return=false;
		if (!empty($class))
		{
			for ($i=0,$j=count($this->breadcrumbs); $i<$j; $i++)
			{
				$this->breadcrumbs[$i]->removeClass($class);
			}
		}
		return $return;
	}
	
	public function removeLastBreadcrumb()
	{
		$index=(count($this->breadcrumbs)-1);
		unset($this->breadcrumbs[$index]);
		$this->breadcrumbs[($index-1)]->setLast();
		return $this;
	}
	
	public function removeFirstBreadcrumb()
	{
		unset($this->breadcrumbs[0]);
		for ($i=1,$j=count($this->breadcrumbs); $i<=$j; $i++)
		{
			$this->breadcrumbs[($i-1)]=$this->breadcrumbs[$i];
		}
		$this->breadcrumbs[0]->setFirst();
		$this->removeLastBreadcrumb();
		return $this;
	}

//	public function destroyBreadcrumbByIndex($index=0)
//	{
//		$return=false;
//		if (isset($this->breadcrumbs[$index]))
//		{
//			$this->breadcrumbs[$index]->setDestroyed();
//		}
//		return $return;
//	}
//
//	public function destroyBreadcrumbByNodeText($text=null)
//	{
//		$return=false;
//		if (!empty($text))
//		{
//			$breadcrumb=$this->getBreadcrumbByNodeText($text);
//			if ($breadcrumb instanceof breadcrumbs_crumb)$breadcrumb->setDestroyed();
//		}
//		return $return;
//	}
//
//	public function destroyAllBreadcrumbs()
//	{
//		for ($i=0,$j=count($this->breadcrumbs); $i<$j; $i++)
//		{
//			$this->breadcrumbs[$i]->setDestroyed();
//		}
//		return true;
//	}
}
?>
