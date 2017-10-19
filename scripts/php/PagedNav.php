<?php
/**
 * This file contains the class PagedNav to create a paged navigation.
 * @author Simon Speich
 */
namespace WebsiteTemplate;


/**
 * Class to create and display a paged navigation.
 */
class PagedNav {
	// Note: numRec and numRecPerPage are private to force setting either through constructor or setter setProps

	/** @var int total number of records with current query */
	private $numRec;

	/** @var int number of records to display per page */
	private $numRecPerPage = 10;

	/** @var int number of pages */
	private $numPages;

	/** @var int even number of links to display */
	public $numLinks = 10;

	/** @var string name of query variable to pass current page */
	public $queryVarName = 'pg';

	/** @var int small forward-backward link, e.g. [-10] [+10] */
	public $stepSmall = 10;

	/** @var int large forward-backward link, e.g. [-50] [+50] */
	public $stepBig = 50;

	/** @var string name of CSS class of container element */
	public $cssClass = 'pgNav';

	/** @var array translations for internationalization */
	public $i18n = array(
		'de' => array(
			'entries' => 'Einträge',
			'entry' => 'Eintrag',
			'pages' => 'Seiten',
			'page' => 'Seite',
			'search result' => 'Suchergebnis',
			'on' => 'auf'
		),
		'fr' => array(
			'entries' => 'inscriptions',
			'entry' => 'inscription',
			'pages' => 'pages',
			'page' => 'page',
			'search result' => 'Résultat de la recherche',
			'on' => ''
		),
		'it' => array(
			'entries' => 'iscrizioni',
			'entry' => 'inscriptione',
			'pages' => 'pagine',
			'page' => 'pagina',
			'search result' => 'Risultato della ricerca',
			'on' => ''
		),
		'en' => array(
			'entries' => 'entries',
			'entry' => 'entry',
			'pages' => 'pages',
			'page' => 'page',
			'search result' => 'search result',
			'on' => 'on'
		)
	);

	/** @var string Language */
    public $lang = 'en';

	/** @var bool render text */
	public $renderText = true;

	/**
	 * Construct instance of PageNav.
	 * @param int $numRec total number of records
	 * @param int $numRecPerPage number of records on a page
	 * @param int|null $numLinks number of links to display in navigation
	 */
	function __construct($numRec = null, $numRecPerPage = null, $numLinks = null) {
		if (!is_null($numRec) && is_numeric($numRec)) {
			$this->numRec = $numRec;
			$this->updateNumPages();
		}
		if (!is_null($numRecPerPage) && is_numeric($numRecPerPage)) {	// do not overwrite default value with null
			$this->numRecPerPage = $numRecPerPage;
		}
		if (!is_null($numLinks) && is_numeric($numLinks)) {	// do not overwrite default value with null
			$this->numLinks = $numLinks ? $numLinks : $this->numLinks;
		}
	}

	/**
	 * Set total number of records
	 * @param int $numRec number of records
	 */
	public function setNumRec($numRec) {
		$this->numRec = $numRec;
		$this->updateNumPages();
	}

	/**
	 * Set number of records to display per page.
	 * @param int $numRecPerPage
	 */
	public function setNumRecPerPage($numRecPerPage) {
		$this->numRecPerPage = $numRecPerPage;
		if (isset($this->numRec)) {
			$this->updateNumPages();
		}
	}

	/**
	 * Update the number of pages.
	 * Sets the total number of pages based on total number of records and number of records per page.
	 */
	private function updateNumPages() {
		$this->numPages = ceil($this->numRec / $this->numRecPerPage);
	}

	/**
	 * Get the total number of records
	 * @return int
	 */
	public function getNumRec() {
		return $this->numRec;
	}

	/**
	 * @return int
	 */
	public function getNumRecPerPage() {
		return $this->numRecPerPage;
	}

	/**
	 * Calculate lower boundary of range of pages to display in navigation.
	 * @param int $curPage current page number
	 * @return int
	 */
	function getLowerBoundary($curPage) {
		// special case when less pages than range (=numRecPerPage)
		if ($this->numPages <= $this->numLinks || $curPage <= floor($this->numLinks / 2)) {
			$i = 1;
		}
		else {
			$i = $curPage - floor($this->numLinks / 2);
		}
		return $i;
	}

	/**
	 * Calculate upper boundary of range of pages to display in navigation.
	 * @param int $curPage current page number
	 * @return int
	 */
	function getUpperBoundary($curPage) {
		// special case when less pages than range (=numRecPerPage)
		if ($this->numPages < $this->numLinks) {
			$j = $this->numPages;
		}
		// last range
		else if ($curPage + floor($this->numLinks / 2) > $this->numPages) {
			$j = $this->numPages;
		}
		// first range
		else if ($curPage < ($this->numLinks / 2)) {
			$j = $this->numLinks;
		}
		else {
			$j = $curPage + $this->numLinks / 2;
		}
		return $j;
	}

	/**
	 * Print HTML navigation.
	 * The parameter $curPage is 1-based.
	 * @param integer $curPage current page number
	 * @param Website $web
	 * @return string HTML string to print
	 */
	function render($curPage, $web) {
		$lb = $this->getLowerBoundary($curPage);
		$ub = $this->getUpperBoundary($curPage);

		$str = '<div class="'.$this->cssClass.'">';

		if ($this->renderText) {
			$str.= '<div class="text">';
			$str.= $this->i18n[$this->lang]['search result'].": ".$this->numRec." ";
			$str.= $this->numRec > 1 ? $this->i18n[$this->lang]['entries'] : $this->i18n[$this->lang]['entry'];
			$str.= " ".$this->i18n[$this->lang]['on']." $this->numPages ";
			$str.= $this->numPages > 1 ? $this->i18n[$this->lang]['pages'] : $this->i18n[$this->lang]['page'];
			$str.= '</div>';
		}

		$str.= '<div class="pages">';
		// link jump back small
		if ($lb > $this->numLinks / 2) {
			// reuse existing query string in navigation links
			$query = $web->getQuery(array($this->queryVarName => $curPage - $this->stepSmall));
			$str.= '<span class="pageStepSmall prevPages"><a href="'.$web->page.$query.'">';
			$str.= '[-'.$this->stepSmall.']';
			$str.= '</a></span>';
		}
		// direct accessible pages
		for (; $lb <= $ub; $lb++) {
			if ($this->numPages > 1) {
				if ($lb == $curPage) {
					$str.= '<span class="curPage">';
				}
				else {
					$str.= '<span class="page">';
					$query = $web->getQuery(array($this->queryVarName => $lb));
					$str.= '<a href="'.$web->page.$query.'">';
				}
				$str.= $lb;
				if ($lb !== $curPage) {
					$str.= '</a>';
				}
				$str.= '</span>';
			}
		}
		// link jump forward small
		if ($ub <= $this->numPages - $this->numLinks / 2) {
			// reuse query string
			$query = $web->getQuery(array($this->queryVarName => $curPage + $this->stepSmall));
			$str.= '<span class="pageStepSmall nextPages"><a href="'.$web->page.$query.'">';
			$str.= '[+'.$this->stepSmall.']';
			$str.= "</a></span>";
		}
		$str.= '</div></div>';

		return $str;
	}
}