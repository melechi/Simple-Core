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

/**
 * Deeply extends or merges one object into another object.
 * Note that this function will in most cases copy the properties,
 * so references will be lost.
 * @member Object
 * @param {Object} destination Destination variable to extend or merge into.
 * @param {Object} source Source object to extend or merge from.
 * @return {Object} destination
 */
Object.extend=function(destination,source)
{
	if (Object.isAssocArray(source))
	{
		if (destination==null || Object.isUndefined(destination))destination={};
		for (var property in source)
		{
			if (Object.isAssocArray(source))
			{
				if (Object.isUndefined(destination[property])
				|| destination[property]==null
				|| destination[property]==false
				)destination[property]={};
				destination[property]=Object.extend(destination[property],source[property]);
			}
			else if (Object.isArray(source[property]))
			{
				destination[property]=Object.extend(destination[property],source[property]);
			}
			else
			{
				destination[property]=source[property];
			}
		}
		delete property;
	}
	else if (Object.isArray(source))
	{
		if (!Object.isArray(destination))destination=[];
		for (var i=(source.length-1); i>-1; i--)
		{
			destination[i]=Object.extend(destination[i],source[i]);
		}
		delete i;
	}
	else
	{
		destination=source;
	}
	return destination;
};
/**
 * Clones an object, returning a new object containing
 * a copy of everything the source object had.
 * @member Object
 * @param {Object} The source object which will be cloned.
 * @return {Object} The cloned source.
 */
Object.clone=			function(source)
{
	var clone={};
	clone=Object.extend(clone,source);
	return clone;
}
/**
 * Checks if the given object is undefined.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isUndefined=		function(object)
{
	return typeof object=='undefined';
}
/**
 * Checks if the given object is defined.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isDefined=		function(object)
{
	return typeof object!='undefined';
}
/**
 * Checks if the given object is a function.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {BObjectlean}
 */
Object.isFunction=		function(object)
{
	return typeof object=='function';
}
/**
 * Checks if the given object is a string.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {BObjectlean}
 */
Object.isString=		function(object)
{
	return typeof object=='string';
}
/**
 * Checks if the given object is a number type.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isNumber=		function(object)
{
	return typeof object=='number';
}
/**
 * Checks if the given object is numeric.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isNumeric=		function(object)
{
	return object!='' && Object.prototype.toString.call(object)!=='[object Array]' && !isNaN(Number(object));
}
/**
 * Checks if the given object is a DOM element.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isElement=		function(object)
{
	return object && object.nodeType==1;
}
/**
 * Checks if the given object is an array.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 * @credit http://thinkweb2.com/projects/prototype/instanceof-considered-harmful-or-how-to-write-a-robust-isarray/
 */
Object.isArray=			function(object)
{
	return Object.prototype.toString.call(object)==='[object Array]';
}
Object.isActionScriptArray=function(object)
{
	if (typeof object==='undefined' || object===null)return false;
	return Object.isDefined(object.constructor) && object.constructor.toString()==='[class Array]';
}
Object.isActionScriptObject=function(object)
{
	if (typeof object==='undefined' || object===null)return false;
	return typeof object.constructor!=='undefined' && object.constructor.toString()==='[class Object]';
}
/**
 * Checks if the given object is a date.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 * @credit http://thinkweb2.com/projects/prototype/instanceof-considered-harmful-or-how-to-write-a-robust-isarray/
 */
Object.isDate=			function(object)
{
	return Object.prototype.toString.call(object)==='[object Date]';
}
/**
 * Checks if the given object is null.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isNull=			function(object)
{
	return object===null;
}
/**
 * Checks if the given object is an associative array.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isAssocArray=	function(object)
{
	return Object.prototype.toString.call(object)==='[object Object]';
}
/**
 * Checks if the given object is a boolean value.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isBoolean=		function(object)
{
	return typeof object=='boolean';
}
/**
 * Checks if the given object is considered an empty value.
 * 
 * Empty values include:
 * '' - An empty String.
 * ' ' - A string with only one or more spaces.
 * 0 - As an Integer.
 * '0' - As a String.
 * [] - An empty array.
 * null
 * false
 * undefined
 * 
 * In Strict Mode, these values are also included:
 * \t - A tab character.
 * \v - A vertical tab.
 * \s - any white space character, including space, tab, form feed, line feed and other unicode spaces.
 * Also included in strict mode are the previous values, mixed in
 * with the non-strict values (where applicable).
 * @member Object
 * @param {Object} The object to be checked.
 * @param {Boolean} True for strict mode. Defaults to false.
 * @return {Boolean}
 */
