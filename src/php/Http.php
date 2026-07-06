<?php

namespace WebsiteTemplate;

use function count;
use function is_array;


/**
 * Helper class to parse HTTP responses.
 */
class Http
{
    /**
     * Extract the http response header into an associative array.
     * Returns headers with upper case of fir
     * @param array|string $header http raw response or array $http_response_header
     * @return array
     */
    public function parseHeader(array|string $header): array
    {
        // code adapted from by bsdnoobz http://stackoverflow.com/users/1396314/bsdnoobz
        $lines = is_array($header) ? $header : explode("\r\n", $header);
        //$lines = array_filter($lines, !'empty');
        $head[0] = trim(array_shift($lines));  // message is always first line in header
        preg_match("/HTTP\/[0-9\.]+\s+([0-9]+)/", $head[0], $code);
        $head[1] = (int)$code[1];
        foreach ($lines as $line) {
            $arr = explode(':', $line, 2);
            $key = $this->toCanonical($arr[0]);
            $val = count($arr) > 1 ? $arr[1] : '';  // in case of a redirect, a second http message is present
            $val = trim($val);
            if ($key === 'Set-Cookie') {
                $head['Set-Cookie'][] = $val;
            } else {
                $head[$key] = $val;
            }
        }

        return $head;
    }

    /**
     * Convert response header keys to canonical form.
     * @param string $string header key to convert
     */
    public function toCanonical(string $string): string
    {
        return implode('-', array_map('ucfirst', explode('-', $string)));
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