<?php
/**
 * File contains the class Web, which is a simple helper class to manage websites.
 * @author Simon Speich
 * @copyright You can use and alter this freely as long as you list the author.
 * @package General
 */

/**
 * Simple helper class to manage websites.
 * @package General
 */
class Website {
	/** @var string host e.g. www.lfi.ch */
	private $host;
	/** @var string root folder e.g. / or /dev/ */
	private $webRoot;
	/** @var string physical path to root / e.g. c:/websites/lfi/ */
	private $docRoot;
	/** @var string current path from root including page, e.g. /library/inc_global.php */
	private $path;
	/** @var string current page without path, e.g. inc_global.php */
	private $page;
	/** @var string current path without page, e.g. /library */
	private $dir;
	/** @var string date of last website update */
	protected $lastUpdate;
	/** @var string default page title */
	protected $pageTitle = '';
	/** @var string namespace for session variables */
	protected $namespace = 'Web';
	/** @var string character set */
	protected $charset = 'utf-8';
	/** @var string language code */
	protected $lang = 'de';

	/**
	 * Creates a new instance of the class Web.
	 * @return Website
	 */
	public function __construct() {
		$arrUrl = parse_url("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->docRoot = $_SERVER['DOCUMENT_ROOT'];
		$this->webRoot = '/'; // has to be set to another subfolder by using setWebRoot()
		$this->host = $_SERVER['HTTP_HOST'];
		$this->query = isset($arrUrl['query']) ? $arrUrl['query'] : false;
		if (isset($arrUrl['path'])) {
			$this->path = $arrUrl['path'];
			$arrPath = pathinfo($this->path);
			$this->page = $arrPath['basename'];
			$this->dir = $arrPath['dirname'];
		}
		else {
			$this->path = false;
			$this->page = false;
			$this->dir = false;
		}
	}

	/**
	 * Returns the host name of the website.
	 * e.g. www.mywebsite.ch
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Returns the absolute path from the web root including the current page.
	 * E.g. /examplepath/examplefile.php
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Returns the query string of the current url.
	 * @return string|boolean
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Takes an array of key-value pairs as input and adds it to the query string.
	 * If there is already the same key in the query string its value gets overwritten
	 * with the new value. Saves the added key-value pairs to be reused (query property is changed).
	 * @return string querystring
	 * @param array $arrQuery
	 */
	public function addQuery($arrQuery) {
		if ($this->query != '') { // check if query var(s) already exists -> overwrite with new or append
			parse_str($this->query, $arrVar);
			$arrQuery = array_merge($arrVar, $arrQuery); // if arrays have same string keys, the later key will overwrite the previous
			$this->query = http_build_query($arrQuery); // update local self::$Query
		}
		else {
			$this->query = http_build_query($arrQuery);
		}
		return '?'.htmlspecialchars($this->query);
	}

	/**
	 * Returns the file name of the current page.
	 * @return string
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Returns the absolute path from the web root excluding the page.
	 * E.g. /examplepath without the trailing slash.
	 * @return string
	 */
	public function getDir() {
		return $this->dir;
	}

	/**
	 * Returns the document root directory under which the current website is running.
	 * e.g. C:/Documents and Settings/Websites/mywebsite/
	 * @return string
	 */
	public function getDocRoot() {
		return $this->docRoot;
	}

	/**
	 * Sets the document root to a subfolder.
	 *
	 * It is not always possible in a webproject to use relative paths.
	 * But with absolute or phyisical paths you run into problems
	 * if you want to move your project into another subfolder or you
	 * publish your website into different folders
	 * e.g. www.mywebsite.ch and www.mywebsite.ch/developerversion/
	 * In these cases use getWebRoot()/ SetWebRoot() or SetDocRoot()/GetDocRoot
	 *
	 * @param string $path
	 */
	function setDocRoot($path) {
		// if your developing version is in a subdir
		$this->docRoot = $this->docRoot.'/'.$path;
	}

	/**
	 * Returns the webs root directory under which the current website is running.
	 * If developer version runs in a different subdir set it with the SetWebRoot() method, e.g. /dev
	 * @return string
	 */
	public function getWebRoot() {
		return $this->webRoot;
	}

	/**
	 * Sets the document root to a subfolder.
	 * It is not always possible in a webproject to use relative paths.
	 * But with absolute or phyisical paths you run into problems
	 * if you want to move your project into another subfolder or you
	 * publish your website into different folders
	 * e.g. www.mywebsite.ch and www.mywebsite.ch/developerversion/
	 * In these cases use getWebRoot()/ SetWebRoot() or SetDocRoot()/GetDocRoot
	 *
	 * @param string $path
	 */
	function setWebRoot($path) {
		// if your developing version is in a subdir
		$this->webRoot = $path;
	}

	/**
	 * Prints out all session variables.
	 */
	public function printSession() {
		echo "<strong>Sessions:</strong><br>";
		foreach ($_SESSION as $id => $val) {
			if (is_array($val)) {
				$arr = array_map(create_function('$key, $value', 'return "[".$key."]: ".$value." ";'), array_keys($val), array_values($val));
				$val = "$id ".implode('', $arr);
			}
			else {
				$val = "$id: ".$val;
			}
			echo htmlspecialchars($val, ENT_NOQUOTES, $this->charset)."<br>";
		}
	}

	/**
	 * Prints out all POST variables.
	 */
	public function printPost() {
		echo "<strong>Posts:</strong><br>";
		foreach ($_POST as $id => $val) {
			if (is_array($val)) {
				echo var_export($val);
			}
			else {
				echo "$id: $val<br>";
			}
		}
	}

	/**
	 * Implements a ping to check if a host is available.
	 * @param string $host
	 * @param integer $port
	 * @return bool
	 */
	public function checkHost($host, $port = null) {
		$fp = @fsockopen($host, $port, $errNo, $errStr, 2);
		return $fp != false;
	}

	/**
	 * Returns the namespace used in session variables.
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * Sets the namespace used in session variables
	 * @param string $name
	 */
	public function setNamespace($name) {
		$this->namespace = $name;
	}

	/**
	 * Set websites default character set
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Get websites default character set
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Save current url to a session variable.
	 * If argument $url is provided it is used instead of current url.
	 * @param string $url
	 * @param string $namespace
	 */
	public function setLastPage($url = null, $namespace = null) {
		$namespace = is_null($namespace) ? $this->namespace : $namespace;
		if (isset($url)) {
			$_SESSION[$namespace]['backPage'] = $url;
		}
		else {
			$_SESSION[$namespace]['backPage'] = $_SERVER['REQUEST_URI'];
		}
	}

	/**
	 * Returns the page to go back to.
	 * @param string $namespace
	 * @return null|string
	 */
	public function getLastPage($namespace = null) {
		$namespace = is_null($namespace) ? $this->namespace : $namespace;
		if (!isset($_SESSION[$namespace]['backPage'])) {
			return null;
		}
		else {
			return $_SESSION[$namespace]['backPage'];
		}
	}

	/**
	 * Resets the saved url.
	 * @param string $namespace
	 */
	public function resetLastPage($namespace = null) {
		$namespace = is_null($namespace) ? $this->namespace : $namespace;
		unset($_SESSION[$namespace]['backPage']);
	}

	/**
	 * Returns the page title.
	 * @return string
	 */
	public function getPageTitle() {
		return $this->pageTitle;
	}

	/**
	 * Sets the page title.
	 * @param string $pageTitle
	 */
	public function setPageTitle($pageTitle) {
		$this->pageTitle = $pageTitle;
	}

	/**
	 * Returns date of last website update.
	 * @return string
	 */
	public function getLastUpdate() {
		return $this->lastUpdate;
	}

	/**
	 * Sets the date of the last website update.
	 * @param string $lastUpdate
	 * @see Website::$lastUpdate
	 */
	public function setLastUpdate($lastUpdate) {
		$this->lastUpdate = $lastUpdate;
	}

	/**
   * Returns the language identifier.
   * @return string
   */
	public function getLang() {
		return $this->lang;
	}

}