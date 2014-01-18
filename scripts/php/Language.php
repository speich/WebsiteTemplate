<?php
namespace WebsiteTemplate;

require_once 'Website.php';

/**
 * Helper class which allows website to be multi language.
 */
class Language extends Website {
	/** @var string current language code */
	private $lang = '';

	/** @var string default language code */
	public $langDefault = 'de';

	/** @var array contains all available language codes */
	public $arrLang = array('de', 'en');

	/** @var array maps language codes to text */
	public $arrLangLong = array('de' => 'Deutsch', 'en' => 'English');

	/** @var null|array save language strings as arrays */
	public $i18n = null;

	/**
	 * Returns the language code.
	 * @return string
	 */
	public function getLang() {
		// explicitly changing lang?
		if (isset($_GET['lang'])) {
			$regExpr = "/[^".implode('', $this->arrLang)."]/";
			$this->lang = $lang = preg_replace($regExpr, '', $_GET['lang']);
		}
		// session?
		else if (isset($_SESSION[$this->namespace]['lang'])) {
			$this->lang = $_SESSION[$this->namespace]['lang'];
		}
		// check for lang preference?
		else if (isset($_COOKIE['lang'])) {
			$this->lang = $_COOKIE['lang'];
		}
		// check language header
		else {
			$lang = $this->getLangFromHeader();
			$this->lang = $lang ? $lang : $this->langDefault;
		}

		return $this->lang;
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
	public function getLangHeader() {
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
		$arr = $this->getLangHeader();

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
	 * Sets the language code.
	 * @param string $lang
	 */
	public function setLang($lang = null) {
		if (isset($lang) && in_array($lang, $this->arrLang)) {
			setcookie('lang', $lang, time() + 3600 * 24 * 365);
			$_SESSION[$this->namespace]['lang'] = $lang;
			$this->lang = $lang;
		}
	}

	/**
	 * Modify $page for language.
	 * @param string $page
	 * @param string $lang
	 * @return string
	 */
	public function createLangPage($page, $lang) {
		$page = preg_replace('/\-[a-z]{2}\.php/', '.php', $page);
		$defaultPage = $page;
		if (strpos($this->getDir(), '/articles') !== false) {
			$page = '';
		}
		else if ($page === '' || $page === 'default.php') {
			$page = 'default.php';
		}
		else if ($lang !== 'de') {
			$page = str_replace('.php', '-'.$lang.'.php', $page);
			if (!file_exists($this->getDocRoot().$this->getWebRoot().$this->getDir().$page)) {
				$page = $defaultPage;
			}
		}
		return $page;
	}

	/**
	 * Returns a HTML string with links to select the language.
	 * @return string Html
	 */
	public function renderLangNav() {
		$page = $this->page;
		$str = '';
		$str.= '<ul id="navLang" class="nav">';
		foreach ($this->arrLang as $lang) {
			$page = $this->createLangPage($page, $lang);
			$url = $this->getDir().$page.$this->getQuery(array('lang' => $lang));
			$str.= '<li';
			if ($lang == $this->getLang()) {
				$str.= ' class="navActive"';
			}
			$str.= '><a href="'.$url.'" title="'.$this->arrLangLong[$lang].'">'.strtoupper($lang).'</a>';
			$str.= '</li>';
		}
		$str.= '</ul>';

		return $str;
	}

} 