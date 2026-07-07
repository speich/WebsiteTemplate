<?php

namespace WebsiteTemplate\Http;

use JsonException;
use stdClass;
use function count;
use function is_array;

/**
 * This class handles the incoming HTTP request.
 * REST resources are transformed into an array of resources, e.g.:
 * index.php/administration/user/1 would be stored as array('administration', 'user', 1)
 */
class Request
{
    /** @var string http protocol */
    private string $protocol;

    /** @var string http method */
    private string $method;

    /** @var string|null trailing path information from $_SERVER['PATH_INFO'] */
    private ?string $resources;

    /**
     * Constructs the Request instance.
     */
    public function __construct()
    {
        $this->protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->resources = $_SERVER['PATH_INFO'] ?? null;
    }

    /**
     * Converts PHP input parameters to an object.
     * Object properties correspond with request data.
     * @param bool $json handle post data as json
     * @return stdClass|null
     * @throws JsonException
     */
    public function getDataAsObject(bool $json = false): ?stdClass
    {
        // Note on types when using json_decode():
        // Values true, false and null are returned as TRUE, FALSE and NULL respectively.
        // NULL is returned if the JSON cannot be decoded or if the encoded data is deeper than the recursion limit
        $data = null;

        switch ($this->method) {
            case 'POST':
                if ($json) {
                    $data = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
                } else {
                    // note: Make sure you set the correct Content-Type when doing a xhr POST
                    $data = $_POST;
                }
                break;
            case 'PUT':
                $data = $this->getInput($json);
                break;
            case 'GET':
                $data = $_GET;
                break;
            case 'DELETE':
                if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
                    $data = [];
                    parse_str($_SERVER['QUERY_STRING'], $data);
                } else {
                    $data = $this->getInput($json);
                }
                break;
        }

        if (is_array($data)) {
            $data = count($data) > 0 ? (object)$data : null;
        }

        return $data;
    }

    /**
     * Read php input stream
     * @param bool $json
     * @return object|array mixed
     * @throws JsonException
     */
    private function getInput(bool $json): object|array
    {
        $input = file_get_contents('php://input');
        if ($input !== '' && $json === true) {
            $data = json_decode($input, false, 512, JSON_THROW_ON_ERROR);
        } else {
            parse_str($input, $data);
        }

        return $data;
    }

    /**
     * Returns the http method, e.g., GET, POST, PUT, or DELETE
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the currently used http protocol.
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * Returns the path split into segments.
     * Contains any client-provided pathname information trailing the actual script filename but preceding the query string.
     * Returns null if no path information is available. If the path is only a slash and $asString is false, an array with an empty string is returned.
     * @param ?bool $asString return a string instead of an array
     * @return array|string|null
     */
    public function getResource(?bool $asString = null): array|string|null
    {
        $resources = $this->resources;
        if ($resources !== null && $asString !== true) {
            $resources = trim($resources, '/');
            $resources = explode('/', $resources);
        }

        return $resources;
    }
}