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

var $break={};
Object.extend
(
	Array.prototype,
	{
		/**
		 * @private
		 */
		_each: function(iterator)
		{
			for (var i=0,length=this.length; i<length; i++)iterator(this[i]);
		},
		/**
		 * Pass in a function which will act as the iterator.
		 * 
		 * @param {Function} iterator The interator method.
		 * @!param {Object} context 
		 * @return {Array}
		 */
		each: function(iterator)//,context)
		{
			var index=0;
//			iterator=iterator.bind(context);
			try
			{
				this._each
				(
					function(value)
					{
						iterator(value,index++);
					}
				);
			}
			catch(e)
			{
				if (e!=$break)throw e;
			}
			return this;
		},
		/**
		 * Iterates over an array and returns the first item which returns true.
		 * 
		 * @param {Function} iterator The interator function which will be called for each iteration over the array.
		 * @return {Mixed} The found item in the array.
		 */
		find: function(iterator)//,context)
		{
//			iterator=iterator.bind(context);
			var result;
			this.each
			(
				function(value,index)
				{
					if (iterator(value,index))
					{
						result=value;
						throw $break;
					}
				}
			);
			return result;
		},
		/**
		 * Iterates over an array and returns each item in the array
		 * in which the iterator returns true.
		 * 
		 * @param {Function} iterator The interator function which will be called for each iteration over the array.
		 * @return {Array} Array of matching items.
		 */
		findAll: function(iterator)//,context)
		{
//			iterator	=iterator.bind(context);
			var results	=[];
			this.each
			(
				function(value, index)
				{
					if (iterator(value,index))results.push(value);
				}
			);
			return results;
		},
//		/**
//		 * 
//		 * 
//		 * @return {Array}
//		 */
//		sort: function()
//		{
//			var result=[];
//			this.each
//			(
//				function(item)
//				{
//					item.key
//				}
//			);
//			return result;
//		},
		/**
		 * Returns the first item in an array.
		 * 
		 * @return {Mixed}
		 */
		first: function() 
		{
			return this[0];
		},
		/**
		 * Returns the last item in an array.
		 * 
		 * @return {Mixed}
		 */
		last: function()
		{
			return this[this.length-1];
		},
		/**
		 * Returns a boolean result if needle is found.
		 * 
		 * @param {Mixed} needle to search for within array.
		 * @return {Boolean} True if need is found in array.
		 */
		inArray: function(needle)
		{
			return (this.find
			(
				function(iteration)
				{
					return (needle==iteration)?true:false;
				}
			)!=undefined)?true:false;
		},
		/**
		 * Returns an array without the values passed to it.
		 * 
		 * @param {Mixed} ... Values to remove from the array.
		 * @return {Array} Array excluded of values to be removed.
		 */
		without: function()
		{
			if (Object.isArray(arguments[0]))
			{
				var values=$A(arguments[0]);
				return this.findAll
				(
					function(value)
					{
						return !values.include(value);
					}
				);
			}
			else
			{
				var values=$A(arguments);
				return this.findAll
				(
					function(value)
					{
						return !values.include(value);
					}
				);
			}
		}
	}
);