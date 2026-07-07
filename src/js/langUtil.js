export const langUtil = {

  /**
   * Get the language code from the HTML lang attribute.
   * Returns 'de' if no lang attribute is set.
   * @return {string}
   */
  get: function() {
    let lang = 'de';

    if (document.documentElement.hasAttribute('lang') && document.documentElement.lang !== '') {
      lang = document.documentElement.lang;
      lang = this.fromLocale(lang);
    }

    return lang;
  },

  fromLocale: function(locale) {
    return locale.split('-')[0];
  },

};