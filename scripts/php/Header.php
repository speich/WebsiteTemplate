<?php
namespace WebsiteTemplate;

/**
 * Helper class to work with HTTP headers.
 */
class Header {

	private $charset = 'utf-8';

	private $headers = array();

	/** @var array $contentTypes mime types */
	private $contentTypes = array(
		'text' => 'text/plain',
		'csv' => 'text/csv',
		'json' => 'application/json',
		'pdf' => 'application/pdf',
		'html' => 'text/html',
		'svg'	=> 'image/svg+xml'
	);

	/**
	 * Returns full content type by abbreviation.
	 * Returns false if abbreviation (key in array) is not found.
	 * @param string $type
	 * @return string|false
	 */
	public function getContentType($type) {
		return array_key_exists($type, $this->contentTypes) ? $this->contentTypes[$type] : false;
	}

	/**
	 * Extracts the range start and end from the header.
	 * Returns an array with start and end key or null if range header is not sent.
	 * @return array|null
	 */
	public function getRange() {
		if (isset($_SERVER['HTTP_RANGE'])) {
			$arr = explode('-', substr($_SERVER['HTTP_RANGE'], 6)); // e.g. items=0-24
			return array('start' => $arr[0], 'end' => $arr[1]);
		}
		else {
			return null;
		}
	}

	/**
	 * Creates the range header string.
	 * @param array $arrRange array containing start and end
	 * @param int $numRec total number of items
	 * @return string
	 */
	public function createRange($arrRange, $numRec) {
		return 'Content-Range: items '.$arrRange['start'].'-'.$arrRange['end'].'/'.$numRec;
	}

	/**
	 * Returns the character set
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Add a header to the headers array.
	 * @param {String} $header header string
	 */
	public function add($header) {
		array_push($this->headers, $header);
	}

	/**
	 * Returns the array containing headers.
	 * @return array
	 */
	public function get() {
		return $this->headers;
	}
}