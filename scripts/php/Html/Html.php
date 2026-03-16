<?php

namespace WebsiteTemplate\Html;

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
    /** @var bool|string html id attribute */
    protected string|bool $id = false;

    /** @var array html classes */
    private array $cssClass = [];

    /** @var string title attribute */
    protected string $title = '';

    /** @var string style attribute */
    protected string $cssStyle;

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
     * Add one or several CSS classes.
     * Adds one or more classes to the CSS attribute. Existing classes with the same name are overwritten.
     * @param string ...$name
     */
    public function addCssClass(...$name): void
    {
        $this->cssClass = array_unique(array_merge($this->cssClass, $name));
    }

    /**
     * Return the class attribute of a HTMLElement.
     * Returns the
     * @return string HTML class attribute string
     */
    public function renderCssClass(): string
    {
        return count($this->cssClass) === 0 ? '' : ' class="'.implode($this->cssClass).'"';
    }

    /**
     * Set the style attribute of a HTMLElement.
     * @param string $style
     */
    public function setCssStyle(string $style): void
    {
        $this->cssStyle = $style;
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