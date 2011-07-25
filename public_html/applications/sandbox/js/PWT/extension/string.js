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
		}
	}
);