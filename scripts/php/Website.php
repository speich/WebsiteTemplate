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
    private string $host;

    /** @var string http protocol */
    public string $protocol = 'https';

    /** @var string current path from root including page, e.g. /scripts/php/inc_global.php */
    public mixed $path;

    /** @var string current page without path, e.g. inc_global.php */
    public mixed $page;

    /** @var string current path without page, e.g. /library */
    private string $dir;

    /** @var string query string without leading question mark */
    public mixed $query = '';

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

    public static array $pageCookieDefaultOptions = [
        'Path' => '/',
        'Secure' => false,
        'HttpOnly' => true,
        'SameSite' => 'Strict'
    ];

    /**
     * Creates a new instance of the class Web.
     * @param array $domains list of whitelisted domains
     */
    public function __construct(array $domains)
    {
        $this->domains = $domains;
        $this->host = $this->isWhitelisted();
        if ($this->host === false) {
            exit('not a whitelisted domain');
        }
        $arrUrl = parse_url($this->getProtocol(true).$this->host.$_SERVER['REQUEST_URI']);
        $this->query = $arrUrl['query'] ?? '';
        if (isset($arrUrl['path'])) {
            $this->path = $arrUrl['path'];
            $arrPath = pathinfo($this->path);
            $this->page = $arrPath['basename'];
            $this->dir = rtrim($arrPath['dirname'], DIRECTORY_SEPARATOR).'/';
        }
        self::$pageCookieDefaultOptions['Domain'] = str_replace('www.', '.', $_SERVER['HTTP_HOST']);
    }

    /**
     * Check if the current host (and port) is whitelisted.
     * @return false|string
     */
    protected function isWhitelisted(): bool|string
    {
        if (in_array($_SERVER['HTTP_HOST'], $this->domains, true)) {

            return $_SERVER['HTTP_HOST'];
        }

        return false;
    }

    /**
     * Returns the date of the last update.
     * Returns the date either as a DateTime instance or a string formatted according to
     * @param ?string $format
     * @return DateTime|string|null
     */
    public static function getLastUpdate(string $format = null): DateTime|string|null
    {
        return $format === null ? self::$lastUpdate : self::$lastUpdate->format($format);
    }

    /**
     * Set the date of the last update of the website.
     * A date/time string. Valid formats are explained in https://www.php.net/manual/en/datetime.formats.php
     * @param string $lastUpdate
     */
    public static function setLastUpdate(string $lastUpdate): void
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
    public static function checkHost(string $host, ?int $port = null): bool
    {
        $fp = @fsockopen($host, $port, $errNo, $errStr, 2);

        return $fp !== false;
    }

    /**
     * Save the current url to a cookie.
     * If the argument $url is provided, it is used instead of the current url.
     * @param ?string $url
     */
    public static function setLastPage(?string $url = null): void
    {
        $url = $url ?? $_SERVER['REQUEST_URI'];

        setcookie('backPage', $url, self::$pageCookieDefaultOptions);
    }

    /**
     * Returns the page to go back to.
     * @return null|string
     */
    public static function getLastPage(): ?string
    {
        return $_COOKIE['backPage'] ?? null;
    }

    /**
     * Resets the saved url.
     */
    public static function resetLastPage(): void
    {
        unset($_COOKIE['backPage']);
        $options = [
            ...self::$pageCookieDefaultOptions,
            'Expires' => 0,

        ];
        setcookie('backPage', '', $options);
    }

    /**
     * Returns the current protocol.
     * Returns the current protocol (only http or https) from the requested page.
     * including the colon and the double slashes e.g. <protocol>:// unless false is passed as the method argument.
     * @param bool $full return additional characters?
     * @return string
     */
    public function getProtocol(?bool $full = null): string
    {
        if ((!empty($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/2.0') ||
            (!empty($_SERVER['HTTP2']) && $_SERVER['HTTP2'] === 'on') ||
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443)) {
            $this->protocol = 'https';
        } else {
            $this->protocol = 'http';
        }
        return $full === false ? $this->protocol : $this->protocol.'://';
    }
}