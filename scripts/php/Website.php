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
	public $host;
	/** @var string root folder e.g. / or /dev/ */
	public $webRoot;
	/** @var string current path from root including page, e.g. /library/inc_global.php */
	public $path;
	/** @var string current page without path, e.g. inc_global.php */
	public $page;
	/** @var string current path without page, e.g. /library */
	public $dir;
	/** @var string date of last website update */
	public $lastUpdate;
	/** @var string default page title */
	public $pageTitle = '';
	/** @var string namespace for session variables */
	public $namespace = 'web';
	/** @var string character set */
	public $charset = 'utf-8';
	/** @var string language code */
	public $lang = 'de';

	/**
	 * Creates a new instance of the class Web.
	 * @return Website
	 */
	public function __construct() {
		$arrUrl = parse_url("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->webRoot = '/';
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
	 * Takes an array of key-value pairs as input and adds it to the query string.
	 * If there is already the same key in the query string its value gets overwritten
	 * with the new value. Saves the added key-value pairs to be reused (query property is changed).
	 * @return string query string
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
	 * Print out all session variables.
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
	 * Print out all POST variables.
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
}