<?php

namespace WebsiteTemplate;

/**
 * Class LanguageMenu
 * Class to render a menu to change the language
 */
class LanguageMenu
{
    use CssBemTrait;

    /** @var ?string id attribute of HTMLUListElement */
    public ?string $cssId = null;

    /** @var string url to redirect to if page does not exists in that language */
    public string $redirect;

    /** @var Website */
    protected Website $web;

    /** @var bool link text based on label instead of lang */
    public bool $useLabel = false;

    /** @var Language */
    protected Language $lang;

    /** @var array keys that are allowed in the query string */
    private array $whitelist = [];

    /**
     * LanguageMenu constructor.
     * @param Language $lang
     * @param Website $web
     */
    public function __construct(Language $lang, Website $web)
    {
        // Initialize BEM defaults here (not as property overrides) because CssBemTrait
        // is composed directly; redeclaring $bemBlock/$bemElement in the class body
        // would trigger PHP's trait-property compatibility check (different default).
        $this->bemBlock = 'lang-menu';
        $this->bemElement = 'item';

        $this->lang = $lang;
        $this->web = $web;
        $this->redirect = '/'.$web->indexPage;
    }

    /**
     * Set the allowed keys in the query string.
     * @param array $whitelist
     */
    public function setWhitelist(array $whitelist): void
    {
        $this->whitelist = $whitelist;
    }

    /**
     * Returns an HTML string with links to the current page in all available languages.
     * Method checks if the page exists for each language. If it doesn't, the link will point to a language switcher page,
     * which is referenced with the property LanguageMenu::redirect
     * @return string html
     */
    public function render(): string
    {
        $language = $this->lang;
        $query = new QueryString($this->whitelist);
        $str = '';
        $cssId = $this->cssId === null ? '' : ' id="'.$this->cssId.'"';
        $str .= '<ul'.$cssId.' class="'.$this->bemClass(element: '').'">';
        foreach ($language->arrLang as $lang => $label) {
            $page = $this->lang->createPage($this->web->page, $lang);
            $path = $this->web->getDir();
            $text = $this->useLabel ? $label : strtoupper($lang);
            if (file_exists($this->web->getDocRoot().$path.$page)) {
                $url = $path.$page.$query->withString(['lang' => $lang]);
            } else {
                $url = $this->redirect.$query->withString(['lang' => $lang, 'url' => $path.$page]);
            }
            if ($lang === $language->get()) {
                $str .= '<li class="'.$this->bemClass(modifier: 'active').'">'.$text.'</li>';
            } else {
                $str .= '<li class="'.$this->bemClass().'">';
                $str .= '<a class="'.$this->bemClass('link').'" href="'.htmlspecialchars($url).'" title="'.$label.'">'.$text.'</a></li>';
            }
        }
        $str .= '</ul>';

        return $str;
    }
}