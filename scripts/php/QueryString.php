<?php

namespace WebsiteTemplate;


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
    /** @var array query string names and values */
    private $queryVars;

    public $charset = 'UTF-8';

    /**
     * QueryString constructor.
     * By default the query string only contains keys that are whitelisted.
     * @param array|null $whitelist allowed keys in the query string
     */
    public function __construct($whitelist = null)
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
    private function intersect($queries, $whitelist) {
        // can't use array_intersect_keys() would remove duplicate keys, which are perfectly fine in a query string
        $arr = array();
        foreach ($whitelist as $key) {
            if (array_key_exists($key, $queries)) {
                $arr[$key] = $queries[$key];
            }
        }

        return $arr;
    }

    /**
     * Add query variables to the query string
     * $vars is expected to be a associative array with key values.
     * @param array $vars
     */
    public function add($vars)
    {
        $this->queryVars = array_merge($this->queryVars, $vars);
    }

    /**
     * Remove variables from the internal query string.
     * @param null $arr
     */
    public function remove($arr = null)
    {
        if ($arr === null) {
            $this->queryVars = array();
        }
        else {
            $keys = array_fill_keys($arr, null);
            $this->queryVars = array_diff_key($this->queryVars, $keys);
        }
    }

    /**
     * Returns an array with the whitelisted query variables
     * @return array
     */
    public function get()
    {
        return $this->queryVars;
    }

    /**
     * Returns the query string.
     * Returns the urlencoded string prefixed with a question mark. If there is no query an empty string is returned.
     * @param int $encType
     * @return string
     */
    public function getString($encType = PHP_QUERY_RFC1738) {
        $str = http_build_query($this->queryVars, $encType);

        return $str === '' ? '' : '?'.$str;
    }

    /**
     * Returns the variables and values of the query string as an array.
     *
     * With the argument $arrAdd variables and values can be added to the returned array without changing the internally
     * stored original query string read from the server. The variable names should used as the keys of the array and the
     * values as the array values.
     * With the argument $arrRemove variables can be removed from the returned array without changing the internally
     * stored original query string read from the server. The array should consist only of the variable names as the array values.
     * @param null|array $arrAdd keys and values to add
     * @param null|array $arrRemove keys to remove
     * @return array
     */
    public function with($arrAdd = null, $arrRemove = null)
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
     * Returns the URL-encoded query string.
     * Returns the string prefixed with a question mark. If there is no query an empty string is returned.
     * With the argument $arrInc variables and values can be included to the returned query string without changing the internally
     * stored original query string read from the server. The variable names should used as the keys of the array and the
     * values as the array values.
     * With the argument $arrExcl variables can be excluded from the returned query string without changing the internally
     * stored original query string read from the server. The array should consist only of the variable names as the array values.
     * @see http_build_query()
     * @param null|array $arrInc variables and values to add
     * @param null|array $arrExl variables to remove
     * @param int $encType
     * @return string
     */
    public function withString($arrInc = null, $arrExl = null, $encType = PHP_QUERY_RFC1738) {
        $str = http_build_query($this->with($arrInc, $arrExl), $encType);

        return $str === '' ? '' : '?'.$str;
    }
}