Object.isEmpty=		function(object,strict)
{
	if (!object
	|| Object.isUndefined(object)
	|| Object.isNull(object)
	|| (Object.isArray(object) && object.length===0)
	|| (Object.isString(object) && (object.trim()==='' || object==='0'))
	|| (Object.isNumber(object && object===0)))
	{
		return true;
	}
	else if (strict && /^(\t*)|(\v*)|(\s*)$/.test(object))
	{
		return true;
	}
	return false;
}

/**
 * Converts the given object to a URL Encoded query string.
 * @member Object
 * @param {Object} The object to be converted.
 * @return {String} queryString
 */
Object.toQueryString=	function(object)
{
	if (this.isString(object))
	{
		return object;
	}
	var queryString='';
	for (var property in object)
	{
		if (!this.isFunction(object[property]) && typeof object[property]!='object')
		{
			queryString+=encodeURIComponent(property)+'='+encodeURIComponent(object[property])+'&';
		}
	}
	return queryString;
}
Object.toIndexedArray=function(object)
{
	var ret=[];
	for (var item in object)
	{
		ret.push(object[item]);
	}
	return ret;
}
Object.toArray=function(object)
{
	if (Object.isArray(object))return object;
	if (Object.isString(object))
	{
		return object.split('');
	}
	else if (Object.isNumber(object))
	{
		return [object];
	}
	else if (Object.isAssocArray(object) || Object.isActionScriptArray(object) || Object.isActionScriptObject(object))
	{
		if (Object.isIndexed(object))
		{
			return Object.toIndexedArray(object);
		}
		else
		{
			var	ret		=[],
				index	=-1;
			for (var item in object)
			{
				index++;
				if (Object.isNumber(item))
				{
					if (Object.isDefined(ret[index]))
					{
						var next=(index+1);
						ret[next]=ret[index];
						ret[index]=object[item];
						index++;
						continue;
					}
				}
				ret[index]=object[item];
			}
		}
		return ret;
	}
	else
	{
		if (Object.isUndefined(window.nonAssocArrayBullshit))window.nonAssocArray=[];
		window.nonAssocArray.push(object);
	}
	return [object];
}
Object.isIndexed=function(object)
{
	if (Object.isAssocArray(object))
	{
		var oneItem=false;
		for (var item in object)
		{
			oneItem=true;
			if (!Object.isNumeric(item))
			{
				return false;
			}
		}
		return oneItem;
	}
	return false;
}
Object.normalizeArray=function(object,recursive,preserveArrays,normalizeArrays,convertIndexedAssocToArray)
{
	if (Object.isArray(object) && recursive)
	{
		var ret=[];
		for (var i=(object.length-1); i>-1; i--)
//		for (var i=0,j=object.length; i<j; i++)
		{
			if (preserveArrays && (Object.isAssocArray(object[i]) || Object.isActionScriptArray(object[i]) || Object.isActionScriptObject(object[i])))
			{
				if (normalizeArrays)
				{
					ret.push(Object.normalizeAssocArray(object[i],true,true,true,convertIndexedAssocToArray));
				}
				else
				{
					ret.push(object[i]);
				}
			}
			else
			{
				ret.push(Object.normalizeArray(object[i],true));
			}
		}
		return ret;
	}
	else if (Object.isAssocArray(object) || Object.isActionScriptArray(object) || Object.isActionScriptObject(object))
	{
		if (recursive)
		{
			var ret=[];
			for (var key in object)
			{
				if (preserveArrays && (Object.isAssocArray(object[key]) || Object.isActionScriptArray(object[key]) || Object.isActionScriptObject(object[key])))
				{
					if (normalizeArrays)
					{
						ret.push(Object.normalizeAssocArray(object[key],true,true,true,convertIndexedAssocToArray));
					}
					else
					{
						ret.push(object[key]);
					}
				}
				else
				{
					ret.push(Object.normalizeArray(object[key],true));
				}
			}
			return ret;
		}
		else
		{
			var ret=[];
			for (var key in object)
			{
				ret.push(object[key]);
			}
			return ret;
		}
	}
	return object;
}
Object.normalizeAssocArray=function(object,recursive,preserveArrays,normalizeArrays,convertIndexedAssocToArray)
{
	if (Object.isAssocArray(object) && recursive)
	{
		var ret={};
		for (var key in object)
		{
			if (!Object.isFunction(object[key]))
			{
				if (preserveArrays && Object.isArray(object[key]))
				{
					if (normalizeArrays)
					{
						ret[key]=Object.normalizeArray(object[key],true,true,true);
					}
					else
					{
						ret[key]=object[key];
					}
				}
				else
				{
					ret[key]=Object.normalizeAssocArray(object[key],true,preserveArrays,normalizeArrays,convertIndexedAssocToArray);
				}
				if (convertIndexedAssocToArray && Object.isAssocArray(ret[key]) && Object.isIndexed(ret[key]))
				{
					ret[key]=Object.toArray(ret[key]);
				}
			}
		}
		if (convertIndexedAssocToArray && Object.isAssocArray(ret) && Object.isIndexed(ret))
		{
			ret=Object.toArray(ret);
		}
		return ret;
	}
	else if (Object.isArray(object))
	{
		if (recursive)
		{
			var ret={};
			for (var i=(object.length-1); i>-1; i--)
//			for (var i=0,j=object.length; i<j; i++)
			{
				if (preserveArrays && Object.isArray(object[i]))
				{
					if (normalizeArrays)
					{
						ret[i]=Object.normalizeArray(object[i],true,true,true);
					}
					else
					{
						ret[i]=object[i];
					}
				}
				else
				{
					ret[i]=Object.normalizeAssocArray(object[i],true,preserveArrays,normalizeArrays,convertIndexedAssocToArray);
				}
				if (convertIndexedAssocToArray && Object.isAssocArray(ret[i]) && Object.isIndexed(ret[i]))
				{
					ret[i]=Object.toArray(ret[i]);
				}
			}
			if (convertIndexedAssocToArray && Object.isAssocArray(ret) && Object.isIndexed(ret))
			{
				ret=Object.toArray(ret);
			}
			return ret;
		}
		else
		{
			var ret={};
			for (var i=(object.length-1); i>-1; i--)
//			for (var i=0,j=object.length; i<j; i++)
			{
				ret[i]=object[i];
			}
			if (convertIndexedAssocToArray && Object.isIndexed(ret))
			{
				ret=Object.toArray(ret);
			}
			return ret;
		}
	}
	else if (Object.isActionScriptArray(object) || Object.isActionScriptObject(object))
	{
		if (recursive)
		{
			var ret={};
			for (var key in object)
			{
				if (!Object.isFunction(object[key]))
				{
					if (preserveArrays && Object.isArray(object[key]))
					{
						if (normalizeArrays)
						{
							ret[key]=Object.normalizeArray(object[key],true,true,true);
						}
						else
						{
							ret[key]=object[key];
						}
					}
					else
					{
						ret[key]=Object.normalizeAssocArray(object[key],true,preserveArrays,normalizeArrays,convertIndexedAssocToArray);
					}
					if (key=='yRangeReal');
					if (convertIndexedAssocToArray && Object.isAssocArray(ret[key]) && Object.isIndexed(ret[key]))
					{
						ret[key]=Object.toArray(ret[key]);
					}
				}
			}
			if (convertIndexedAssocToArray && Object.isAssocArray(ret) && Object.isIndexed(ret))
			{
				ret=Object.toArray(ret);
			}
			return ret;
		}
		else
		{
			var ret={};
			for (var key in object)
			{
				if (!Object.isFunction(object[key]))
				{
					ret[key]=object[key];
				}
				if (convertIndexedAssocToArray && Object.isAssocArray(ret[key]) && Object.isIndexed(ret[key]))
				{
					ret[key]=Object.toArray(ret[key]);
				}
			}
			if (convertIndexedAssocToArray && Object.isIndexed(ret))
			{
				ret=Object.toArray(ret);
			}
			return ret;
		}
	}
	return object;
}
Object.ifSetOr=function(object,or)
{
	return Object.isDefined(object)?object:or || null;
}