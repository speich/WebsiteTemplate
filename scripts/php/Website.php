<?php
/**
 * Singleton
 */
class Website	{
	private static $url, $page, $dir, $ip, $host, $docRoot;

	/** @var Website Holds scripts instance */
	private static $instance;

	/** $var string Sets webroot to subbfolder */
	private static $webRoot = '/';

	/** @var string Stores query string without leading question mark */
	private static $query = '';

	/** @var string Date of last website update */
	protected static $lastUpdate;

	/** @var string File name of default page */
	private static $defaultPage = 'index.php';

	/** @var string Default page title */
	protected static $windowTitle;

	/** @var string Default language code */
	protected static $lang = 'de';

	/** @var array Holds all available languages and the corresponding file name extensions */
	private static $arrLang = array('de' => '', 'fr' => '-fr', 'it' => '-it', 'en' => '-en');

	/** @var string File name of back page */
	private static $backPage = '';

	/** @var string Namespace for session variables */
	private static $namespace = 'Website';

	public function __construct() {
		// these vars are not reliable, so be careful when using them
		$arrUrl = parse_url($_SERVER["REQUEST_URI"]);
		$arrPath = pathinfo($arrUrl['path']);
		self::$page = $arrPath['basename'];
		self::$dir = $arrPath['dirname'];
		// note www.speich.net/articles/?p=12 returns / and not /articles
		if (strpos(self::$page, '.') === false) {
			self::$page = '';
			self::$dir = rtrim($arrUrl['path'], '/');
		}
		self::$ip = $_SERVER['REMOTE_ADDR'];
		self::$host = $_SERVER['HTTP_HOST'];
		self::$docRoot = $_SERVER['DOCUMENT_ROOT'];
		self::$url = $_SERVER["REQUEST_URI"];
		self::$query = array_key_exists('query', $arrUrl) ? $arrUrl['query'] : '';
	}

	public static function getInstance() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Returns physical path to webroot.
	 * Path is returned without a trailing slash and if
	 * site resides in a subfolder, this subfolder is appended to the path.
	 * @return string
	 * @see Website::setWebRoot()
	 */
	public function getDocRoot() {
		return self::$docRoot;
	}

	/**
	 * Sets the website root to a subfolder.
	 *
	 * It is not always possible in a webproject to use relative paths.
	 * But with absolute or physical paths you could run into problems:
	 * If you want to move your project into another subfolder or you
	 * publish your website into different folders
	 * e.g. www.mywebsite.ch and www.mywebsite.ch/developerversion/
	 * In these cases use the methods setWebRoot() and getWebRoot().
	 * @param string $path webroot
	 */
	function setWebRoot($path) {
		$path = trim($path, '/');
		self::$webRoot = $path == '' ? '/' : '/'.$path. '/';
	}

	/**
	 * Returns the website's root folder.
	 * Returns an absolute path with trailing slash.
	 * @return string
	 */
	function getWebRoot() { return self::$webRoot; }

	public function getIp() { return self::$ip; }

	public function getHost() {	return self::$host; }

	/**
	 * Returns complete current url.
	 * @return string
	 */
	public function getUrl() { return self::$url; }

	/**
	 * Returns the current web page.
	 * @return string
	 */
	public function getPage() {
		if (self::$dir == '\\' || self::$dir == '/') {
			return '';
		}
		else { 	return self::$page; }
	}

	/**
	 * Saves current url to a session variable.
	 * Stores Url to use to go back in a session variable. If argument
	 * is provided it is used instead.
	 * @param string $url [optional]
	 * @param string $namespace [optional]
	 */
	public function setLastPage($url = null, $namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		if (isset($url)) {
			$_SESSION[$namespace]['backPage'] = $url;
		}
		else {
			$_SESSION[$namespace]['backPage'] = $this->getUrl();
		}
	}

	/**
	 * Returns the page to go back to.
	 * @param string $namespace [optional]
	 * @return null|string
	 */
	public function getLastPage($namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		if (!isset($_SESSION[$namespace]['backPage'])) {
			return null;
		}
		else {
			return $_SESSION[$namespace]['backPage'];
		}
	}

	/**
	 * Resets the saved url.
	 * @param string $namespace [optional]
	 */
	public function resetLastPage($namespace = null) {
		$namespace = is_null($namespace) ? self::$namespace : $namespace;
		unset($_SESSION[$namespace]['BackPage']);
	}

	/**
	 * Returns the current web directory.
	 * @return string
	 */
	public function getDir() { return self::$dir; }

	/**
	 * Takes an array of key-value pairs as input and adds it to the query string.
	 *
	 * If there is already the same key in the query string its value gets overwritten
	 * with the new value. Saves the added key-value pairs to be reused (query property is changed).
	 * @return string querystring
	 * @param array $arrQuery
	 */
	public function addQuery($arrQuery) {
		if (self::$query != '') {	// check if query var(s) already exists -> overwrite with new or append
			parse_str(self::$query, $arrVar);
			$arrQuery = array_merge($arrVar, $arrQuery);	// if arrays have same string keys, the later key will overwrite the previous
			self::$query = http_build_query($arrQuery);		// update local self::$Query
		}
		else {
			self::$query = http_build_query($arrQuery);
		}
		return '?'.htmlspecialchars(self::$query);
	}

