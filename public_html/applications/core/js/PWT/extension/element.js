/*
 * Petim Web Tools v1.0
 * Copyright(c) 2008, Petim Pty. Ltd.
 * licensing@petim.com.au
 * 
 * See packaged license.txt
 * OR URL:
 * http://www.petim.com.au/products/pwt/license/view/
 * for full license terms.
 */

Object.extend
(
	Element.prototype,
	{
		/**
		 * Inserts content into any position around the element.
		 * Valid positions are top, bottom, before and after.
		 * 
		 * @param {Element, String} [content] Can be a actual DOM Element or a string
		 * representation of a valid HTML block.
		 * @param {String} A valid position to insert content (top, bottom, before, after). (defaults to bottom)
		 * @return {Element} this
		 * 
		 * @credit Parts of this was taken from Prototype's insert function.
		 * http://www.prototypejs.org/api/element/insert
		 */
		insert: function(content,position)
		{
			var tags=
			{
				TABLE:	['<table>',                '</table>',                   1],
				TBODY:	['<table><tbody>',         '</tbody></table>',           2],
				TR:		['<table><tbody><tr>',     '</tr></tbody></table>',      3],
				TD:		['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
				SELECT:	['<select>',               '</select>',                  1]
			}
			if (Object.isUndefined(position))position='bottom';
			position=position.toLowerCase();
			if (!['before','after','top','bottom'].inArray(position))
			{
				position='bottom';
			}
			var realInsert=function(){};
			switch (position)
			{
				case 'before':
				{
					realInsert=function(element,node)
					{
						element.parentNode.insertBefore(node,element);
					}
					break;
				}
				case 'after':
				{
					realInsert=function(element,node)
					{
						element.parentNode.insertBefore(node,element.nextSibling);
					}
					break;
				}
				case 'top':
				{
					realInsert=function(element,node)
					{
						element.insertBefore(node,element.firstChild);
					}
					break;
				}
				case 'bottom':
				{
					realInsert=function(element,node)
					{
						element.appendChild(node);
					}
					break;
				}
			}
			if (Object.isElement(content))
			{
				realInsert(this,content);
				return;
			}
			var tagName=((position=='before' || position=='after')?this.parentNode:this).tagName.toUpperCase();
			var tempDiv=document.createElement('div');
			var tag=tags[tagName];
			if (tag)
			{//TODO: test this further.
				tempDiv.innerHTML=tag[0]+content+tag[1];
				var repeat=function(){tempDiv=tempDiv.firstChild};
				for (var i=0; i<t[2]; i++)repeat();
			}
			else
			{
				tempDiv.innerHTML=content;
			}
			var childNodes=$A(tempDiv.childNodes);
			delete tempDiv;
			if (position=='top' || position=='after')childNodes.reverse();
			childNodes.each(realInsert.curry(this));
			return this;
		},
		/**
		 * Replaces the child elements within the element with the given content.
		 * 
		 * @param {Element, String} [content] Can be a actual DOM Element or a string
		 * representation of a valid HTML block.
		 * @return {Element} this
		 */
		update: function(content)
		{
			if (Object.isElement(content))
			{
				$A(this.childNodes).each
				(
					function(node)
					{
						this.removeChild(node);
					}.bind(this)
				);
				this.appendChild(content);
			}
			else
			{
				this.innerHTML=content;
			}
			return this;
		},
		/**
		 * Replaces the element and all other child elements with the given content.
		 * 
		 * @param {Element, String} [content] Can be a actual DOM Element or a string
		 * representation of a valid HTML block.
		 * @return {Element} this.parentNode
		 */
		replace: function(content)
		{
			$return=this.parentNode;
			if (Object.isElement(content))
			{
				this.parentNode.replaceChild(content,this);
			}
			else
			{
				this.parentNode.innerHTML=content;
			}
			return $return;
		},
		/**
		 * Removes the element from the DOM and returns its parent.
		 * 
		 * @return {Element} this.parentNode
		 */
		remove: function()
		{
			$return=this.parentNode;
			try{$return.removeChild(this);}catch(e){}
			return $return;
		},
		/**
		 * Shows an element by setting its CSS display property to ''.
		 * 
		 * @return {Element} this
		 */
		show: function()
		{
			this.style.display='';
			return this;
		},
		/**
		 * Hides an element by setting its CSS display property to 'none'.
		 * 
		 * @return {Element} this
		 */
		hide: function()
		{
			this.style.display='none';
			return this;
		},
		/**
		 * Checks if an element is visible based on it's css properties.
		 * 
		 * @param {Boolean} strict If the check should include parent objects and classes.
		 * @return {Boolean} true if it is visible, otherwise false.
		 * @todo: strict mode.
		 */
		isVisible: function(strict)
		{
			if ((this.getStyle('display')=='none' || window.getComputedStyle(this,'').display=='none')
			|| (this.getStyle('visibility')=='hidden' || window.getComputedStyle(this,'').visibility=='hidden'))
			{
				return false;
			}
			return true;
		},
		/**
		 * Checks if a given class name is currently set on an element.
		 * 
		 * @param {String} className The name of the class to check.
		 * @return {Boolean} True if element has className.
		 */
		hasClassName: function(className)
		{
			return this.className.split(' ').inArray(className);
		},
		/**
		 * Adds a class to an element if the class doesn't already exist on the element.
		 * 
		 * @param {String} className The name of the class to add to the element.
		 * @return {Element} this
		 */
		addClassName: function(className)
		{
			if (!this.hasClassName(className))
			{
				this.className+=' '+className;
			}
			return this;
		},
		/**
		 * Removes a class name from an element if the class already exists on the element.
		 * 
		 * @param {String} className The name of the class to remove from the element.
		 * @return {Element} this
		 */
		removeClassName: function(className)
		{
			var newClassDef=[];
			this.className.split(' ').each
			(
				function(thisClassName)
				{
					if (thisClassName!=className)
					{
						newClassDef.push(thisClassName);
					}
				}
			);
			this.className=newClassDef.join(' ');
			return this;
		},
		/**
		 * Toggles a class name on an element by adding or removing it.
		 * If the class already exists on the element, then it will be removed.
		 * If the class doesn't already exist on the element, then it will be added.
		 * 
		 * @param {String} className The class to be toggled.
		 * @return {Element} this
		 */
		toggleClassName: function(className)
		{
			if (this.hasClassName(className))
			{
				this.removeClassName(className);
			}
			else
			{
				this.addClassName(className);
			}
		},
		/**
		 * Will swap class names by finding classNameA and replacing
		 * it with classNameB.
		 * 
		 * @param {String} classNameA Class name to find.
		 * @param {String} classNameB Class name to replace with.
		 * @return {Element} this
		 */
		swapClassNames: function(classNameA,classNameB)
		{
			return this.removeClassName(classNameA).addClassName(classNameB);
		},
		/**
		 * Shorthand for Element.querySelector which performs a CSS query
		 * on the element and returns the first resulting element based on
		 * that query.
		 * 
		 * @param {String} query The CSS query to be executed.
		 * @return {Mixed} Returns the result of the query selector.
		 * @see Element#querySelector
		 */
		select: function(query)
		{
			return this.querySelector(query);
		},
		/**
		 * Same as select() except it returns more than one element.
		 * 
		 * @param {String} query The CSS query to be executed.
		 * @param {Boolean} nodeList If true, will return a standard
		 * DOM NodeList instead of an array (defaults to false).
		 * @return {Mixed} Returns the result of the query selector.
		 * @see Element#querySelector
		 */
		selectAll: function(query,nodeList)
		{
			if (nodeList)
			{
				if (Object.isFunction(this.querySelectorAll))
				{
					return this.querySelectorAll(query);
				}
				//Support for ExtJS's query method.
				else if (!Object.isUndefined(Ext))
				{
					return new Ext.Element(this).query(query);
				}
			}
			else
			{
				if (Object.isFunction(this.querySelectorAll))
				{
					return $A(this.querySelectorAll(query));
				}
				//Support for ExtJS's query method.
				else if (!Object.isUndefined(Ext))
				{
					return $A(new Ext.Element(this).query(query));
				}
			}
		},
		/**
		 * Sets style properties on an element.
		 * 
		 * @param {Object, String} [style] A valid CSS string or object with valid Camel Cased key value pairs.
		 * @return {Element} this
		 */
		setStyle: function(style)
		{
			if (Object.isString(style))
			{
				this.style.cssText+=';'+style;
			}
			else
			{
				for (var property in style)
				{
					this.style[property]=style[property];
				}
			}
			return this;
		},
		/**
		 * Fetches the value of a given style from an element.
		 * 
		 * @param {String} style The stype to fetch.
		 * @return {String} Value of style.
		 */
		getStyle: function(style)
		{
			if (style.indexOf('-')>-1)
			{
				var thisStyle='';
				for (var i=0,j=style.length; i<j; i++)
				{
					if (style.charAt(i)=='-')
					{
						i++;
						thisStyle+=style.charAt(i).toUpperCase();
					}
					else
					{
						thisStyle+=style.charAt(i);
					}
				}
				return this.style[thisStyle];
			}
			else
			{
				return this.style[style];
			}
		},
		cleanWhitespace: function()
		{
			var node=this.firstChild;
			while (node)
			{
				var nextNode=node.nextSibling;
				if (node.nodeType==3 && !/\S/.test(node.nodeValue))
				this.removeChild(node);
				node=nextNode;
			}
//					for (var i=0,j=this.childNodes.length; i<j; i++)
//					{
//						if (this.childNodes[i].nodeType==3 && !/\S/.test(this.childNodes[i].nodeValue))
//						{console.log(this.childNodes[i],this.childNodes[i].nodeType,this.childNodes[i].nodeValue);
//							//this.childNodes[i].remove();
//							this.removeChild(this.childNodes[i]);
//						}
//					}
//					delete i,j;
			return this;
		},
		next: function()
		{
			this.parentNode.cleanWhitespace();
			if (arguments.length==1)
			{
				var arg=$A(arguments).first();
				if (Object.isNumber(arg))
				{
					var element=this.nextSibling;
					for (var i=0; i<arg; i++)
					{
						element=element.nextSibling;
					}
					delete i;
					return element;
				}
				else
				{
					
//							return this.select(arg);
				}
			}
//					else if (arguments.length==2)
//					{
//						var args=$A(arguments);
//						return this.selectAll(args[0])[args[1]];
//					}
			else
			{
				return this.nextSibling;
			}
		},
		previous: function()
		{
			this.parentNode.cleanWhitespace();
			if (arguments.length==1)
			{
				var arg=$A(arguments).first();
				if (Object.isNumber(arg))
				{
					var element=this.previousSibling;
					for (var i=0; i<arg; i++)
					{
						element=element.previousSibling;
					}
					delete i;
					return element;
				}
			}
			else
			{
				return this.previousSibling;
			}
		},
		down: function()
		{
			this.cleanWhitespace();
			if (arguments.length==1)
			{
				var arg=$A(arguments).first();
				if (Object.isNumber(arg))
				{
					var element=this.firstChild;
					for (var i=0; i<arg; i++)
					{
						element=element.firstChild;
					}
					delete i;
					return element;
				}
				else
				{
					return this.select(arg);
				}
			}
			else if (arguments.length==2)
			{
				var args=$A(arguments);
				return this.selectAll(args[0])[args[1]];
			}
			else
			{
				return this.firstChild;
			}
		},
		up: function()
		{
			try
			{
				this.parentNode.cleanWhitespace();
			}
			catch(e)
			{
				try
				{
					this.parentElement.cleanWhitespace();
				}
				catch(e){};
			}
			if (arguments.length==1)
			{
				var arg=$A(arguments).first();
				if (Object.isNumber(arg))
				{
					var element=this;
					for (var i=0; i<arg; i++)
					{
						element=element.parentNode;
					}
					delete i;
					return element;
				}
				else
				{//TODO
					//return this.selectAll(arg)
				}
			}
			else
			{
				return this.parentNode;
			}
		}
	}
);