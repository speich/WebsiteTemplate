<?php
/**
 * File contains the class Web, which is a simple helper class to manage websites.
 * @author Simon Speich
 * @copyright You can use and alter this freely as long as you list the author.
 */

namespace WebsiteTemplate;

use DateTime;
use Exception;
use function in_array;


/**
 * Simple helper class to manage websites.
 */
class Website
{
    /** @var string host e.g. lfi.ch */
    private $host;

    /** @var string http protocol */
    public string $protocol = 'https';

    /** @var string current path from root including page, e.g. /scripts/php/inc_global.php */
    public $path;

    /** @var string current page without path, e.g. inc_global.php */
    public $page;

    /** @var string current path without page, e.g. /library */
    private string $dir;

    /** @var string query string without leading question mark */
    public $query = '';

    /** @var string default page title */
    public string $pageTitle = '';

    /** @var string web root directory on web server */
    private string $webroot = '/';

    /** @var string character set */
    public string $charset = 'utf-8';

    /** @var ?DateTime date of last update */
    private static ?DateTime $lastUpdate;

    /** @var string name of index page */
    public string $indexPage = 'index.php';

    /** @var array whitelisted domains */
    private array $domains;

    /**
     * Creates a new instance of the class Web.
     * @param array $domains list of whitelisted domains
     */
    public function __construct(array $domains)
    {
        $this->domains = $domains;
        if (in_array($_SERVER['HTTP_HOST'], $this->domains, true)) {
            $this->host = $_SERVER['HTTP_HOST'];
        }
        $arrUrl = parse_url($this->getProtocol(true).$this->host.$_SERVER['REQUEST_URI']);
        $this->query = $arrUrl['query'] ?? '';
        if (isset($arrUrl['path'])) {
            $this->path = $arrUrl['path'];
            $arrPath = pathinfo($this->path);
            $this->page = $arrPath['basename'];
            $this->dir = rtrim($arrPath['dirname'], DIRECTORY_SEPARATOR).'/';
        }
    }

    /**
     * Returns the date of the last update.
     * Returns the date either as a DateTime instance or a string formatted according to
     * @param ?string $format
     * @return DateTime|string
     */
    public function getLastUpdate($format = null)
    {
        return $format === null ? self::$lastUpdate : self::$lastUpdate->format($format);
    }

    /**
     * Set the date of last update of the website.
     * A date/time string. Valid formats are explained in https://www.php.net/manual/en/datetime.formats.php
     * @param string $lastUpdate
     */
    public function setLastUpdate(string $lastUpdate): void
    {
        try {
            self::$lastUpdate = new DateTime($lastUpdate);
        } catch (Exception $err) {
            self::$lastUpdate = null;
        }
    }

    /**
     * Returns the host
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * List of domain names the website runs on
     * @param array $domains
     */
    public function setDomains(array $domains): void
    {
        $this->domains = $domains;
    }

    /**
     * Returns the document root always with a trailing slash.
     * @return string
     */
    public function getDocRoot(): string
    {
        return rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/';
    }

    /**
     * Returns the web root always with a trailing slash.
     * @return string
     */
    public function getWebRoot(): string
    {
        return rtrim($this->webroot, '/').'/';
    }

    /**
     * Set the web root.
     * @param string $webroot
     */
    public function setWebroot(string $webroot): void
    {
        $this->webroot = $webroot;
    }

    /**
     * Returns the path always with trailing slash.
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * Implements a ping to check if a host is available.
     * @param string $host
     * @param ?int $port
     * @return bool
     */
    public function checkHost(string $host, ?int $port = null): bool
    {
        $fp = @fsockopen($host, $port, $errNo, $errStr, 2);

        return $fp !== false;
    }

    /**
     * Save current url to a session variable.
     * If argument $url is provided it is used instead of current url.
     * @param ?string $url
     */
    public function setLastPage(?string $url = null): void
    {
        $url = $url ?? $_SERVER['REQUEST_URI'];
        $options = [
            'expires' => 0,
            'path' => '/',
            'domain' => str_replace('www.', '.', $_SERVER['HTTP_HOST']),
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        setcookie('backPage', $url, $options);
    }

    /**
     * Returns the page to go back to.
     * @return null|string
     */
    public function getLastPage(): ?string
    {
        return $_COOKIE['backPage'] ?? null;
    }

    /**
     * Resets the saved url.
     */
    public function resetLastPage(): void
    {
        unset($_COOKIE['backPage']);
        $options = [
            'expires' => 0,
            'path' => '/',
            'domain' => str_replace('www.', '.', $_SERVER['HTTP_HOST']),
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        setcookie('backPage', '', $options);
    }

    /**
     * Returns the current protocol.
     * Returns the current protocol (only HTTP or HTTPS) from the requested page
     * including the colon and the double slashes e.g. <protocol>:// unless false is passed as the method argument.
     * @param bool $full return additional characters ?
     * @return string
     */
    public function getProtocol(?bool $full = null): string
    {
        $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443 ? 'https' : 'http';

        return $full === true ? $this->protocol.'://' : $this->protocol;
    }
}