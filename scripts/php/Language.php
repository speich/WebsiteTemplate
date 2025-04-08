<?php

namespace WebsiteTemplate;

use function array_key_exists;
use function count;

/**
 * Helper class which allows website to be multi language.
 */
class Language
{
    /** @var string current language code */
    private string $lang = '';

    /** @var string default language code */
    private string $langDefault = 'de';

    /** @var array maps language codes to text */
    public array $arrLang = ['de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English'];

    /** @var string regular expression capturing group for language codes */
    private string $langCaptureGroup;

    /**
     * Language constructor.
     */
    public function __construct()
    {
        $this->langCaptureGroup = '('.implode('|', $this->getAll()).')';
    }

    /**
     * Returns the language code.
     * Gets the language short code by order of precedence of query string, session, cookie or http header.
     * @return string
     */
    public function get(): string
    {
        return $this->lang;
    }

    /**
     * Returns all language codes
     * @return array
     */
    public function getAll(): array
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
    public function getHeaderLanguages(): array
    {
        $arr = [];

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
    public function fromHeader(): bool|string
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
    public function setPagePattern(?string $pagePattern): void
    {
        $this->langCaptureGroup = $pagePattern;
    }

    /**
     * Returns the page (filename) of the current url
     * @return string
     */
    public function getPage(): string
    {
        return basename($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Sets the language code.
     * @param null|string $lang
     */
    public function set(?string $lang): void
    {
        $this->lang = $lang;
    }

    /**
     * Save the language code to a cookie.
     * @param string $lang
     */
    public function save(string $lang): void
    {
        $this->setCookie($lang);
    }

    /**
     * Checks if the language is valid.
     * Checks the language against the list of available languages, e.g. from Language::arrLang
     * @param string $lang
     * @return bool
     */
    public function isValid(string $lang): bool
    {
        return array_key_exists($lang, $this->arrLang);
    }

    /**
     * Returns the default language.
     * @return string
     */
    public function getDefault(): string
    {
        return $this->langDefault;
    }

    /**
     * Tries to detect the language automatically.
     * Detects the language in the following order: from the query string, from the page name, from the cookie or
     * from the http header.
     * @return string|false
     */
    public function autoDetect(): bool|string
    {
        // from query string
        if (isset($_GET['lang'])) {
            $lang = preg_replace('/\W/', '', $_GET['lang']);
        } // from directory, e.g. /en/
        elseif (preg_match('/\/'.$this->langCaptureGroup.'(\/|$)/', $_SERVER['REQUEST_URI'], $matches) === 1) {
            $lang = $matches[1];
        } // from page name
        elseif (preg_match('/-'.$this->langCaptureGroup.'\.php/', $this->getPage(), $matches) === 1) {
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
     * @param string $lang
     */
    public function setCookie(string $lang): void
    {
        $options = [
            ...Website::$pageCookieDefaultOptions,
            'Expires' => time() + 3600 * 24 * 365,
        ];
        setcookie('lang', $lang, $options);
    }

    /**
     * Automatically detect and set the language.
     * @param ?bool $save save to cookie?
     * @see Language::autoDetect()
     */
    public function autoSet(?bool $save = null): void
    {
        $lang = $this->autoDetect();
        $this->lang = $lang === false ? $this->getDefault() : $lang;
        if ($save !== false) {
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
    public function createPage(string $page, ?string $lang = null): string
    {
        if ($lang === null) {
            $lang = $this->get();
        }

        // remove language postfix
        $removed = $this->removePostfix($page);
        $page = $removed ?? $page;

        // add new language postfix
        if ($lang !== $this->langDefault) {
            $prefixed = preg_replace('/\.([a-z]+)$/', '-'.$lang.'.$1', $page);
            $page = is_string($prefixed) ? $prefixed : $page;
        }

        return $page;
    }

    /**
     * Remove the language postfix from the page string.
     * @param string $page
     * @return string
     */
    public function removePostfix(string $page): string
    {

        return preg_replace('/-'.$this->langCaptureGroup.'\.([a-z]+)$/', '.$2', $page);
    }
}