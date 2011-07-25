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
	Math,
	{
		/**
		 * Rounds a number to the nearest roundTo.
		 * 
		 * @param {Number} number The number to perform the rounding on.
		 * @param {Number} roundTo The number to round nearest to.
		 * @return {Number} Rounded number.
		 */
		roundToNearest: function(number,roundTo)
		{
			return number.round(number/roundTo)*roundTo;
		}
	}
);