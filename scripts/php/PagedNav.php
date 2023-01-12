<?php
/**
 * This file contains the class PagedNav to create a paged navigation.
 * @author Simon Speich
 */

namespace WebsiteTemplate;

/**
 * Class to create and display a paged navigation.
 */
class PagedNav
{
    // Note: numRec and numRecPerPage are private to force setting either through constructor or setter setProps

    /** @var int total number of records with current query */
    private int $numRec;

    /** @var int number of records to display per page */
    private int $numRecPerPage = 10;

    /** @var int number of pages */
    private int $numPages;

    /** @var string|int|float even number of links to display */
    public string|int|float $numLinks = 10;

    /** @var string name of query variable to pass current page */
    public string $queryVarName = 'pg';

    /** @var array keys that are allowed in the query string */
    private array $whitelist = [];

    /** @var int small forward-backward link, e.g. [-10] [+10] */
    public int $stepSmall = 10;

    /** @var int large forward-backward link, e.g. [-50] [+50] */
    public int $stepBig = 50;

    /** @var string name of CSS class of container element */
    public string $cssClass = 'pgNav';

    /** @var array translations for internationalization */
    public array $i18n = [
        'de' => [
            'entries' => 'Einträge',
            'entry' => 'Eintrag',
            'pages' => 'Seiten',
            'page' => 'Seite',
            'search result' => 'Suchergebnis',
            'on' => 'auf',
        ],
        'fr' => [
            'entries' => 'inscriptions',
            'entry' => 'inscription',
            'pages' => 'pages',
            'page' => 'page',
            'search result' => 'Résultat de la recherche',
            'on' => '',
        ],
        'it' => [
            'entries' => 'iscrizioni',
            'entry' => 'inscriptione',
            'pages' => 'pagine',
            'page' => 'pagina',
            'search result' => 'Risultato della ricerca',
            'on' => '',
        ],
        'en' => [
            'entries' => 'entries',
            'entry' => 'entry',
            'pages' => 'pages',
            'page' => 'page',
            'search result' => 'search result',
            'on' => 'on',
        ],
    ];

    /** @var string Language */
    public string $lang = 'en';

    /** @var bool render text */
    public bool $renderText = true;

    /**
     * Construct instance of PageNav.
     * @param ?int $numRec total number of records
     * @param ?int $numRecPerPage number of records on a page
     * @param ?int $numLinks number of links to display in navigation
     */
    public function __construct(int $numRec = null, int $numRecPerPage = null, int $numLinks = null)
    {
        if ($numRec !== null && is_numeric($numRec)) {
            $this->setNumRec($numRec);
        }
        if ($numRecPerPage !== null && is_numeric($numRecPerPage)) {    // do not overwrite default value with null
            $this->setNumRecPerPage($numRecPerPage);
        }
        if ($numLinks !== null && is_numeric($numLinks)) {    // do not overwrite default value with null
            $this->numLinks = $numLinks ?: $this->numLinks;
        }
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
     * Set total number of records
     * @param int $numRec number of records
     */
    public function setNumRec(int $numRec): void
    {
        $this->numRec = $numRec;
        $this->updateNumPages();
    }

    /**
     * Set number of records to display per page.
     * @param int $numRecPerPage
     */
    public function setNumRecPerPage(int $numRecPerPage): void
    {
        $this->numRecPerPage = $numRecPerPage;
        if (isset($this->numRec)) {
            $this->updateNumPages();
        }
    }

    /**
     * Update the number of pages.
     * Sets the total number of pages based on total number of records and number of records per page.
     */
    private function updateNumPages(): void
    {
        $this->numPages = ceil($this->numRec / $this->numRecPerPage);
    }

    /**
     * Get the total number of records
     * @return int
     */
    public function getNumRec(): int
    {
        return $this->numRec;
    }

    /**
     * @return int
     */
    public function getNumRecPerPage(): int
    {
        return $this->numRecPerPage;
    }

    /**
     * Calculate lower boundary of range of pages to display in navigation.
     * @param int $curPage current page number
     * @return int
     */
    public function getLowerBoundary(int $curPage): int
    {
        // special case when less pages than range (=numRecPerPage)
        if ($this->numPages <= $this->numLinks || $curPage <= floor($this->numLinks / 2)) {
            $i = 1;
        } else {
            $i = $curPage - floor($this->numLinks / 2);
        }

        return (int)$i;
    }

    /**
     * Calculate upper boundary of range of pages to display in navigation.
     * @param int $curPage current page number
     * @return int
     */
    public function getUpperBoundary(int $curPage): int
    {
        // special case when less pages than range (=numRecPerPage)
        if ($this->numPages < $this->numLinks) {
            $j = $this->numPages;
        } // last range
        elseif ($curPage + floor($this->numLinks / 2) > $this->numPages) {
            $j = $this->numPages;
        } // first range
        elseif ($curPage < ($this->numLinks / 2)) {
            $j = $this->numLinks;
        } else {
            $j = $curPage + $this->numLinks / 2;
        }

        return (int)$j;
    }

    /**
     * Print HTML navigation.
     * The parameter $curPage is 1-based.
     * @param int $curPage current page number
     * @param Website $web
     * @return string HTML string to print
     */
    public function render(int $curPage, Website $web): string
    {
        $query = new QueryString($this->whitelist);
        $lb = $this->getLowerBoundary($curPage);
        $ub = $this->getUpperBoundary($curPage);
        $str = '<div class="'.$this->cssClass.'">';

        if ($this->renderText) {
            $str .= '<div class="text">';
            $str .= $this->i18n[$this->lang]['search result'].': '.$this->numRec.' ';
            $str .= $this->numRec > 1 ? $this->i18n[$this->lang]['entries'] : $this->i18n[$this->lang]['entry'];
            $str .= ' '.$this->i18n[$this->lang]['on']." $this->numPages ";
            $str .= $this->numPages > 1 ? $this->i18n[$this->lang]['pages'] : $this->i18n[$this->lang]['page'];
            $str .= '</div>';
        }

        $str .= '<div class="pages">';
        // link jump back small
        if ($lb > $this->numLinks / 2) {
            // reuse existing query string in navigation links
            $queryStr = $query->withString([$this->queryVarName => $curPage - $this->stepSmall]);
            $str .= '<span class="pageStepSmall prevPages"><a href="'.$web->page.$queryStr.'">';
            $str .= '[-'.$this->stepSmall.']';
            $str .= '</a></span>';
        }
        // direct accessible pages
        for (; $lb <= $ub; $lb++) {
            if ($this->numPages > 1) {
                if ($lb === $curPage) {
                    $str .= '<span class="curPage">';
                } else {
                    $str .= '<span class="page">';
                    $queryStr = $query->withString([$this->queryVarName => $lb]);
                    $str .= '<a href="'.$web->page.$queryStr.'">';
                }
                $str .= $lb;
                if ($lb !== $curPage) {
                    $str .= '</a>';
                }
                $str .= '</span>';
            }
        }
        // link jump forward small
        if ($ub <= $this->numPages - $this->numLinks / 2) {
            // reuse query string
            $queryStr = $query->withString([$this->queryVarName => $curPage + $this->stepSmall]);
            $str .= '<span class="pageStepSmall nextPages"><a href="'.$web->page.$queryStr.'">';
            $str .= '[+'.$this->stepSmall.']';
            $str .= '</a></span>';
        }
        $str .= '</div></div>';

        return $str;
    }
}