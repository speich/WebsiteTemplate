<?php
/**
 * This file contains the class Controller.
 */

namespace WebsiteTemplate;

use stdClass;


/**
 * This class is used as a REST controller.
 * REST resources are transformed into a controller (first path segment) and an array of resources, e.g.:
 * controller.php/administration/user/1 would be stored as
 * $this->controller = 'administration' and $this->resources = array('user', 1);
 */
class Controller
{
    public $err;

    /** @var null|string http protocol */
    private $protocol = null;

    /** @var null|string http method */
    private $method = null;

    /** @var string|array|null array of path segments */
    private $resources = null;

    /** @var null|Header */
    private $header = null;

    /** @var bool respond with 404 resource not found */
    public $notFound = false;

    /** @var bool flush buffer every bytes */
    public $outputChunked = false;

    /** @var int chunk size for flushing */
    public $chunkSize = 4096;    // = 1 KB

    /**
     * Constructs the controller instance.
     * If you don't want the first path segment to be set as the controller, set $useController to false.
     * @param Header $header
     * @param Error $error
     * @param Boolean $useController use first path segment as controller?
     */
    public function __construct(Header $header, Error $error, $useController = true)
    {
        $this->header = $header;
        $this->protocol = $_SERVER["SERVER_PROTOCOL"];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->resources = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $this->resources;
        $this->err = $error;
    }

    /**
     * Converts PHP input parameters to an object
     * Object properties correspond with request data
     * @param bool $json handle post data as json
     * @return stdClass |null
     */
    public function getDataAsObject($json = false)
    {
        switch ($this->method) {
            case 'POST':
                if ($json) {
                    $arr = json_decode(file_get_contents('php://input'));
                } else {
                    // note: Make sure you set the correct Content-Type when doint a xhr POST
                    $arr = $_POST;
                }
                break;
            case 'PUT':
                $data = file_get_contents('php://input');
                if ($json) {
                    $arr = json_decode($data);
                } else {
                    parse_str($data, $arr);
                }
                break;
            case 'GET':
                $arr = $_GET;
                break;
            case 'DELETE':
                if ($_SERVER['QUERY_STRING'] !== '') {
                    // Delete has no body, but a query string is possible
                    parse_str($_SERVER['QUERY_STRING'], $arr);
                } else {
                    $arr = array();
                }
                break;
            default:
                $arr = array();
        }

        return count($arr) > 0 ? (object)$arr : null;
    }

    /**
     * Returns the http method, e.g. GET, POST, PUT or DELETE
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns the currently used http protocol.
     * @return null|string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Returns the path split into segments.
     * Contains any client-provided pathname information trailing the actual script filename but preceding the query string.
     * Returns null, if no path information is available. If path is only a slash and $asString is false, an array with and empty string is returned.
     * @param bool $asString return a string instead of an array
     * @return array|string|null
     */
    public function getResource($asString = false)
    {
        $resources = $this->resources;
        if (!is_null($resources) && $asString === false) {
            $resources = trim($resources, '/');
            $resources = explode('/', $resources);
        }

        return $resources;
    }

    /**
     * Prints the header section of the HTTP response.
     * Sets the Status Code, Content-Type and additional headers set optionally.
     */
    public function printHeader()
    {
        $this->printStatus();
        $headers = $this->header->get();
        $contentType = $this->notFound ? 'text/html' : $this->header->getContentType();
        header('Content-Type: '.$contentType.'; '.$this->header->getCharset());
        foreach ($headers as $key => $value) {
            header($key.': '.$value);
        }
    }

    /**
     * Sets HTTP header status code
     */
    public function printStatus()
    {
        $headers = $this->header->get();
        $headers = array_change_key_case($headers);

        // server error
        if (count($this->err->get()) > 0) {
            header($this->getProtocol().' 500 Internal Server Error');
        } // resource not found
        elseif ($this->notFound) {
            header($this->getProtocol().' 404 Not Found');
        } // resource found and processed
        elseif ($this->getMethod() === 'POST' && !array_key_exists('content-disposition', $headers)) {
            // IE/Edge fail to download with status 201
            header($this->getProtocol().' 201 Created');
        } // range response
        elseif (array_key_exists('content-range', $headers)) {
            header($this->getProtocol().' 206 Partial Content');
        } else {
            header($this->getProtocol().' 200 OK');
        }
    }

    /**
     * Prints the body section of the HTTP response.
     * Prints the body in chunks if outputChunked is set to true.
     * @param string $data response body
     */
    public function printBody($data = null)
    {
        // an error occurred
        if (count($this->err->get()) > 0) {
            if ($this->header->getContentType() === 'application/json') {
                echo $this->err->getAsJson();
            } else {
                echo $this->err->getAsString();
            }
        } // response contains data
        elseif ($data) {
            if ($this->outputChunked) {
                $chunks = str_split($data, $this->chunkSize);
                foreach ($chunks as $chunk) {
                    echo $chunk;
                    ob_flush();
                    flush();
                }
            } else {
                echo $data;
            }
        } // no response, 200 ok only // TODO: should be 204 No Content
        else {
            if ($this->header->getContentType() === 'application/json') {
                echo '{}';
            }
        }
    }
}