<?php

namespace WebsiteTemplate\Html;

use WebsiteTemplate\CssBemTrait;
use WebsiteTemplate\CssClassTrait;
use function count;

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
    use CssBemTrait;

    /** @var bool|string html id attribute */
    protected string|bool $id = false;

    /** @var string title attribute */
    protected string $title = '';

    /**
     * Set the id attribute of a HTMLElement.
     * @param int|string $id
     */
    public function setId(int|string $id): void
    {
        $this->id = $id;
    }

    /**
     * Return the id attribute of a HTMLElement.
     * @return string|bool id or false
     */
    public function getId(): bool|string
    {
        if ($this->id) {
            return $this->id;
        }

        return false;
    }

    /**
     * Get the title attribute.
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title attribute
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}