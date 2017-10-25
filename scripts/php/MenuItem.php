<?php
/**
 * This file a class to create items for the navigation menu.
 * @author Simon Speich
 */

namespace WebsiteTemplate;

/**
 * Class to create menu items.
 * MenuItems are part of a simple recursive php menu.
 * @see Menu
 */
class MenuItem
{

    /**  @var null|string id */
    public $id = null;

    /** @var null|string parent id */
    public $parentId = null;

    /** @var string text of link */
    public $linkTxt = '';

    /** @var string url of link */
    public $linkUrl = '';

    /** @var null|string CSS class name */
    private $cssClass = null;

    /** @var bool render children */
    private $childToBeRendered = false;

    /** @var bool is item active */
    private $active = false;

    /** @var bool has item an active child */
    private $hasActiveChild = false;

    /** @var string link target */
    public $linkTarget = '';

    /**
     * Constructs the menu item.
     * @param integer|string $id unique id
     * @param integer|string $parentId id of parent item
     * @param string $linkTxt link text
     * @param string [$linkUrl] link url
     */
    public function __construct($id, $parentId, $linkTxt, $linkUrl = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->linkTxt = $linkTxt;
        $this->linkUrl = $linkUrl;
    }

    /** Get item property if children will be rendered */
    public function getChildToBeRendered()
    {
        return $this->childToBeRendered;
    }

    /**
     * Set item property if children will be rendered.
     * @param bool [$ChildToBeRendered]
     */
    public function setChildToBeRendered($childrenToBeRendered = true)
    {
        $this->childToBeRendered = $childrenToBeRendered;
    }

    /**
     * Set item to be active.
     * @param bool [$active]
     */
    public function setActive($active = true)
    {
        $this->active = $active;
    }

    /**
     * Get item active status.
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $hasActiveChild
     */
    public function setHasActiveChild($hasActiveChild)
    {
        $this->hasActiveChild = $hasActiveChild;
    }

    /**
     * @return boolean
     */
    public function getHasActiveChild()
    {
        return $this->hasActiveChild;
    }

    /**
     * Adds a css class to the item.
     * Allows to have multiple CSS classes per item.
     * @param string $name CSS class name
     */
    public function addCssClass($name)
    {
        if (!is_null($this->cssClass)) {
            $this->cssClass .= ' ';    // multiple classes have to be separated by a space
        }
        $this->cssClass .= $name;
    }

    /**
     * Returns the css class string
     * @return string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * Sets the css class string.
     * @param null|string $cssClass
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }
}