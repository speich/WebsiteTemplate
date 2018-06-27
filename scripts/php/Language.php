<?php

namespace WebsiteTemplate;

use stdClass;


/**
 * Helper class which allows website to be multi language.
 */
class Language
{

    /** @var string current language code */
    private $lang = '';

    /** @var string default language code */
    private $langDefault = 'de';

    /** @var array maps language codes to text */
    public $arrLang = array('de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English');

    /** @var string namespace for session to use */
    private $namespace = __NAMESPACE__;

    /** @var null|string regular expression to match language from page naming */
    private $pagePattern = null;

    /**
     * Language constructor.
     */
    public function __construct()
    {
        $this->pagePattern = '/-('.implode('|', $this->getAll()).')\.php/';
    }

    /**
     * Returns the language code.
     * Gets the language short code by order of precedence of query string, session, cookie or http header.
     * @return string
     */
    public function get()
    {
        return $this->lang;
    }

    /**
     * Returns all language codes
     * @return array
     */
    public function getAll()
    {
        return array_keys($this->arrLang);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
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
    public function getHeaderLanguages()
    {
        $arr = array();

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
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
    public function getLangFromHeader()
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
     * @param null|string $pagePattern
     */
    public function setPagePattern($pagePattern)
    {
        $this->pagePattern = $pagePattern;
    }

    /**
     * Returns the page (filename) of the current url
     * @return string
     */
    public function getPage()
    {
        return pathinfo($_SERVER['REQUEST_URI'], PATHINFO_BASENAME);
    }

    /**
     * Sets the language code.
     * Sets the language property either explicitly by passing it or detects it automatically either using the query string
     * or the lanuage contained in the page url.
     * @param null|string $lang
     */
    public function set($lang = null)
    {
        // Note: do not read session or cookies to switch language here, otherwise accessing the webite
        // directly from a link might messup languages. Use cookie or session to read language only on index page

        // set explicitly, override all
        if (isset($lang)) {
            $this->lang = $lang;
        } // query string ? (e.g. switch to different language)?
        else if (isset($_GET['lang'])) {
            $this->lang = preg_replace('/\W/', '', $_GET['lang']);
        } // language by page url
        else if (preg_match($this->pagePattern, $this->getPage(), $matches) === 1) {
            // note: default language 'de' is not part ot the page url, e.g. page-de.php does not exist
            // will be set to 'de' bewlow, e.g. using default lanuage
            $this->lang = $matches[1];
        } // use default language
        else {
            $this->lang = $this->langDefault;
        }

        // check that lang property only contains valid content
        if (!array_key_exists($this->lang, $this->arrLang)) {
            $this->lang = $this->langDefault;
        }

        $this->setCookie($this->lang);
    }

    /**
     * @return string
     */
    public function getLangDefault()
    {
        return $this->langDefault;
    }

    /**
     * Returns the language detected from a previously set cookie or from the http header.
     * @return string
     */
    public function getAutoDefault()
    {
        $lang = $this->langDefault;

        // if there is no previously set language, e.g. (session or cooky) use browser http header
        $langHeader = $this->getLangFromHeader();

        // cookie?
        if (isset($_COOKIE[$this->namespace]['lang'])) {
            $lang = $_COOKIE[$this->namespace]['lang'];
        } // http language header
        else if ($langHeader) {
            $lang = $langHeader;
        }

        return $lang;
    }

    /**
     * Stores the lanuage in a cookie.
     * @param $lang
     */
    public function setCookie($lang)
    {
        // remove subdomain www from host to prevent conflicting with cookies set in subdomain
        $domain = str_replace('www.', '.', $_SERVER['HTTP_HOST']);
        setcookie($this->namespace.'[lang]', $lang, time() + 3600 * 24 * 365, '/', $domain, false);
    }

    /**
     * Modify a $page for language.
     * Inserts a minus character and the language abbreviation between page name and page extension except
     * for the default language, e.g.: mypage.php -> mypage-fr.php
     * @param string $page page only
     * @param string|null $lang
     * @return string
     */
    public function createPage($page, $lang = null)
    {
        $page = preg_replace('/\-[a-z]{2}\.(php|gif|jpg|pdf)$/', '.$1', $page);

        if (is_null($lang)) {
            $lang = $this->get();
        }

        if ($lang === '') {
            $lang = $this->langDefault;
        }

        if ($lang !== $this->langDefault) {
            //$page = str_replace('.php', '-'.$lang.'.php', $page);
            $page = preg_replace('/\.(php|gif|jpg|pdf)$/', '-'.$lang.'.$1', $page);
        }

        return $page;
    }

    /**
     * Returns a HTML string with links to the current page in all available languages.
     * Method checks if the page exist for each language. If it doesn't, it will direct to the language switcher page
     * given with Config->switcher
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
    public function renderNav($config, $web)
    {
        $query = new QueryString();
        $count = 0;
        $str = '';
        $str .= '<ul id="'.$config->ulId.'" class="'.$config->ulClass.'">';
        foreach ($this->arrLang as $lang => $label) {
            $page = $this->createPage($web->page, $lang);
            $path = $web->getDir();
            if (file_exists($web->getDocRoot().$path.$page)) {
                $url = $path.$page.$query->withString(array('lang' => $lang));
            } else {
                $url = $config->redirect.$query->withString(array('lang' => $lang, 'url' => $path.$page));
            }
            $str .= '<li';
            if ($lang == $this->get()) {
                $str .= ' class="'.$config->liClassActive.'"';
            }
            $str .= '><a href="'.$url.'" title="'.$label.'">'.strtoupper($lang).'</a>';
            $str .= '</li>';
            if ($config->delimiter != '' && $count < count($this->arrLang)) {
                $str .= '<li>'.$config->delimiter.'</li>';
            }
            $count++;
        }
        $str .= '</ul>';

        return $str;
    }

} 