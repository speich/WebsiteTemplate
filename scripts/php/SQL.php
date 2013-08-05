<?php
namespace WebsiteTemplate;

/**
 * Helper class to provide some often used SQL funcionality.
 * @author Simon Speich
 */
class SQL {
	/** @var resource|null oracle database connection */
	private $db = null;

	/** @var integer number of records to display per page */
	private $numRecPerPage = 10;

	/** @var string character to separate a variable and its value in a string */
	private $varSeparator = '|';

	/**
	 * Instantiates the class SQL
	 * @param Db $db instance of Db
	 */
	public function __construct($db) {
		$this->db = $db;
	}

	/**
	 * Converts the provided array to an SQL ORDER BY clause.
	 * The input is expected to be a key value pair string|array separated by the $varSeparator containing
	 * the name of database colum to sort (including table name if necessary) and the sort order, e.g.: varNr|asc
	 * @param string|array $sort
	 * @return string SQL ORDER BY clause
	 */
	public function getSortSQL($sort) {
		$sql = '';
		$arr = is_array($sort) ? $sort : array($sort);
		foreach ($arr as $val) {
			$arrVal = explode($this->getVarSeparator(), $val);
			$arrVal[0] = preg_replace("/[^\w\.]/", '', $arrVal[0]);	// replace any non-word character, except a dot
			$arrVal[1] = preg_replace("/[^ascde]/i", '', $arrVal[1]);
			$sql.= $arrVal[0].' '.$arrVal[1].',';
		}
		$sql = rtrim($sql, ',');
		return " ORDER BY ".$sql;
	}

	/**
	 * Creates an SQL WHERE string to filter a query.
	 * The input is expected to be a key value pair separated by the $varSeparator containing
	 * the name of database colum to filter (including table name if necessary) and the filter value, e.g.: invNr|350
	 * The returned SQL contains the enumarated bind var :filter{i} to be used in bindFilter() below before executing the query.
	 * All input is sanitized.
	 * @param array|string $filter
	 * @return string SQL WHERE clause without the WHERE
	 */
	public function getFilterSQL($filter) {
		$sql = '';
		$count = 0;
		$arr = is_array($filter) ? $filter : array($filter);
		foreach ($arr as $val) {
			if ($val) {   // allows for skipping a filter by setting it to null
				$arrVal = explode($this->getVarSeparator(), $val);
				$arrVal[0] = preg_replace("/[^\w\.]/", '', $arrVal[0]);	// for security reasons replace any non-word character except a dot
				$arrVal[1] = preg_replace("/\W/", '', $arrVal[1]);			// for security we create binds, which can be used with bindFilter()
				if ($count > 0) {
					$sql.= " AND";
				}
				$sql.= " ".$arrVal[0]. " = :filter".$count;
			}
			$count++;
		}
		return $sql;
	}

	/**
	 * Binds the filter values to the prepared SQL
	 * @see getFilterSQL()
	 * @param resource $stmt
	 * @param array|string $filter
	 */
	public function bindFilter($stmt, $filter) {
		$count = 0;
		$arr = is_array($filter) ? $filter : array($filter);
		foreach ($arr as $val) {
			if (!is_null($val)) {   // allows for skipping a filter by setting it to null
				$arrVal = explode($this->getVarSeparator(), $val);
				$this->db->bind($stmt, 'filter'.$count, $arrVal[1]);
			}
			$count++;
		}
	}

	/**
	 * Returns a 2-dim array with a limited number of records
	 * Use for paged display. $Page = current page to get records.
	 * @param string $sql sql
	 * @param integer $page current page number
	 * @param array $arrBind variables to bind
	 * @param int $mode OCI_ASSOC|OCI_NUM
	 * @return array
	 */
	public function getDataPaged($sql, $page, $arrBind = null, $mode = null) {
		if (!$arrBind) { $arrBind = array(); }
		$recFrom = ($page - 1) * $this->numRecPerPage + 1;
		$recTo = $page * $this->numRecPerPage;
		$query = "SELECT * FROM (
				SELECT tblTemp.*, rowNum AS lowerBound FROM ($sql) tblTemp
				WHERE rowNum <= :recTo
			) WHERE :recFrom <= lowerBound";
		$stmt = $this->db->parse($query);
		$this->db->bind($stmt, 'recFrom', $recFrom);
		$this->db->bind($stmt, 'recTo', $recTo);
		foreach ($arrBind as $val) {
			$this->db->bind($stmt, $val[0], $val[1], $val[2]);
		}
		$this->db->execute($stmt);
		oci_set_prefetch($stmt, $this->numRecPerPage); // use php buffer about size of paged records
		oci_fetch_all($stmt, $results, 0, -1, ($mode || OCI_ASSOC) + OCI_RETURN_LOBS + OCI_FETCHSTATEMENT_BY_ROW);
		return $results;
	}

	/**
	 * Sets the number of records per page for paged display.
	 * @param integer $numRecPerPage
	 */
	public function setNumRecPerPage($numRecPerPage) { $this->numRecPerPage = $numRecPerPage; }

	/**
	 * Get the number of records per page for paged display.
	 * @return integer
	 */
	public function getNumRecPerPage() { return $this->numRecPerPage; }

	/**
	 * Sets the character that is used to separate key value pairs in a string.
	 * @param string $varSeparator character
	 */
	public function setVarSeparator($varSeparator) {
		$this->varSeparator = $varSeparator;
	}

	/**
	 * Returns the character that is used to separate key value pairs in a string.
	 * @return string
	 */
	public function getVarSeparator() {
		return $this->varSeparator;
	}
}