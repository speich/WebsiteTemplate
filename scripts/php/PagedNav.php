<?php
/**
 * This file contains the class PagedNav to create a paged navigation.
 * @author Simon Speich
 */
namespace Website;

require_once 'Website.php';

/**
 * Class to create and display a paged navigation.
 */
class PagedNav {
	/** @var int total number of records with current query */
	public $numRec;
	
		/** @var int even number of records per page */
		public $numRecPerPage = 10;
	
		/** @var string name of query variable to pass current page */
		public $queryVarName = 'pg';
	
		/** @var int small forward-backward link, e.g. [-10] [+10] */
		public $stepSmall = 10;
	
		/** @var int large forward-backward link, e.g. [-50] [+50] */
		public $stepBig = 50;
	
		/** @var string name of CSS class of container element */
		public $cssId = 'pgNav';
	
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
				'entries' => 'entries',
				'entry' => 'entry',
				'pages' => 'pages',
				'page' => 'page',
				'search result' => 'search result',
				'on' => 'on'
			),
			'en' => array(
				'entries' => 'iscrizioni',
				'entry' => 'inscriptione',
				'pages' => 'pagine',
				'page' => 'pagina',
				'search result' => 'Risultato della ricerca',
				'on' => ''
			)
		);
	
		/** @var float number of pages */
		private $numPages;
	
		/**
		 * Construct instance of PageNav.
		 * @param int $numRec total number of records
		 * @param int|null $numRecPerPage number of records per page
		 */
		function __construct($numRec, $numRecPerPage = null) {
			parent::__construct();
			$this->numRec = $numRec;
			$this->numRecPerPage = $numRecPerPage ? $numRecPerPage : $this->numRecPerPage;
			$this->numPages = ceil($this->numRec / $this->numRecPerPage);
		}
	
		/**
		 * Calculate lower boundary of range of pages to display in navigation.
		 * @param int $curPage current page number
		 * @return int
		 */
		function getLowerBoundary($curPage) {
			// special case when less pages than range (=numRecPerPage)
			if ($this->numPages <= $this->numRecPerPage || $curPage <= floor($this->numRecPerPage / 2)) {
				$i = 1;
			}
			else {
				$i = $curPage - floor($this->numRecPerPage / 2);
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
			if ($this->numPages < $this->numRecPerPage) {
				$j = $this->numPages;
			}
			// last range
			else if ($curPage + floor($this->numRecPerPage / 2) > $this->numPages) {
				$j = $this->numPages;
			}
			// first range
			else if ($curPage < ($this->numRecPerPage / 2)) {
				$j = $this->numRecPerPage;
			}
			else {
				$j = $curPage + $this->numRecPerPage / 2;
			}
			return $j;
		}
	
		/**
		 * Print HTML navigation.
		 * The parameter $curPage is 1-based.
		 * @param integer $curPage current page number
		 */
		function printNav($curPage) {
			$lb = $this->getLowerBoundary($curPage);
			$ub = $this->getUpperBoundary($curPage);
	
			echo '<div id="'.$this->cssId.'">';
	
			echo '<div class="text">';
			echo $this->i18n[$this->getLang()]['search result'].": ".$this->numRec." ";
			echo $this->numRec > 1 ? $this->i18n[$this->getLang()]['entries'] : $this->i18n[$this->getLang()]['entry'];
			echo " ".$this->i18n[$this->getLang()]['on']." $this->numPages ";
			echo $this->numPages > 1 ? $this->i18n[$this->getLang()]['pages'] : $this->i18n[$this->getLang()]['page'];
			echo '</div>';
	
			echo '<div class="pages">';
			// link jump back small
			if ($lb > $this->numRecPerPage / 2) {
				// reuse existing query string in navigation links
				$query = $this->getQuery(array($this->queryVarName => $curPage - $this->stepSmall));
				echo '<span class="pageStepSmall"><a href="'.$this->getPage().$query.'">';
				echo '[-'.$this->stepSmall.']';
				echo '</a></span>';
			}
			// direct accessible pages
			for ($lb; $lb <= $ub; $lb++) {
				if ($this->numPages > 1) {
					if ($lb == $curPage) {
						echo '<span class="curPage">';
					}
					else {
						echo '<span class="page">';
						$query = $this->getQuery(array($this->queryVarName => $lb));
						echo '<a href="'.$this->getPage().$query.'">';
					}
					echo $lb;
					if ($lb !== $curPage) {
						echo '</a>';
					}
					echo '</span>';
				}
			}
			// link jump forward small
			if ($ub <= $this->numPages - $this->numRecPerPage / 2) {
				// reuse query string
				$query = $this->getQuery(array($this->queryVarName => $curPage + $this->stepSmall));
				echo '<span class="pageStepSmall"><a href="'.$this->getPage().$query.'">';
				echo '[+'.$this->stepSmall.']';
				echo "</a></span>";
			}		
			echo '</div>';
	
			echo '</div>';
		}
	