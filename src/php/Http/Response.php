<?php

namespace WebsiteTemplate\Http;

use function array_key_exists;
use function str_split;

/**
 * This class handles the outgoing HTTP response.
 */
class Response
{
    /** @var bool respond with 404 resource not found */
    public bool $notFound = false;
    /** @var bool flush buffer after every $chunkSize bytes */
    public bool $outputChunked = false;
/** @var int chunk size for flushing */
    public int $chunkSize = 4096;
    /** @var Header */
    private Header $header;
    /** @var int The HTTP status code */
    private int $statusCode = 0;    // = 1 KB

    /**
     * Constructs the response instance.
     * @param Header $header
     */
    public function __construct(Header $header)
    {
        $this->header = $header;
    }

    /**
     * Set a specific HTTP status code for the response.
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Prints the header section of the HTTP response.
     * Sets the Status Code, Content-Type, and additional headers set optionally.
     */
    public function printHeader(): void
    {
        $this->printStatus();
        $headers = $this->header->get();
        header('Content-Type: '.$this->header->getContentType().'; charset='.$this->header->getCharset());
        foreach ($headers as $key => $value) {
            header($key.': '.$value);
        }
    }

    /**
     * Sets HTTP header status code
     */
    public function printStatus(): void
    {
        $headers = $this->header->get();
        $headers = array_change_key_case($headers);

        // 1. Explicitly set status code overrides legacy behavior
        if ($this->statusCode > 0) {
            http_response_code($this->statusCode);
        } // 2. Legacy fallback: resource not found
        elseif ($this->notFound) {
            header($this->getProtocol().' 404 Not Found');
            $this->statusCode = 404;
        } // 3. Legacy fallback: resource found and processed
        elseif (!array_key_exists('content-disposition', $headers) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            // IE/Edge fail to download with status 201
            header($this->getProtocol().' 201 Created');
            $this->statusCode = 201;
        } // 4. Legacy fallback: range response
        elseif (array_key_exists('content-range', $headers)) {
            header($this->getProtocol().' 206 Partial Content');
            $this->statusCode = 206;
        } // 5. Default
        else {
            header($this->getProtocol().' 200 OK');
            $this->statusCode = 200;
        }
    }

    /**
     * Get the HTTP protocol from the server.
     * @return string
     */
    private function getProtocol(): string
    {
        return $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    }

    /**
     * Prints the body section of the HTTP response.
     * Prints the body in chunks if outputChunked is set to true.
     * @param string|null $data response body
     */
    public function printBody(?string $data = null): void
    {
        // response contains data
        if ($data !== null && $data !== '') {
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
        } // no response data provided
        else {
            // Safely replaces the 200 OK header with 204 No Content if output hasn't been flushed
            // Ensures we do not accidentally overwrite a deliberate 404 or 500 error code!
            if ($this->statusCode === 200 && !headers_sent()) {
                header($this->getProtocol().' 204 No Content', true, 204);
                $this->statusCode = 204;
            }
        }
    }
}