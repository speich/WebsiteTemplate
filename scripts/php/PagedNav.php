<?php
/**
 * This file contains the class PagedNav to create a paged navigation.
 * @author Simon Speich
 * @package General
 */
namespace Website;

/**
 * Class to create and display a paged navigation.
 */
class PagedNav {

	/**
	 * Number of records per page, used to calculate the number of pages in paged navigation.
	 * @var int
	 */
	private $numRecPerPage;

	/**
	 * Total number of records with current query, used to calculate number of pages
	 * @var int
	 */
	private $numRec;

	/**
	 * Current page to display
	 * @var int
	 */
	private $curPageNum;

	/**
	 * range of pages in paged navigation, must be an even number
	 * @var int
	 */
	private $range = 12;

	/**
	 * display [-10] [+10] backward - forward jump, must be an even number
	 * @var int
	 */
	private $stepSmall = 10;

	/**
	 * display [-50] [+50] backward - forward jump, must be an even number
	 * @var int
	 */
	private $stepBig = 50;

	/**
	 * name of variable in querystring to set page number
	 * @var string
	 */
	private $varNamePgNav = 'cp';

	/**
	 * Constructs the paged navigation.
	 * Total number of records with given query, e.g. with WHERE clause included.
	 * @param Website $web instance of class Website
	 * @param integer $curPageNum Current page to display.
	 * @param integer $numRec Total number of records
	 * @param integer $numRecPerPage Number of records per page
	 */
	public function __construct($web, $curPageNum, $numRec, $numRecPerPage) {
		$this->web = $web;
		$this->curPageNum = $curPageNum;
		$this->numRec = $numRec;
		$this->numRecPerPage = $numRecPerPage;
	}

	/**
	 * Set the number of links to directly accessible pages.
	 * This number has to be even.
	 * @param integer $range number of links
	 */
	public function setRange($range) {
		// TODO: check if even number
		$this->range = $range;
	}

	/**
	 * Set how many pages can be skipped.
	 *
	 * @param integer $stepSmall
	 * @param integer $stepBig
	 */
	public function setStep($stepSmall, $stepBig) {
		// TODO: check if even number
		$this->stepSmall = $stepSmall;
		$this->stepBig = $stepBig;
	}

	/**
	 * Outputs HTML paged data navigation.
	 */
	public function printNav() {
		// calc total number of pages
		$numPage = ceil($this->numRec / $this->numRecPerPage);
		// lower limit (start)
		$start = 1;
		if ($this->curPageNum - $this->range / 2 > 0) {
			$start = $this->curPageNum - $this->range / 2;
		}
		// upper limit (end)
		$end = $this->curPageNum + $this->range / 2;
		if ($this->curPageNum + $this->range / 2 > $numPage) {
			$end = $numPage;
		}
		// special cases
		if ($numPage < $this->range) {
			$end = $numPage;
		}
		else {
			if ($end < $this->range) {
				$end = $this->range;
			}
		}

		echo '<nav><ul>';
		// jump back big step
		if ($this->curPageNum > $this->stepBig / 2) { // && $this->curPageNum >= $this->stepBig + $this->stepSmall) {
			$stepBig = ($this->curPageNum > $this->stepBig ? $this->stepBig : $this->curPageNum - 1);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepBig)));
			echo '<li><a class="linkJumpBig" href="'.$this->web->page.$query.'" title="-'.$stepBig.'">';
			echo '<span class="arrow-w"></span><span class="arrow-w"></span></a></li>';
		}
		// jump back small step
		if ($this->curPageNum > $this->stepSmall / 2) {
			$stepSmall = ($this->curPageNum > $this->stepSmall ? $this->stepSmall : $this->curPageNum - 1);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepSmall)));
			echo '<li><a class="linkJumpSmall" href="'.$this->web->page.$query.'" title="-'.$stepSmall.'">';
			echo '<span class="arrow-w"></span></a></li>';
		}
		// direct accessible pages (1 2 3 4... links)
		$Count = 0;
		for ($i = $start; $i <= $end && (($i - 1) * $this->numRecPerPage < $this->numRec); $i++) {
			if ($numPage > 1) {
				if ($Count > 0) {
					echo ' ';
				}
				$Count++;
				if ($i == $this->curPageNum) {
					echo ' <li class="linkCurPageNum">';
				}
				else {
					$query = $this->web->addQuery(array($this->varNamePgNav => $i));
					echo '<li class="pages">';
					echo '<a class="linkJumpPage" href="'.$this->web->page.$query.'">';
				}
				echo $i; // page number
				if ($i == $this->curPageNum) {
					echo '</li>';
				}
				else {
					echo '</a></li>';
				}
			}
		}
		// jump forward small step
		if ($numPage > $this->curPageNum + $this->stepSmall / 2) {
			$stepSmall = ($numPage > ($this->curPageNum + $this->stepSmall) ? $this->stepSmall : $numPage - $this->curPageNum);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepSmall)));
			echo '<li><a class="linkJumpSmall" href="'.$this->web->page.$query.'" title="+'.$stepSmall.'">';
			echo '<span class="arrow-e"></span></a></li>';
		}
		// jump forward big step
		if ($numPage >= $this->curPageNum + $this->stepBig / 2) {
			$stepBig = ($numPage > $this->curPageNum + $this->stepBig ? $this->stepBig : $numPage - $this->curPageNum);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepBig)));
			echo '<li><a title="" class="linkJumpBig" href="'.$this->web->page.$query.'" title="+'.$stepBig.'">';
			echo '<span class="arrow-e"></span><span class="arrow-e"></span></a></li>';
		}
		// show number of records
		echo '<div class="numRec">';
		if ($this->curPageNum > $this->stepBig / 2) {
			echo '<div><a class="linkJumpSmall" style="width: 16px;"></a></div>';
		}
		if ($this->curPageNum > $this->stepSmall / 2) {
			echo '<div><a class="linkJumpBig" style="width: 16px;"></a></div>';
		}
		echo '<div>'.$this->numRec.' Datensätze</div>';
		echo '</div>';

		echo "</nav></ul>";
	}
}