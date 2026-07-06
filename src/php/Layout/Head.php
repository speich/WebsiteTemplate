<?php

namespace WebsiteTemplate\Layout;


/**
 * Class Head
 * Render the content of the HtmlHeadElement.
 */
abstract class Head
{
    /**
     * Render the content of the HTML head element.
     * @return string html
     */
    abstract public function render(): string;
}