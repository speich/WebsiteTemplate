<?php

namespace WebsiteTemplate;


use function array_key_exists;
use function is_array;


/**
 * Class QueryString
 * Class to work with query strings.
 * Reads the query string from the server. Allowed keys can be whitelisted.
 * All methods related to adding something expect the passed array to have keys and values. All methods removing something expect
 * the passed array only to contains values.
 * @package LFI
 */
class QueryString
{
    /** @var array names and values of the query string */
    private array $queryVars;

    /**
     * QueryString constructor.
     * By default, the query string only contains keys that are whitelisted.
     * @param array $whitelist allowed keys in the query string
     */
    public function __construct(array $whitelist = [])
    {
        parse_str($_SERVER['QUERY_STRING'], $queries);
        if (is_array($whitelist)) {
            $queries = $this->intersect($queries, $whitelist);
        }
        $this->queryVars = $queries;
    }

    /**
     * Only allow keys that are whitelisted
     * @param array $queries key-values from query string
     * @param array $whitelist keys to be allowed
     * @return array
     */
    private function intersect(array $queries, array $whitelist): array
    {
        // can't use array_intersect_keys() would remove duplicate keys, which are perfectly fine in a query string
        $arr = [];
        foreach ($whitelist as $key) {
            if (array_key_exists($key, $queries)) {
                $arr[$key] = $queries[$key];
            }
        }

        return $arr;
    }

    /**
     * Add query variables to the query string.
     * $vars is expected to be a associative array with key values.
     * @param array $vars keys and values
     */
    public function add(array $vars): void
    {
        $this->queryVars = array_merge($this->queryVars, $vars);
    }

    /**
     * Remove variables from the internal query string.
     * @param null $arr
     */
    public function remove($arr = null): void
    {
        if ($arr === null) {
            $this->queryVars = [];
        } else {
            $keys = array_fill_keys($arr, null);
            $this->queryVars = array_diff_key($this->queryVars, $keys);
        }
    }

    /**
     * Returns an array with the whitelisted query variables
     * @return array
     */
    public function get(): array
    {
        return $this->queryVars;
    }

    /**
     * Returns the query string.
     * Returns the urlencoded string prefixed with a question mark. If there is no query an empty string is returned.
     * @param ?int $encType by default PHP_QUERY_RFC1738
     * @return string
     */
    public function getString(?int $encType = null): string
    {
        $encType = $encType ?? PHP_QUERY_RFC1738;
        $str = http_build_query($this->queryVars, $encType);
        // http_build_query returns &myVar[] as &myVar[0]
        $str = preg_replace('/\%5B\d+\%5D/', '%5B%5D', $str);

        return $str === '' ? '' : '?'.$str;
    }

    /**
     * Returns the URL-encoded query string.
     * Returns the string prefixed with a question mark. If there is no query an empty string is returned.
     * With the argument $arrInc variables and values can be included to the returned query string without changing the internally
     * stored original query string read from the server. The variable names should used as the keys of the array and the
     * values as the array values.
     * With the argument $arrExcl variables can be excluded from the returned query string without changing the internally
     * stored original query string read from the server. The array should consist only of the variable names as the array values.
     * @param ?array $arrInc variables and values to add
     * @param ?array $arrExl variables to remove
     * @param ?int $encType by default PHP_QUERY_RFC1738
     * @return string
     * @see http_build_query()
     */
    public function withString(array $arrInc = null, array $arrExl = null, int $encType = null): string
    {
        $encType = $encType ?? PHP_QUERY_RFC1738;
        $str = http_build_query($this->with($arrInc, $arrExl), $encType);
        // http_build_query returns &myVar[] as &myVar[0]
        $str = preg_replace('/\%5B\d+\%5D/', '%5B%5D', $str);

        return $str === '' ? '' : '?'.$str;
    }

    /**
     * Returns the variables and values of the query string as an array.
     *
     * With the argument $arrAdd variables and values can be added to the returned array without changing the internally
     * stored original query string read from the server. The variable names should be used as the keys of the array and the
     * values as the array values.
     * With the argument $arrRemove, variables can be removed from the returned array without changing the internally
     * stored original query string read from the server. The array should consist only of the variable names as the array values.
     * @param array|null $arrAdd keys and values to add
     * @param array|null $arrRemove keys to remove
     * @return array
     */
    public function with(array $arrAdd = null, array $arrRemove = null): array
    {
        $vars = $this->queryVars;
        if ($arrAdd !== null) {
            $vars = array_merge($vars, $arrAdd);
        }
        if ($arrRemove !== null) {
            $keys = array_fill_keys($arrRemove, null);
            $vars = array_diff_key($vars, $keys);
        }

        return $vars;
    }

    /**
     * Test if all key and values provided, exist in the query string.
     * If $vars contains keys and values, they are tested against the query variables and values.
     * If $vars contains only values, the values are tested against the query variables.
     * @param array $vars
     * @return bool
     */
    public function in(array $vars): bool
    {
        if (isset($vars[0])) {  // simplified and opinionated check if array is not an associative
            foreach ($vars as $val) {
                if (!array_key_exists($val, $this->queryVars)) {

                    return false;
                }
            }
        } else {
            foreach ($vars as $key => $val) {
                if (!array_key_exists($key, $this->queryVars) || $this->queryVars[$key] !== $val) {

                    return false;
                }
            }
        }

        return true;
    }
}