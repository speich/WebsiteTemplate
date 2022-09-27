<?php


use WebsiteTemplate\QueryString;
use PHPUnit\Framework\TestCase;

final class QueryStringTest extends TestCase
{

    public string $queryStr = 'test1=blabla&var2=val2&test2=blabla2&var1=val1';

    /**
     * Check if all keys and values occur also in the query string.
     * @return void
     */
    public function testKeyValueIn(): void
    {
        $arr = ['var1' => 'val1', 'var2' => 'val2'];
        $_SERVER['QUERY_STRING'] = $this->queryStr;
        $query = new QueryString();
        $this->assertTrue($query->in($arr));
    }

    /**
     * Check if all values occur in the query string as keys.
     * @return void
     */
    public function testKeyIn(): void
    {
        $arr = ['var1', 'var2'];
        $_SERVER['QUERY_STRING'] = $this->queryStr;
        $query = new QueryString();
        $this->assertTrue($query->in($arr));
    }
}
