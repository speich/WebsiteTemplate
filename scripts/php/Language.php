<?php

namespace WebsiteTemplate;

/**
 * Helper class which allows website to be multi language.
 */
class Language
{
	/** @var string current language code */
	private $lang = '';

	/** @var string default language code */
	private $langDefault = 'de';

	/** @var array maps language codes to text */
	public $arrLang = array('de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English');

	/** @var string regular expression to match language from page naming */
	private $pagePattern;

	/** @var string regular expression to match language from directory naming */
	private $dirPattern;

	/**
	 * Language constructor.
	 */
	public function __construct()
	{
		$this->pagePattern = '/-(' . implode('|', $this->getAll()) . ')\.php/';
		$this->dirPattern = '/\/(' . implode('|', $this->getAll()) . ')\//';
	}

	/**
	 * Returns the language code.
	 * Gets the language short code by order of precedence of query string, session, cookie or http header.
	 * @return string
	 */
	public function get()
	{
		return $this->lang;
	}

	/**
	 * Returns all language codes
	 * @return array
	 */
	protected function getAll()
	{
		return array_keys($this->arrLang);
	}

	/**
	 * Returns an array containing languages from the accept-language header.
	 * e.g. Array (
	 *    [en-ca] => 1
	 *    [en] => 0.8
	 *    [en-us] => 0.6
	 *    [de-de] => 0.4
	 *    [de] => 0.2
	 * )
	 * @see http://www.thefutureoftheweb.com/blog/use-accept-language-header
	 * @return array
	 */
	public function getHeaderLanguages()
	{
		$arr = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.\d+))?/i',
				$_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrLang);

			if (count($arrLang[1]) > 0) {
				// create a list like "en" => 0.8
				$arr = array_combine($arrLang[1], $arrLang[4]);

				// set default to 1 for any without q factor
				foreach ($arr as $lang => $val) {
					if ($val === '') {
						$arr[$lang] = 1;
					}
				}
				// sort list based on value
				arsort($arr, SORT_NUMERIC);
			}
		}

		return $arr;
	}

	/**
	 * Return language string extracted from HTTP header.
	 * @return bool|string
	 */
	public function fromHeader()
	{
		$arr = $this->getHeaderLanguages();

		// look through sorted list and use first one that matches our languages
		foreach ($arr as $lang => $val) {
			$lang = explode('-', $lang);
			if (array_key_exists($lang[0], $this->arrLang)) {
				return $lang[0];
			}
		}

		return false;
	}

	/**
	 * Sets the page pattern
	 * Regular expression pattern to get the language from the page name.
	 * @param null|string $pagePattern
	 */
	public function setPagePattern($pagePattern)
	{
		$this->pagePattern = $pagePattern;
	}

	/**
	 * Returns the page (filename) of the current url
	 * @return string
	 */
	public function getPage()
	{
		return pathinfo($_SERVER['REQUEST_URI'], PATHINFO_BASENAME);
	}

	/**
	 * Sets the language code.
	 * @param null|string $lang
	 */
	public function set($lang)
	{
		$this->lang = $lang;
	}

	/**
	 * Save the language code to a cookie.
	 * @param string $lang
	 */
	public function save($lang)
	{
		$this->setCookie($lang);
	}

	/**
	 * Checks if the language is valid.
	 * Checks the language against the list of available languages, e.g. from Language::arrLang
	 * @param string $lang
	 * @return bool
	 */
	public function isValid($lang)
	{
		return array_key_exists($lang, $this->arrLang);
	}

	/**
	 * Returns the default language.
	 * @return string
	 */
	public function getDefault()
	{
		return $this->langDefault;
	}

	/**
	 * Tries to detect the language automatically.
	 * Detects the language in the following order: from the query string, from the page name, from the cookie or
	 * from the http header.
	 * @return string|false
	 */
	public function autoDetect()
	{
		// from query string
		if (isset($_GET['lang'])) {
			$lang = preg_replace('/\W/', '', $_GET['lang']);
		} // from directory, e.g. /en/
		elseif (preg_match($this->dirPattern, $_SERVER['REQUEST_URI'], $matches) === 1) {
			$lang = $matches[1];
		} // from page name
		elseif (preg_match($this->pagePattern, $this->getPage(), $matches) === 1) {
			// note: default language is not part of the page name, e.g. page{-defaultLang}.php does not exist
			$lang = $matches[1];
		} // cookie?
		elseif (isset($_COOKIE['lang'])) {
			$lang = $_COOKIE['lang'];
		} // http language header or false
		else {
			$lang = $this->fromHeader();
		}

		return $this->isValid($lang) ? $lang : false;
	}

	/**
	 * Stores the language in a cookie.
	 * @param $lang
	 */
	public function setCookie($lang)
	{
		// remove subdomain www from host to prevent conflicting with cookies set in subdomain
		$options = [
			'expires' => time() + 3600 * 24 * 365,
			'path' => '/',
			'domain' => str_replace('www.', '.', $_SERVER['HTTP_HOST']),
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Strict'
		];
		setcookie('lang', $lang, $options);
	}

	/**
	 * Automatically detect and set the language.
	 * @param bool $save save to cookie?
	 * @see Language::autoDetect()
	 */
	public function autoSet($save = true)
	{
		$lang = $this->autoDetect();

		$this->lang = $lang === false ? $this->getDefault() : $lang;
		if ($save === true) {
			$this->save($lang);
		}
	}

	/**
	 * Modify the name of page to match the current language.
	 * Inserts a minus character and the language abbreviation between page name and page extension except
	 * for the default language, e.g.: mypage.php -> mypage-fr.php
	 * @param string $page page only
	 * @param string|null $lang
	 * @return string
	 */
	public function createPage($page, $lang = null)
	{
		$page = preg_replace('/\-[a-z]{2}\.(php|gif|jpg|pdf)$/', '.$1', $page);

		if ($lang === null) {
			$lang = $this->get();
		}

		if ($lang !== $this->langDefault) {
			$page = preg_replace('/\.(php|gif|jpg|pdf)$/', '-' . $lang . '.$1', $page);
		}

		return $page;
	}
} 