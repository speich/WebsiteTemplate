<?php

namespace WebsiteTemplate;

/**
 * Class LanguageMenu
 * Class to render a menu to change the language
 */
class LanguageMenu
{
    /** @var string id attribute of HTMLUListElement */
	public $ulId = 'navLang';

	/** @var string class attribute of HTMLUListElement */
	public $ulClass = 'nav';

	/** @var string class attribute of HTMLLIElement */
	public $liClassActive = 'navActive';

	/** @var string character to display between language labels */
	public $delimiter = '';

	/** @var string url to redirect to if page does not exists in that language */
	public $redirect;

	/** @var Website */
    protected $web;

    /** @var bool link text based on label instead of lang */
    public $useLabel = false;

    /** @var Language */
    protected $lang;

    /** @var array keys that are allowed in the query string */
    private $whitelist = array();

    /**
     * LanguageMenu constructor.
     * @param Language $lang
     * @param Website $web
     */
	public function __construct(Language $lang, Website $web)
    {
        $this->lang = $lang;
        $this->web = $web;
        $this->redirect = '/'.$web->indexPage;
    }

    /**
     * Set the allowed keys in the query string.
     * @param array $whitelist
     */
    public function setWhitelist($whitelist) {
        $this->whitelist = $whitelist;
    }

    /**
     * Returns a HTML string with links to the current page in all available languages.
     * Method checks if the page exist for each language. If it doesn't, the link will point to a language switcher page,
     * which is referenced with the property LanguageMenu::redirect
     * @return string html
     */
    public function render()
    {
        $language = $this->lang;
        $query = new QueryString($this->whitelist);
        $count = 0;
        $str = '';
        $str .= '<ul id="'.$this->ulId.'" class="'.$this->ulClass.'">';
        foreach ($language->arrLang as $lang => $label) {
            $page = $this->lang->createPage($this->web->page, $lang);
            $path = $this->web->getDir();
            $text = $this->useLabel ? $label : strtoupper($lang);
            if (file_exists($this->web->getDocRoot().$path.$page)) {
                $url = $path.$page.$query->withString(array('lang' => $lang));
            } else {
                $url = $this->redirect.$query->withString(array('lang' => $lang, 'url' => $path.$page));
            }
            if ($lang === $language->get()) {
                $str .= '<li class="'.$this->liClassActive.'">'.$text.'</li>';
            }
            else {
                $str .= '<li><a href="'.htmlspecialchars($url).'" title="'.$label.'">'.$text.'</a></li>';
            }
            if ($this->delimiter !== '' && $count < count($language->arrLang)) {
                $str .= '<li>'.$this->delimiter.'</li>';
            }
            $count++;
        }
        $str .= '</ul>';

        return $str;
    }
}