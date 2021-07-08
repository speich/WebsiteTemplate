<?php

namespace WebsiteTemplate;

use function is_array;


/**
 * Helper class to parse HTTP responses.
 */
class Http
{

    /**
     * Extract the http response header into an associative array.
     * Adds the http message (first line in header) as the first item with key = 0 and the code with key = 1. All
     * other headers have their name as the key, e.g. 'Content-Type'.
     * @param array|string $header http raw response or array $http_response_header
     * @return array
     */
    public function parseHeader($header): array
    {
        // code by bsdnoobz http://stackoverflow.com/users/1396314/bsdnoobz
        $lines = is_array($header) ? $header : explode("\r\n", $header);
        $head[0] = array_shift($lines);  // message is always first line in header
        preg_match("/HTTP\/[0-9\.]+\s+([0-9]+)/", $head[0], $code);
        $head[1] = (int)$code[1];
        foreach ($lines as $line) {
            $arr = explode(':', $line, 2);
            $key = $arr[0];
            $val = count($arr) > 1 ? $arr[1] : '';  // in case of a redirect, a second http message is present
            if ($key === 'Set-Cookie') {
                $head['Set-Cookie'][] = trim($val);
            } else {
                $head[$key] = trim($val);
            }
        }

        return $head;
    }

    /**
     * Decode the chunked-encoded string.
     * @param string $str
     * @return string
     */
    public function decodeChunked(string $str): string
    {
        // code by bsdnoobz http://stackoverflow.com/users/1396314/bsdnoobz
        for ($res = ''; !empty($str); $str = trim($str)) {
            $pos = strpos($str, "\r\n");
            $len = hexdec(substr($str, 0, $pos));
            $res .= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
        }

        return $res;
    }

    /**
     * Splits the http response into header and body.
     * @param string $str http response
     * @return array array with keys header and body
     */
    public function getHeaderAndBody(string $str): array
    {
        $pos = strpos($str, "\r\n\r\n");

        return [
            'header' => substr($str, 0, $pos),
            'body' => substr($str, $pos + 2),
        ];
    }
}