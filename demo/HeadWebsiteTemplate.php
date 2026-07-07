<?php


use WebsiteTemplate\Layout\Head;

/**
 * Class Head
 * Render the content of the HtmlHeadElement.
 */
class HeadWebsiteTemplate extends Head
{
    /**
     * Render the content of the HTML head element.
     * @return string html
     */
    public function render(): string
    {
        return '<meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link type="text/css" rel="stylesheet" href="/src/css/modern-normalize.min.css" media="all">
             <link type="text/css" rel="stylesheet" href="/demo/layout/css/layout.css" media="all">';
    }
}