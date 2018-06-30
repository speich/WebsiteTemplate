<?php

namespace WebsiteTemplate;

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

    /**
     * LanguageMenu constructor.
     * @param Website $web
     */
	public function __construct(Website $web)
    {
        $this->redirect = '/'.$web->indexPage;
    }

    /**
     * Returns a HTML string with links to the current page in all available languages.
     * Method checks if the page exist for each language. If it doesn't, it will direct to the language switcher page
     * referenced with the property LanguageMenu::redirect
     * @param Website $web
     * @return string html
     */
    public function render($web)
    {
        $query = new QueryString();
        $count = 0;
        $str = '';
        $str .= '<ul id="'.$this->ulId.'" class="'.$this->ulClass.'">';
        foreach ($this->arrLang as $lang => $label) {
            $page = $this->createPage($web->page, $lang);
            $path = $web->getDir();
            if (file_exists($web->getDocRoot().$path.$page)) {
                $url = $path.$page.$query->withString(array('lang' => $lang));
            } else {
                $url = $this->redirect.$query->withString(array('lang' => $lang, 'url' => $path.$page));
            }
            $str .= '<li';
            if ($lang == $this->get()) {
                $str .= ' class="'.$this->liClassActive.'"';
            }
            $str .= '><a href="'.$url.'" title="'.$label.'">'.strtoupper($lang).'</a>';
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