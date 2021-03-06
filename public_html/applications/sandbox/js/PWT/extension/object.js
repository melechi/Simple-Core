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
	}
	else if (Object.isArray(source))
	{
		if (!Object.isArray(destination))destination=[];
		for (var i=0,j=source.length; i<j; i++)
		{
			destination[i]=Object.extend(destination[i],source[i]);
		}
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
 * Checks if the given object is a number.
 * @member Object
 * @param {Object} The object to be checked.
 * @return {Boolean}
 */
Object.isNumber=		function(object)
{
	return typeof object=='number';
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
	return (!Object.isArray(object) && !Object.isBoolean(object) && !Object.isElement(object)
			&& !Object.isFunction(object) && !Object.isNumber(object) && !Object.isString(object)
			&& !Object.isUndefined(object) && object!=null);
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
	else if (strict&& /^(\t*)|(\v*)|(\s*)$/.test(object))
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