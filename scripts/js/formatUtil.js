/**
 * @module NAFIDAS/formatUtil
 * A Module providing helper methods to format results.
 */
define(function() {
	'use strict';

	return {
		/**
		 * Format a unit with superscript tags.
		 * e.g. m2*ha-1
		 * @param {string} unit Unit to format.
		 * @return string
		 */
		formatUnit: function(unit) {
			unit = unit.replace(/m(2|3)/, "m<sup>$1</sup>");
			// currently not used in output
			// note: js does not support Positive and Negative Lookbehind, rewrite this regexpr
			// unit = unit.replace(/(?<![0-9])-1(?![0-9\-])/, "<sup>-1</sup>");
			unit = unit.replace('*', '·');
			return unit;
		}
	};
});
