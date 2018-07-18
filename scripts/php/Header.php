<?php

namespace WebsiteTemplate;

/**
 * Helper class to work with HTTP headers.
 */
class Header
{

    /** @var string $contentType header default MIME type set to text/html */
    private $contentType = 'text/html';

    /** @var string $charset default characterset set to utf-8 */
    private $charset = 'utf-8';

    /** @var array contains additional response headers */
    private $headers = array();

    /** @var array $contentTypes MIME types lookup */
    private $contentTypes = array(
        'text' => 'text/plain',
        'csv' => 'text/csv',
        'json' => 'application/json',
        'pdf' => 'application/pdf',
        'html' => 'text/html',
        'svg' => 'image/svg+xml',
    );

    /**
     * Set the MIME type of the header.
     * Abbreviations can be used instead of full MIME type for some content types.
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $contentType = array_key_exists($contentType,
            $this->contentTypes) ? $this->contentTypes[$contentType] : $contentType;
        $this->contentType = $contentType;
    }

    /**
     * Returns the content type (MIME type).
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Extracts the range start and end from the header.
     * Returns an array with start and end key or null if range header is not sent.
     * @return array|null
     */
    public function getRange()
    {
        if (isset($_SERVER['HTTP_RANGE'])) {
            $arr = explode('-', substr($_SERVER['HTTP_RANGE'], 6)); // e.g. items=0-24

            return array('start' => $arr[0], 'end' => $arr[1]);
        } else {
            return null;
        }
    }

    /**
     * Creates the range header.
     * Returns an array where the first item ist the name of the range header and second the value.
     * Note: Uses items instead of bytes as the ranges-specifier to work with dstore
     * @param array $arrRange array containing start and end
     * @param int $numRec total number of items
     * @return array
     */
    public function createRange($arrRange, $numRec)
    {
        $end = $arrRange['end'] > $numRec ? $numRec : $arrRange['end'];

        return array('Content-Range', 'items='.$arrRange['start'].'-'.$end.'/'.$numRec);
    }

    /**
     * Returns the character set
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Add a header to the headers array.
     * Note: Header with same name will be overwritten no matter its case-insensitively.
     * @param $name
     * @param $value
     */
    public function add($name, $value)
    {
        $this->headers = array_filter($this->headers, function ($key, $name) {
            return strtolower($key) !== strtolower($name);
        }, ARRAY_FILTER_USE_KEY);
        $this->headers[$name] = $value;
    }

    /**
     * Set header disposition to attachment forcing browser to offer download dialog.
     * Note: Content type has to be set separately.
     * @param string $fileName file path
     * @param string $fileExtension
     */
    public function addDownload($fileName, $fileExtension)
    {
        $this->add('Expires', 0);
        $this->add('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $this->add('Content-Disposition', 'attachment; filename="'.$fileName.'.'.$fileExtension.'"');
    }

    /**
     * Returns the array containing the headers.
     * @return array
     */
    public function get()
    {
        return $this->headers;
    }
}