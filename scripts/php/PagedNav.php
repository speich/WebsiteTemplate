<?php

class PagedNav {
	private $numRecPerPage;	         		// number of records per page, used to calculate the number of pages in navigation
	private $numRec;			      			// total number of records with current query, used to calculate number of pages
	private $curPageNum;		         		// Current page to display.
	private $range = 6;		      			// range of pages in paged navigation, must be an even number
	private $stepSmall = 10;         		// [-10] [+10] backward - forward jump, must be an even number
	private $stepBig = 50;	         		// [-50] [+50] backward - forward jump, must be an even number
	private $lan = "de";				         // language of text navigation
	private $varNamePgNav = 'pgNav';	      // name of variable in querystring to set page number
	private $formMethod = 'GET';			   // default method is GET, e.g use querystring

	/**
	 * Constructs the paged navigation.
	 *
	 * Total number of records with given query, e.g. with WHERE clause included.
	 * @param Website $web instance of Website
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
	 * Set post method for paged navigation.
	 * Links can either be GET or POST
	 * @param string $method
	 */
	public function setMethod($method) {
		$this->formMethod = $method;
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

	public function setLan($lan) { $this->lan = $lan; }

	/**
	 * Outputs HTML paged data navigation.
	 */
	public function printNav() {
		switch ($this->lan) {
			case 'fr':
				$lanStr01 = ' inscriptions';
				$lanStr02 = ' inscription';
				$lanStr03 = ' pages: ';
				$lanStr04 = ' page';
				$lanStr05 = 'Résultat de la recherche: ';
				$lanStr06 = '';
				break;
			case 'it':
				$lanStr01 = ' iscrizioni';
				$lanStr02 = ' inscriptione';
				$lanStr03 = ' pagine: ';
				$lanStr04 = ' pagina';
				$lanStr05 = 'Risultato della ricerca: ';
				$lanStr06 = '';
				break;
			case 'en':
				$lanStr01 = ' entries';
				$lanStr02 = ' entry';
				$lanStr03 = ' pages: ';
				$lanStr04 = ' page';
				$lanStr05 = 'search result: ';
				$lanStr06 = 'on';
				break;
			default:
				$lanStr01 = ' Fotos';
				$lanStr02 = ' Foto';
				$lanStr03 = ' Seiten: ';
				$lanStr04 = ' pro Seite';
				$lanStr05 = ' sortiert nach: ';
				break;
		}

		// calc total number of pages
		$numPage = ceil($this->numRec / $this->numRecPerPage);
		// lower limit (start)
		$start = 1;
		if ($this->curPageNum - $this->range/2 > 0) { $start = $this->curPageNum - $this->range/2; }
		// upper limit (end)
		$end = $this->curPageNum + $this->range / 2;
		if ($this->curPageNum + $this->range/2 > $numPage) { $End = $numPage;	}
		// special cases
		if ($numPage < $this->range) { $end = $numPage; }
		else if ($end < $this->range) { $end = $this->range; }

		echo '<div scripts="pagedNavBar">';
		// jump back big step
		if ($this->curPageNum > $this->stepBig / 2) { // && $this->curPageNum >= $this->stepBig + $this->stepSmall) {
			$stepBig = ($this->curPageNum > $this->stepBig ? $this->stepBig : $this->curPageNum - 1);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepBig)));
			echo '<div><a scripts="linkJumpBig" href="'.$this->web->getPage().$query.'">';
			echo '<img src="'.$this->web->getWebRoot().'layout/images/icon_backfast.gif" alt="Icon back" title="schnell Rückwärts blättern [-'.$stepBig.']"/></a></div>';
		}
		// jump back small step
		if ($this->curPageNum > $this->stepSmall / 2) {
			$stepSmall = ($this->curPageNum > $this->stepSmall ? $this->stepSmall : $this->curPageNum - 1);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum - $stepSmall)));
			echo '<div><a scripts="linkJumpSmall" href="'.$this->web->getPage().$query.'">';
			echo '<img src="'.$this->web->getWebRoot().'layout/images/icon_back.gif" alt="Icon back" title="Rückwärts blättern [-'.$stepSmall.']"/></a></div>';
		}
		// direct accessible pages (1 2 3 4... links)
		$Count = 0;
		for ($i = $start; $i <= $end && (($i-1) * $this->numRecPerPage < $this->numRec); $i++) {
			if ($numPage > 1) {
				if ($Count > 0) { echo ' '; }
				$Count++;
				if ($i == $this->curPageNum) { echo ' <div scripts="linkCurPageNum">'; }
				else {
					$query = $this->web->addQuery(array($this->varNamePgNav => $i));
					echo '<div scripts="pages">';
					echo '<a scripts="linkJumpPage" href="'.$this->web->getPage().$query.'">';
				}
				echo $i;	// page number
				if ($i == $this->curPageNum) { echo '</div>'; }
				else { echo '</a></div>'; }
			}
		}
		// jump forward small step
		if ($numPage > $this->curPageNum + $this->stepSmall / 2) {
			$stepSmall = ($numPage > ($this->curPageNum + $this->stepSmall) ? $this->stepSmall : $numPage - $this->curPageNum);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepSmall)));
			echo '<div><a scripts="linkJumpSmall" href="'.$this->web->getPage().$query.'">';
			echo '<img src="'.$this->web->getWebRoot().'layout/images/icon_forward.gif" alt="Icon forward" title="Vorwärts blättern [+'.$stepSmall.']"/></a></div>';
		}
		// jump forward big step
		if ($numPage >= $this->curPageNum + $this->stepBig / 2) {
			$stepBig = ($numPage > $this->curPageNum + $this->stepBig ? $this->stepBig : $numPage - $this->curPageNum);
			$query = $this->web->addQuery(array($this->varNamePgNav => ($this->curPageNum + $stepBig)));
			echo '<div><a scripts="linkJumpBig" href="'.$this->web->getPage().$query.'">';
			echo '<img src="'.$this->web->getWebRoot().'layout/images/icon_forwardfast.gif" alt="Icon forward" title="schnell Vorwärts blättern [+'.$stepBig.']"/></a></div>';
		}
		// show number of records
		echo '<div scripts="numRec">';
		if ($this->curPageNum > $this->stepBig / 2) {
			echo '<div><a scripts="linkJumpSmall" style="width: 16px;"></a></div>';
		}
		if ($this->curPageNum > $this->stepSmall / 2) {
			echo '<div><a scripts="linkJumpBig" style="width: 16px;"></a></div>';
		}
		echo '<div>'.$this->numRec.' Datensätze</div>';
		echo '</div>';

		echo "</div>\n";
	}
}

?>