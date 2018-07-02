<?php

namespace WebsiteTemplate;


/**
 * Class QueryString
 * Class to work with query strings.
 * All methods related to adding something expect the passed array to have keys and values. All methods removing something expect
 * the passed array only to contains values.
 * Security note: It is your own responsibility to sanitize (by using htmlspecialchars) the query variables before using them.
 * This class just reads the query variables from the server into an array.
 * @package LFI
 */
class QueryString
{
    /** @var array query string names and values */
    private $queryVars;

    public function __construct()
    {
        parse_str($_SERVER['QUERY_STRING'], $queries);
        $this->queryVars = $queries;
    }

    /**
     * @param $arr
     */
    public function add($arr)
    {
        $this->queryVars = array_merge($this->queryVars, $arr);
    }

    public function remove($arr = null)
    {
        if ($arr === null) {
            $this->queryVars = [];
        }
        else {
            $keys = array_fill_keys($arr, null);
            $this->queryVars = array_diff_key($this->queryVars, $keys);
        }
    }

    public function get()
    {
        return $this->queryVars;
    }

    /**
     * Returns the query string.
     * Returns the string prefixed with a question mark. If there is no query an empty string is returned.
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