<?php

namespace WebsiteTemplate\Layout;

/**
 * Class BodyEnd
 * Renderer for the HTML closing tags of the layout before the body closing tag.
 *
 * @package LFI\Layout
 */
abstract class BodyEnd
{


    /**
     * @return string
     */
    abstract public function render(): string;


}