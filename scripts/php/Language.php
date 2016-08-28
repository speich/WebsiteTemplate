<?php
namespace WebsiteTemplate;

use stdClass;


/**
 * Helper class which allows website to be multi language.
 */
class Language {

	/** @var string current language code */
	private static $lang = '';

	/** @var string default language code */
	public static $langDefault = 'de';

	/** @var array contains all available language codes */
	public $arrLang = array('de', 'fr', 'it', 'en');

	/** @var array maps language codes to text */
	public $arrLangLong = array('de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English');

	/** @var string namespace for session to use */
	public $namespace = __NAMESPACE__;

	/** @var null|string regular expression to match language from page naming */
	private $pagePattern = null;

	/** @var null|string page name to match against  */
	public $page = null;

	/**
	 * Language constructor.
	 */
	public function __construct() {
		if (isset($this->page)) {
			$this->pagePattern = '/-(['.implode('|', $this->arrLang).'])\.php/';
		}
	}

	/**
	 * Returns the language code.
	 * Gets the language short code by order of precedence of query string, session, cookie or http header.
	 * @return string
	 */
	public static function get() {
		return self::$lang;
	}

	/**
	 * Returns an array containing the content from the accept-language header.
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
	public function getHttpHeader() {
		$arr = array();

		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrLang);

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
	public function getLangFromHeader() {
		$arr = $this->getHttpHeader();

		// look through sorted list and use first one that matches our languages
		foreach ($arr as $lang => $val) {
			$lang = explode('-', $lang);
			if (in_array($lang[0], $this->arrLang)) {
				return $lang[0];
			}
		}
		return false;
	}

	/**
	 * @param null|string $pagePattern
	 */
	public function setPagePattern($pagePattern) {
		$this->pagePattern = $pagePattern;
	}

	/**
	 * Sets the language code.
	 * Sets the language property either explicitly by passing it or automatically by checking in order of precendence: query string,
	 * session, cookie, http header or optionally the current page.
	 * @param null|string $lang
	 */
	public function set($lang = null) {

		$langHeader = $this->getLangFromHeader();

		// set explicitly, overrides all
		if (isset($lang)) {
			self::$lang = $lang;
		}

		// query string?
		if (isset($_GET['lang'])) {
			self::$lang = preg_replace('/\W/', '', $_GET['lang']);
		}
		// session?
		else if (isset($_SESSION[$this->namespace]['lang'])) {
			self::$lang = $_SESSION[$this->namespace]['lang'];
		}
		// cookie?
		else if (isset($_COOKIE[$this->namespace]['lang'])) {
			self::$lang = $_COOKIE[$this->namespace]['lang'];
		}
		// http language header
		else if ($langHeader) {
			self::$lang = $langHeader;
		}
		// by page/file name
		else if (isset($this->pagePattern) && preg_match($this->pagePattern, $this->page, $matches) === 1) {
			self::$lang = $matches[1];
		}
		else {
			self::$lang = self::$langDefault;
		}

		// check that lang property only contains valid content
		if (!in_array(self::$lang, $this->arrLang)) {
			self::$lang = self::$langDefault;
		}

		// remove subdomain www from host to prevent conflicting with cookies set in subdomain
		$domain = str_replace('www.', '.', $_SERVER['HTTP_HOST']);
		setcookie($this->namespace.'[lang]', self::$lang, time() + 3600 * 24 * 365, '/', $domain, false);
		$_SESSION[$this->namespace]['lang'] = self::$lang;
	}

	/**
	 * Modify a $page for language.
	 * Inserts a minus character and the language abbreviation between page name and page extension except
	 * for the default language, e.g.: mypage.php -> mypage-fr.php
	 * @param string $page page only
	 * @param string|null $lang
	 * @return string
	 */
	public function createPage($page, $lang = null) {
		$page = preg_replace('/\-[a-z]{2}\.(php|gif|jpg|pdf)$/', '.$1', $page);

		if (is_null($lang)) {
			$lang = $this->get();
		}

		if ($lang === '') {
			$lang = self::$langDefault;
		}

		if ($lang !== self::$langDefault) {
			//$page = str_replace('.php', '-'.$lang.'.php', $page);
			$page = preg_replace('/\.(php|gif|jpg|pdf)$/', '-'.$lang.'.$1', $page);
		}

		return $page;
	}

	/**
	 * Returns a HTML string with links to the current page in all available languages.
	 * Method checks if the page exist for each language. If it doesn't
	 * Config object allows to overwrite the following HTML attributes:
	 *   $config->ulId            = 'navLang'
	 *   $config->ulClass         = 'nav'
	 *   $config->liClassActive   = 'navActive'
	 *   $config->delimiter      = ''
	 *   $config->redirect         = Website::getWebRoot().Website::indexPage?lang=Website::langDefault;
	 *
	 * @param stdClass $config
	 * @param Website $web
	 * @return string Html
	 */
	public function renderNav($config = null, $web) {
		if (is_null($config)) {
			$config = new stdClass();
			$config->ulId = 'navLang';
			$config->ulClass = 'nav';
			$config->liClassActive = 'navActive';
			$config->delimiter = '';
			$config->redirect = '/'.$web->indexPage;
		}

		$str = '';
		$str .= '<ul id="'.$config->ulId.'" class="'.$config->ulClass.'">';
		foreach ($this->arrLang as $lang) {
			$page = $web->page === '' ? $this->createPage($web->indexPage, $lang) : $this->createPage($web->page, $lang);
			$file = $web->getDir().$page;
			if (file_exists(__DIR__.'/..'.$file)) {
				$url = $file.$web->getQuery(['lang' => $lang]);
			}
			else {
				$url = $this->createPage($config->redirect).$web->getQuery(['lang' => $lang, 'url' => $file]);
			}
			$str .= '<li';
			if ($lang == $this->get()) {
				$str .= ' class="'.$config->liClassActive.'"';
			}
			$str .= '><a href="'.$url.'" title="'.$this->arrLangLong[$lang].'">'.strtoupper($lang).'</a>';
			$str .= '</li>';
			if ($config->delimiter != '' && key($this->arrLang) < count($this->arrLang)) {
				$str .= '<li>'.$config->delimiter.'</li>';
			}
		}
		$str .= '</ul>';

		return $str;
	}

} 