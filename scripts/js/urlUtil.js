define(['dojo/_base/lang', 'dojo/io-query'], function(lang, ioQuery) {
	'use strict';

	/**
	 * A Module providing helper methods to work with urls.
	 * @exports NAFIDAS/mapping/urlUtil
	 */
	return {

		// get window query string information as an object.
		queryToObject: function() {
			var queryStr = window.location.search.slice(1);
			return ioQuery.queryToObject(queryStr);
		}

	};

});