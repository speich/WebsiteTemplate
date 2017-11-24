<?php

namespace WebsiteTemplate\html;

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
    protected $id = false;

    /** @var array html class attribute */
    private $cssClass = array();

    /** @var string title attribute */
    protected $title = '';

    /**
     * Set the id attribute of a HTMLElement.
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return the id attribute of a HTMLElement.
     * @return string|bool id or false
     */
    public function getId()
    {
        if ($this->id) {
            return $this->id;
        } else {
            return false;
        }
    }

    /**
     * Set the class attribute of a HTMLElement.
     * Note: Always overwrites existing classes.
     * TODO: allow to append
     * @param string $class
     */
    public function setCssClass($class)
    {
        $this->cssClass = array_unique(array_merge($this->cssClass, (array)$class));
    }

    /**
     * Return the class attribute of a HTMLElement.
     * Returns the
     * @return string html class attribute string
     */
    public function renderCssClass()
    {
        $str = implode(' ', $this->cssClass);

        return $str === '' ? '' : ' class="'.$str.'"';
    }

    /**
     * Set the style attribute of a HTMLElement.
     * @param string $style
     */
    public function setCssStyle($style)
    {
        $this->cssStyle = $style;
    }

    /**
     * Get the title attribute.
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title attribute
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

}