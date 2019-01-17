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
     */
    public function setLastPage($url = null)
    {
        $url = $url === null ? $_SERVER['REQUEST_URI'] : $url;
        setcookie('backPage', $url, 0, '/', $_SERVER['HTTP_HOST']);

    }

    /**
     * Returns the page to go back to.
     * @return null|string
     */
    public function getLastPage()
    {
        return isset($_COOKIE['backPage']) ? $_COOKIE['backPage'] : null;
    }

    /**
     * Resets the saved url.
     */
    public function resetLastPage()
    {
        unset($_COOKIE['backPage']);
        setcookie('backPage', '', 0, '/', $_SERVER['HTTP_HOST']);
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