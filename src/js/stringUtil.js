/**
 * A Module providing helper methods to work with strings.
 * @module stringUtil
 */
export const stringUtil = {

    /**
     * Make first character uppercase.
     * @see https://stackoverflow.com/questions/1026069/how-do-i-make-the-first-letter-of-a-string-uppercase-in-javascript/53930826#53930826
     * @param {String} str
     * @param {string} [locale]
     * @return {String}
     */
    ucFirst: function (str, locale = 'de') {
        return str.replace(/^\p{CWU}/u, char => char.toLocaleUpperCase(locale));
    },

    /**
     * Convert lower camelCase to dashes.
     * @param {string} str string in lower camelCase
     * @return {String} string with dashes
     */
    camelCaseToDash(str) {
        return str.replace(/([A-Z])/g, "-$1").toLowerCase();
    },

    /**
     * Remove character(s) from the beginning of a string.
     * @param {string} str
     * @param {string} char chara
     * @return {string}
     */
    trimLeft(str, char) {
        const txt = this.#escapeRegExp(char);
        const regExpr = new RegExp('^[' + txt + ']+');

        return str.replace(regExpr, '');
    },

    /**
     * Remove trailing character(s) from a string.
     * @param {string} str
     * @param {string} char one or more characters
     * @return {string}
     */
    trimRight(str, char) {
        let regExpr = new RegExp('[' + char + ']+$');

        return str.replace(regExpr, '');
    },

    /**
     * Escapes special characters in a string for use in a regular expression.
     * Escapes the characters . * + ? ^ $ { } ( ) | [ ] \
     * @param {string} string
     * @return {string}
     */
    #escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
};