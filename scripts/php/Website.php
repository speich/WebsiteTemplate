<?php
/**
 * File contains the class Web, which is a simple helper class to manage websites.
 * @author Simon Speich
 * @copyright You can use and alter this freely as long as you list the author.
 */

namespace WebsiteTemplate;

/**
 * Simple helper class to manage websites.
 */
class Website
{
    /** @var string host e.g. www.lfi.ch */
    public $host;

    /** @var string */
    public $protocol = 'http';

    /** @var string current path from root including page, e.g. /scripts/php/inc_global.php */
    public $path;

    /** @var string current page without path, e.g. inc_global.php */
    public $page;

    /** @var string current path without page, e.g. /library */
    private $dir;

    /** @var string query string without leading question mark */
    public $query = '';

    /** @var string default page title */
    public $pageTitle = '';

    /** @var string namespace for session variables */
    public $namespace = __NAMESPACE__;

    /** @var string web root directory on web server */
    private $webroot = '/';

    /** @var string character set */
    public $charset = 'utf-8';

    /** @var string date of last update */
    public $lastUpdate = '';

    /** @var string name of index page */
    public $indexPage = 'index.php';

    /**
     * Creates a new instance of the class Web.
     */
    public function __construct()
    {
        $arrUrl = parse_url("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $this->host = $_SERVER['HTTP_HOST'];
        $this->query = isset($arrUrl['query']) ? $arrUrl['query'] : '';
        if (isset($arrUrl['path'])) {
            $this->path = $arrUrl['path'];
            $arrPath = pathinfo($this->path);
            $this->page = $arrPath['basename'];
            $this->dir = rtrim($arrPath['dirname'], DIRECTORY_SEPARATOR).'/';
        }
    }

    /**
     * Returns the document root always with a trailing slash.
     * @return string
     */
    public function getDocRoot()
    {
        return rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/';
    }

    /**
     * Returns the web root always with a trailing slash.
     * @return string
     */
    public function getWebRoot()
    {
        return rtrim($this->webroot, '/').'/';
    }

    /**
     * Set the web root.
     * @param string $webroot
     */
    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
    }

    /**
     * Returns the path always with trailing slash.
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Implements a ping to check if a host is available.
     * @param string $host
     * @param integer $port
     * @return bool
     */
    public function checkHost($host, $port = null)
    {
        $fp = @fsockopen($host, $port, $errNo, $errStr, 2);

        return $fp != false;
    }

    /**
     * Save current url to a session variable.
     * If argument $url is provided it is used instead of current url.
     * @param string $url
     * @param string $namespace
     */
    public function setLastPage($url = null, $namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        if (isset($url)) {
            $_SESSION[$namespace]['backPage'] = $url;
        } else {
            $_SESSION[$namespace]['backPage'] = $_SERVER['REQUEST_URI'];
        }
    }

    /**
     * Returns the page to go back to.
     * @param string $namespace
     * @return null|string
     */
    public function getLastPage($namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        if (!isset($_SESSION[$namespace]['backPage'])) {
            return null;
        } else {
            return $_SESSION[$namespace]['backPage'];
        }
    }

    /**
     * Resets the saved url.
     * @param string $namespace
     */
    public function resetLastPage($namespace = null)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        unset($_SESSION[$namespace]['backPage']);
    }

    /**
     * Takes an array of key-value pairs as input and adds it to the query string.
     * If there is already the same key in the query string its value gets overwritten
     * with the new value. Saves the added key-value pairs to be reused (query property is changed).
     * @return string query string
     * @param array $arrQuery
     */
    public function addQuery($arrQuery)
    {
        if ($this->query != '') { // check if query var(s) already exists -> overwrite with new or append
            parse_str($this->query, $arrVar);
            $arrQuery = array_merge($arrVar,
                $arrQuery); // if arrays have same string keys, the later key will overwrite the previous
        }
        $this->query = http_build_query($arrQuery); // update local self::$Query

        return '?'.$this->query;
    }

    /**
     * Returns the current query string.
     *
     * Returns the query string including the question mark or an empty string
     * You can optionally add or remove key-value pairs from the returned querystring without changing it,
     * e.g. same as addQuery or delQuery but the query property remains unchanged.
     * If second argument is an array, the first array is used to add, the second to delete. Otherwise
     * second argument is:
     * 1 = add (default), 2 = remove
     * @param array $arrQuery
     * @param integer|array $modifier
     * @return string query string
     */
    public function getQuery($arrQuery = null, $modifier = 1)
    {
        if (is_null($arrQuery)) {
            if ($this->query === '') {
                return '';
            } else {
                parse_str($this->query, $arrQuery);
            }
        } else {
            if (is_array($modifier)) { // all of second array is to delete
                $str = $this->getQuery($modifier, 2);
                $str = str_replace('?', '', $str);
                parse_str($str, $arrVar);
                $arrQuery = array_merge($arrVar, $arrQuery);
            } else { // first array is either add or delete, no second array
                if ($this->query != '') { // check if query var(s) already exists -> overwrite with new or append
                    parse_str($this->query, $arrVar);
                    if ($modifier == 1) {
                        $arrQuery = array_merge($arrVar,
                            $arrQuery); // if arrays have same string keys, the later key will overwrite the previous
                    } else {
                        if ($modifier == 2) {
                            $arr = array(); // make array keys for array_diff_key
                            foreach ($arrQuery as $QueryVar) {
                                $arr[$QueryVar] = null;
                            }
                            $arrQuery = array_diff_key($arrVar, $arr);
                            if (count($arrQuery) === 0) {
                                return '';
                            }
                        }
                    }
                } else {
                    if ($modifier !== 1) {
                        return '';
                    }
                }
            }
        }

        return '?'.http_build_query($arrQuery);
    }

    /**
     * Removes key-value pairs from querystring before returning it.
     * @return array
     * @param array|string $arrQuery Object
     */
    public function delQuery($arrQuery)
    {
        if (!is_array($arrQuery)) {
            $arrQuery = array($arrQuery);
        }
        if ($this->query != '') {
            foreach ($arrQuery as $queryVar) {
                $pattern = '/&?'.$queryVar.'=[^\&]*/';
                $this->query = preg_replace($pattern, '', $this->query);
            }
        }
        $this->query = preg_replace('/^\&/', '',
            $this->query); // if first key-value pair was removed change ampersand to questions mark

        return htmlspecialchars($this->getQuery());
    }

    /**
     * Returns the current protocol.
     * Returns the current protocol (only HTTP or HTTPS) from the requested page
     * including the colon and the double slashes e.g. <protocol>:// unless false is passed as the method argument.
     * @param bool $full return additional characters ?
     * @return string
     */
    public function getProtocol($full = true)
    {
        $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
        if ($full) {
            return $this->protocol.'://';
        } else {
            return $this->protocol;
        }
    }
}