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
	String.prototype,
	{
//		gsub: function(pattern,replacement)
//		{
//			var	result='',
//				source=this,
//				match,
//				replacement=arguments.callee.prepareReplacement(replacement);//TODO: figure out dependencies (Template)
//			while (source.length>0)
//			{
//				if (match = source.match(pattern))
//				{
//					result+=source.slice(0,match.index);
//					result+=String.interpret(replacement(match));
//					source =source.slice(match.index+match[0].length);
//				}
//				else
//				{
//					result+=source,source='';
//				}
//			}
//			return result;
//		},
//		inspect: function(useDoubleQuotes)
//		{
//			var escapedString=this.gsub
//			(
//				/[\x00-\x1f\\]/,
//				function(match)
//				{
//					var character=String.specialChar[match[0]];
//					return character?character:'\\u00'+match[0].charCodeAt().toPaddedString(2,16);
//				}
//			);
//			if (useDoubleQuotes) return '"'+escapedString.replace(/"/g,'\\"')+'"';
//			return "'"+escapedString.replace(/'/g,'\\\'')+"'";
//		},
//		toJSON: function()
//		{
//			return this.inspect(true);
//		},
//		isJSON: function()
//		{
//			var str=this;
//			if (str.blank())return false;
//			str=this.replace(/\\./g,'@').replace(/"[^"\\\n\r]*"/g,'');
//			return (/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(str);
//		},
//		evalJSON: function(sanitize)
//		{
//			//var json=this.unfilterJSON();
//			var json=this;
//			try
//			{
//				if (!sanitize || json.isJSON())return eval('('+json+')');
//			}
//			catch(e){}
//			throw new SyntaxError('Badly formed JSON string: '+this.inspect());
//		},
		/**
		 * Replaces all new line characters with HTML br tags.
		 * 
		 * @return {String} Formatted string.
		 */
		nl2br: function()
		{
			return this.replace(/[\r\n\f]/g,'<br />');
		},
		/**
		 * Trims whitespace from each end of a string.
		 * @return {String} Trimmed string.
		 * 
		 * @credit http://blog.stevenlevithan.com/archives/faster-trim-javascript
		 */
		trim: function()
		{
			var	str	=this.replace(/^\s\s*/,''),
				ws	=/\s/,
				i	=str.length;
			while (ws.test(str.charAt(--i)));
			return str.slice(0,i+1);
		},
		/**
		 * Parses a URI-like query string and returns an object representation of it.
		 * 
		 * This method is realy targeted at parsing query strings (hence the default value of "&" for the separator argument).
		 * 
		 * For this reason, it does not consider anything that is either before a question mark (which signals the beginning
		 * of a query string) or beyond the hash symbol ("#"), and runs decodeURIComponent() on each parameter/value pair.
		 * 
		 * String#toQueryParams also aggregates the values of identical keys into an array of values.
		 * 
		 * Note that parameters which do not have a specified value will be set to undefined.
		 * 
		 * @param {String} separator
		 * @returns {Object} Object composed of key/val pairs.
		 * 
		 * @credit http://www.prototypejs.org/api/string/toQueryParams
		 */
		toQueryParams: function(separator)
		{
			var match=this.trim().match(/([^?#]*)(#.*)?$/);
			if (!match)return {};
			return match[1].split(separator || '&').inject
			(
				{},
				function(hash,pair)
				{
					if ((pair=pair.split('='))[0])
					{
						var key		=decodeURIComponent(pair.shift());
						var value	=pair.length>1?pair.join('='):pair[0];
						if (!Object.isUndefined(value))value=decodeURIComponent(value);
						if (key in hash)
						{
							if (!Object.isArray(hash[key]))hash[key]=[hash[key]];
							hash[key].push(value);
						}
						else
						{
							hash[key]=value;
						}
					}
					return hash;
				}
			);
		},
		/**
		 * Truncates a string to a given length and adds an ellipse(...) to the end of it.
		 * 
		 * @param {Number} maxLength Specifies the point at which the string should be truncated.
		 * @return {String} The truncated string.
		 */
		ellipse: function(maxLength)
		{
			if(this.length>maxLength)
			{
				return this.substr(0,maxLength-3)+'...';
			}
			return this;
		},
		/**
		 * Returns a formatted string.
		 * 
		 * @return {String} Formatted string.
		 * 
		 * @credit http://phpjs.org/functions/sprintf:522
		 */
		sprintf: function()
		{
			// Return a formatted string  
			// 
			// version: 908.406
			// discuss at: http://phpjs.org/functions/sprintf
			// +   original by: Ash Searle (http://hexmen.com/blog/)
			// + namespaced by: Michael White (http://getsprink.com)
			// +	tweaked by: Jack
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +	  input by: Paulo Ricardo F. Santos
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +	  input by: Brett Zamir (http://brett-zamir.me)
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// *	 example 1: sprintf("%01.2f", 123.1);
			// *	 returns 1: 123.10
			// *	 example 2: sprintf("[%10s]", 'monkey');
			// *	 returns 2: '[	monkey]'
			// *	 example 3: sprintf("[%'#10s]", 'monkey');
			// *	 returns 3: '[####monkey]'
			var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
			if (Object.isString(this))
			{
				var a = arguments, i = 0, format = this;
			}
			else
			{
				var a = arguments, i = 0, format = a[i++];
			}
			
		
			// pad()
			var pad = function (str, len, chr, leftJustify) {
				if (!chr) {chr = ' ';}
				var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
				return leftJustify ? str + padding : padding + str;
			};
		
			// justify()
			var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
				var diff = minWidth - value.length;
				if (diff > 0) {
					if (leftJustify || !zeroPad) {
						value = pad(value, minWidth, customPadChar, leftJustify);
					} else {
						value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
					}
				}
				return value;
			};
		
			// formatBaseX()
			var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
				// Note: casts negative numbers to positive ones
				var number = value >>> 0;
				prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
				value = prefix + pad(number.toString(base), precision || 0, '0', false);
				return justify(value, prefix, leftJustify, minWidth, zeroPad);
			};
		
			// formatString()
			var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
				if (precision != null) {
					value = value.slice(0, precision);
				}
				return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
			};
		
			// doFormat()
			var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
				var number;
				var prefix;
				var method;
				var textTransform;
				var value;
		
				if (substring == '%%') {return '%';}
		
				// parse flags
				var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
				var flagsl = flags.length;
				for (var j = 0; flags && j < flagsl; j++) {
					switch (flags.charAt(j)) {
						case ' ': positivePrefix = ' '; break;
						case '+': positivePrefix = '+'; break;
						case '-': leftJustify = true; break;
						case "'": customPadChar = flags.charAt(j+1); break;
						case '0': zeroPad = true; break;
						case '#': prefixBaseX = true; break;
					}
				}
		
				// parameters may be null, undefined, empty-string or real valued
				// we want to ignore null, undefined and empty-string values
				if (!minWidth) {
					minWidth = 0;
				} else if (minWidth == '*') {
					minWidth = +a[i++];
				} else if (minWidth.charAt(0) == '*') {
					minWidth = +a[minWidth.slice(1, -1)];
				} else {
					minWidth = +minWidth;
				}
		
				// Note: undocumented perl feature:
				if (minWidth < 0) {
					minWidth = -minWidth;
					leftJustify = true;
				}
		
				if (!isFinite(minWidth)) {
					throw new Error('sprintf: (minimum-)width must be finite');
				}
		
				if (!precision) {
					precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
				} else if (precision == '*') {
					precision = +a[i++];
				} else if (precision.charAt(0) == '*') {
					precision = +a[precision.slice(1, -1)];
				} else {
					precision = +precision;
				}
		
				// grab value using valueIndex if required?
				value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];
		
				switch (type) {
					case 's': return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
					case 'c': return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
					case 'b': return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
					case 'o': return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
					case 'x': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
					case 'X': return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
					case 'u': return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
					case 'i':
					case 'd':
						number = parseInt(+value, 10);
						prefix = number < 0 ? '-' : positivePrefix;
						value = prefix + pad(String(Math.abs(number)), precision, '0', false);
						return justify(value, prefix, leftJustify, minWidth, zeroPad);
					case 'e':
					case 'E':
					case 'f':
					case 'F':
					case 'g':
					case 'G':
						number = +value;
						prefix = number < 0 ? '-' : positivePrefix;
						method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
						textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
						value = prefix + Math.abs(number)[method](precision);
						return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
					default: return substring;
				}
			};
			return format.replace(regex, doFormat);
		},
