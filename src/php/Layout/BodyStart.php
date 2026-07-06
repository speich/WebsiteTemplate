<?php

namespace WebsiteTemplate\Layout;


/**
 * Class BodyStart
 * Renderer for the HTML opening tags of the layout right after the body opening tag.
 *
 * @package LFI\Layout
 */
abstract class BodyStart
{


    /**
     * @return string
     */
    abstract public function render(): string;

}