<?php
/**
 * This file contains a class to create items for the navigation menu.
 */

namespace WebsiteTemplate;

/**
 * Class to create menu items.
 * MenuItems are part of a simple recursive php menu.
 * @see Menu
 */
class MenuItem
{
    use CssClassTrait;

    /**  @var string|int id */
    public string|int $id;

    /** @var string|int parent id */
    public string|int $parentId;

    /** @var string text of the link */
    public string $linkTxt;

    /** @var ?string url of the link */
    public ?string $linkUrl;

    /** @var ?string link target */
    public ?string $linkTarget = null;

    private bool $childToBeRendered = false;
    /**
     * Note: When this property is null, the CSS active state is set automatically by Menu::setActive() depending
     * on the current url.
     * @var ?bool is item active
     */
    private ?bool $active = null;

    /** @var bool has item an active child ? */
    private bool $hasActiveChild = false;

    /**
     * Constructs the menu item.
     * @param int|string $id unique id
     * @param int|string $parentId id of parent item
     * @param string $linkTxt link text
     * @param ?string $linkUrl link url
     */
    public function __construct(int|string $id, int|string $parentId, string $linkTxt, ?string $linkUrl = null)
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
     * Get item active status.
     * @return ?bool
     */
    public function getActive(): ?bool
    {
        return $this->active;
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
     * @return bool
     */
    public function getHasActiveChild(): bool
    {
        return $this->hasActiveChild;
    }

    /**
     * @param bool $hasActiveChild
     */
    public function setHasActiveChild(bool $hasActiveChild): void
    {
        $this->hasActiveChild = $hasActiveChild;
    }

}