<?php

namespace WebsiteTemplate\Html;

/**
 * Base class to create HTML snippets.
 *
 * This abstract base class defines a number of attributes and methods
 * to deal with HTMLAttributes that are shared by all HTMLElements,
 * such as the id, name and class attribute.
 */
abstract class Html
{
    use CssClassTrait;
    use CssBlockTrait;

    /** @var string|null html id attribute */
    public ?string $id = null;

    /** @var string|null title attribute */
    public ?string $title = null;

}