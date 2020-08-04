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
    /**  @var string id */
    public $id;

    /** @var string parent id */
    public $parentId;

    /** @var string text of link */
    public $linkTxt = '';

    /** @var string url of link */
    public $linkUrl = '';

    /** @var string CSS class name */
    private $cssClass = '';

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
     * @param int|string $id unique id
     * @param int|string $parentId id of parent item
     * @param string $linkTxt link text
     * @param ?string $linkUrl link url
     */
    public function __construct($id, $parentId, $linkTxt, $linkUrl = null)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->linkTxt = $linkTxt;
        $this->linkUrl = $linkUrl;
    }

    /** Get item property if children will be rendered */
    public function getChildToBeRendered(): bool
    {
        return $this->childToBeRendered;
    }

    /**
     * Set item property if children will be rendered.
     * @param bool $childrenToBeRendered
     */
    public function setChildToBeRendered(?bool $childrenToBeRendered = null): void
    {
        $this->childToBeRendered = $childrenToBeRendered ?? true;
    }

    /**
     * Set item to be active.
     * @param bool $active
     */
    public function setActive(?bool $active = null): void
    {
        $this->active = $active ?? true;
    }

    /**
     * Get item active status.
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $hasActiveChild
     */
    public function setHasActiveChild($hasActiveChild): void
    {
        $this->hasActiveChild = $hasActiveChild;
    }

    /**
     * @return bool
     */
    public function getHasActiveChild(): bool
    {
        return $this->hasActiveChild;
    }

    /**
     * Add one or several css classes.
     * Adds one or more classes to the css attribute. Existing classes with the same name are overwritten.
     * @param string ...$name
     */
    public function addCssClass(...$name): void
    {
        $arr = explode(' ', $this->cssClass);
        $this->cssClass = implode(' ', array_unique(array_merge($arr, $name)));
    }

    /**
     * Returns the css class string
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }
}