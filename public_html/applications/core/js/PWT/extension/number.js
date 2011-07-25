Object.extend
(
	Number.prototype,
	{
		/**
		 * Converts an angle in degrees to radians.
		 * @return {Number} the radian.
		 */
		toRad: function()
		{
			return this*Math.PI/180;
		},
		/**
		 * Converts an angle in radians to degrees (signed).
		 * @return {Number} the degree.
		 */
		toDeg: function()
		{
			return this*180/Math.PI;
		},
		/**
		 * Converts radians to degrees (as bearing: 0...360)
		 * @return {Number} the degree.
		 */
		toBrng: function()
		{
			return (this.toDeg()+360)%360;
		}
	}
)