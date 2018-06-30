<?php

namespace WebsiteTemplate;

/**
 * Class LanguageMenu
 * Class to render a menu to change the language
 */
class LanguageMenu extends Language
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
    private $web;

    /** @var bool link text based on label instead of lang */
    public $useLabel = false;

    /**
     * LanguageMenu constructor.
     * @param Website $web
     */
	public function __construct(Website $web)
    {
        parent::__construct();
        $this->web = $web;
        $this->redirect = '/'.$web->indexPage;
    }

    /**
     * Returns a HTML string with links to the current page in all available languages.
     * Method checks if the page exist for each language. If it doesn't, the link will point to a language switcher page,
     * which is referenced with the property LanguageMenu::redirect
     * @param Website $web
     * @return string html
     */
    public function render()
    {
        $query = new QueryString();
        $count = 0;
        $str = '';
        $str .= '<ul id="'.$this->ulId.'" class="'.$this->ulClass.'">';
        foreach ($this->arrLang as $lang => $label) {
            $page = $this->createPage($this->web->page, $lang);
            $path = $this->web->getDir();
            if (file_exists($this->web->getDocRoot().$path.$page)) {
                $url = $path.$page.$query->withString(array('lang' => $lang));
            } else {
                $url = $this->redirect.$query->withString(array('lang' => $lang, 'url' => $path.$page));
            }
            $str .= '<li';
            if ($lang == $this->get()) {
                $str .= ' class="'.$this->liClassActive.'"';
            }
            $text = $this->useLabel ? $label : strtoupper($lang);
            $str .= '><a href="'.$url.'" title="'.$label.'">'.$text.'</a>';
            $str .= '</li>';
            if ($this->delimiter != '' && $count < count($this->arrLang)) {
                $str .= '<li>'.$this->delimiter.'</li>';
            }
            $count++;
        }
        $str .= '</ul>';

        return $str;
    }
}