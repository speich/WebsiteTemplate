define(function() {
	'use strict';

	/**
	 * A Module providing helper methods to work with numbers.
	 */
	return {

		/**
		 * Test if a value is numeric.
		 * @param value
		 * @return {Boolean}
		 */
		isNumeric: function(value) {
			// taken from jquery
			return !isNaN(parseFloat(value)) && isFinite(value);
		},

		/**
		 * Rounds to specified number of decimal places.
		 * @param num
		 * @param numPlaces
		 * @return {Number}
		 */
		roundTo: function(num, numPlaces) {
			var powered, rounded;

			powered = Math.pow(10, numPlaces);
			rounded = Math.round(num * powered) / powered;

			return rounded;
		},

		/**
		 * Rounds to specified number of place values.
		 * @param num
		 * @param numPlaces
		 * @return {Number}
		 */
		roundToPlaces: function(num, numPlaces) {
			var powered, rounded;

			powered = Math.pow(10, numPlaces);
			rounded = Math.round(num / powered) * powered;

			return rounded;
		},

		/**
		 * Returns an object with the number of place and decimal values.
		 * @param num
		 * @return {object}
		 */
		getPlaces: function(num) {
			var integ, obj, len, str, ch, count = 0;

			obj = {
				places: 0,
				decimals: 0,
				decLeadingZeros: 0
			};

			num = Number(num);

			integ = num > 0 ? Math.floor(num): Math.ceil(num);
			len = Math.abs(integ).toString().length; // use absolute to deal with negative numbers
			obj.places = integ === 0 ? 0: len;

			if (num === integ) {  // no decimal places
				obj.decimals = 0;
			}
			else {
				len = Math.abs(num).toString().length;
				str = Math.abs(num).toString().substring(obj.place + 1, len); // remove integer part and decimal separator
				obj.decimals = str.length;

				ch = str.substring(0, 1);
				str = str.slice(1);
				while (ch === '0' && str.length > 0) {
					ch = str.substring(0, 1);
					str = str.slice(1);
					count++;
				}
				obj.decLeadingZeros = count;
			}

			return obj;
		}
	};
});