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
    /**  @var string|int id */
    public string|int $id;

    /** @var string|int parent id */
    public string|int $parentId;

    /** @var string text of the link */
    public string $linkTxt;

    /** @var ?string url of the link */
    public ?string $linkUrl;

    /** @var string CSS class name */
    private string $cssClass = '';

    /** @var bool render children */
    private bool $childToBeRendered = false;

    /**
     * Note: When this property is null, the css active state is set automatically by Menu::setActive() depending
     * on the current url.
     * @var ?bool is item active
     */
    private ?bool $active = null;

    /** @var bool has item an active child ? */
    private bool $hasActiveChild = false;

    /** @var ?string link target */
    public ?string $linkTarget = null;

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
     * @return ?bool
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $hasActiveChild
     */
    public function setHasActiveChild(bool $hasActiveChild): void
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