//		/**
//		 * Returns a formatted string.
//		 * 
//		 * @return {String} Formatted string.
//		 * 
//		 * @credit http://phpjs.org/functions/printf:494
//		 */
//		printf: function()
//		{
//			// Output a formatted string  
//			// 
//			// version: 908.406
//			// discuss at: http://phpjs.org/functions/printf
//			// +   original by: Ash Searle (http://hexmen.com/blog/)
//			// +   improved by: Michael White (http://getsprink.com)
//			// +   improved by: Brett Zamir (http://brett-zamir.me)
//			// -	depends on: sprintf
//			// *	 example 1: printf("%01.2f", 123.1);
//			// *	 returns 1: 6
//			var body, elmt, d = this.window.document;
//			var ret = '';
//			
//			var HTMLNS = 'http://www.w3.org/1999/xhtml';
//			body = d.getElementsByTagNameNS ?
//			  (d.getElementsByTagNameNS(HTMLNS, 'body')[0] ?
//				d.getElementsByTagNameNS(HTMLNS, 'body')[0] :
//				d.documentElement.lastChild) :
//			  d.getElementsByTagName('body')[0];
//		
//			if (!body) {
//				return false;
//			}
//			
//			ret = this.sprintf.apply(this, arguments);
//		
//			elmt = d.createTextNode(ret);
//			body.appendChild(elmt);
//			
//			return ret.length;
//		}
	}
);