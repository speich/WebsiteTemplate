/**
 * A Module providing helper methods to work with urls.
 * @module urlUtil
 */
export const urlUtil = {

  /**
   * Get query string information
   * If nothing is passed, the query string is taken from the browser address bar.
   * @param {string} [str] query string to parse
   * @return {URLSearchParams}
   */
  queryToObject: function(str) {
    let q;

    if (str) {
      q = new URLSearchParams(str);
    } else {
      q = new URL(window.location.href).searchParams;
    }

    return q;
  },

  /**
   * Returns the hash from the browser address bar.
   *  If the URL does not have a fragment identifier, this property contains an empty string
   * @param {boolean} [withHashCharacter] include the '#' character in the returned string
   * @return {string}
   */
  hash: function(withHashCharacter) {
    let url = new URL(window.location.href).hash;

    return withHashCharacter === false ? url.slice(1) : url;
  },

  /**
   * Returns the object as a query string.
   * @param {Object} obj
   * @return {string}
   */
  objectToQuery: function(obj) {
    return new URLSearchParams(obj).toString();
  },

  /**
   * Returns either the full url or only the path, but both without the query string.
   * E.g. returns the address https://www.lfi.ch/resultate/resultateauswahl.php?p=theme
   * either as https://www.lfi.ch/resultate/resultateauswahl.php
   * or as /resultate/resultateauswahl.php
   * @param {Boolean} [pathOnly] return only path
   * @return {String}
   */
  noQuery: function(pathOnly) {
    let url = pathOnly ? window.location.pathname : window.location.href;

    return url.substring(url.indexOf('?') + 1, url.length);
  },

  /**
   * Returns the full url or only the path with the query string appended.
   * @param {string|URLSearchParams} query query string or query object to include
   * @param {Boolean} [pathOnly]
   * @return {string}
   */
  withQuery: function(query, pathOnly) {
    let queryStr;

    queryStr = query instanceof URLSearchParams ? query.toString() : new URLSearchParams(query).toString();
    queryStr = queryStr === '' ? '' : '?' + queryStr;

    return this.noQuery(pathOnly) + queryStr;
  },

  queryAndHash: function() {
    let str = this.queryToObject().toString();

    str = str === '' ? '' : '?' + str;

    return str + this.hash();
  }
};