	/**
	 * Returns the current query string.
	 *
	 * You can optionally add or remove key-value pairs from the returned querystring without changing it,
	 * e.g. same as AddQuery or DelQuery but Query property remains unchanged.
	 * If second argument is an array, the first array is used to add, the second to delete. Otherwise
	 * second argument is:
	 * 1 = add (default), 2 = remove
	 * @return string query string
	 * @param array $arrQuery
	 * @param integer|array $modifier
	 */
	public function getQuery($arrQuery = null, $modifier = 1) {
		$strQuery = '';
		if (is_null($arrQuery)) {
			if (self::$query != '') {
				$strQuery.= '?'.htmlspecialchars(self::$query);
			}
		}
		else if (is_array($modifier)) {	// all of second array is to delete
			$str = $this->getQuery($modifier, 2);
			$str = str_replace('?', '', $str);
			$str = html_entity_decode($str);
			parse_str($str, $arrVar);
			$arrQuery = array_merge($arrVar, $arrQuery);
			$strQuery.= '?'.htmlspecialchars(http_build_query($arrQuery));
		}
		else {	// first array is either add or delete, no second array
			if (self::$query != '') {	// check if query var(s) already exists -> overwrite with new or append
				parse_str(self::$query, $arrVar);
				if ($modifier == 1) {
					$arrQuery = array_merge($arrVar, $arrQuery);	// if arrays have same string keys, the later key will overwrite the previous
					$strQuery.= '?'.htmlspecialchars(http_build_query($arrQuery));		// update local self::$Query
				}
				else if ($modifier == 2) {
					$arr = array();	// make array keys for array_diff_key
					foreach ($arrQuery as $QueryVar) {
						$arr[$QueryVar] = null;
					}
					$arrQuery = array_diff_key($arrVar, $arr);
					if (count($arrQuery) > 0) {
						$strQuery.= '?'.htmlspecialchars(http_build_query($arrQuery));
					}
				}
			}
			else if ($modifier == 1){
				$strQuery.= '?'.htmlspecialchars(http_build_query($arrQuery));
			}
		}
		return $strQuery;
	}

	/**
	 * Removes key-value pairs from query string before returning it.
	 * @return array
	 * @param array|string $arrQuery Object
	 */
	public function delQuery($arrQuery) {
		if (!is_array($arrQuery)) {
			$arrQuery = array($arrQuery);
		}
		if (self::$query != '') {
			foreach ($arrQuery as $queryVar) {
				$pattern = '/&?'.$queryVar.'=[^\&]*/';
				self::$query = preg_replace($pattern, '', self::$query);
			}
		}
		self::$query = preg_replace('/^\&/', '', self::$query); // if first key-value pair was removed change ampersand to questions mark
		return htmlspecialchars($this->getQuery());
	}

	/**
	 * Returns date of last website update.
	 * @return string
	 */
	public function getLastUpdate() { return self::$lastUpdate; }

  /**
   * Sets the date of the last website update.
   * @param string $lastUpdate
   */
  public function setLastUpdate($lastUpdate) {
  	self::$lastUpdate = $lastUpdate;
  }

	/**
	 * Checks if a certain user is logged in.
	 */
	public function checkLoggedIn() {
		if (!isset($_SESSION[self::$namespace]["LoggedIn"]) && $_SESSION[self::$namespace]["LoggedIn"] != 1) {
			if (func_num_args() == 0) { header("Location: ".self::$defaultPage); }
			else { header("Location: ".func_get_args(0)); }
		}
	}

	/**
	 * Returns the language code.
	 * @see Website::$lang
	 */
	public function getLang() {
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
	 * @return array
	 */
	public function getLangHeader() {
		$langs = array();

		// @see http://www.thefutureoftheweb.com/blog/use-accept-language-header
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrLang);

			if (count($arrLang[1]) > 0) {
				// create a list like "en" => 0.8
				$langs = array_combine($arrLang[1], $arrLang[4]);

				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val) {
					if ($val === '') {
						$langs[$lang] = 1;
					}
				}

				// sort list based on value
				arsort($langs, SORT_NUMERIC);
			}
		}
		return $langs;
	}

	/**
	 * Returns a language dependent file name extension.
	 * @param string $lang language code
	 * @return string
	 */
	public function getLangFileExt($lang = null) {
		if (is_null($lang)) {
			$lang = self::getLang();
		}
		if (array_key_exists($lang, self::$arrLang)) {
			return self::$arrLang[$lang];
		}
		else {
			return false;
		}
	}

	/**
	 * Sets the language.
	 * @param string $lang
	 */
	public function setLang($lang) {
	  self::$lang = $lang;
	}

	/**
	 * Returns the title of the browser window.
	 * @return string
	 */
	public function getWindowTitle() {
		return self::$windowTitle;
	}

	/**
	 * Returns the current set name space.
	 * @return string
	 */
	public function getNamespace() {
		return self::$namespace;
	}

	/**
	 * Sets the window title of the web browser.
	 * @param string $title
	 */
	public function setWindowTitle($title) {
		self::$windowTitle = $title;
	}
}

?>