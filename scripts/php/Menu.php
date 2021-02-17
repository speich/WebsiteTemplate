<?php
/**
 * This file contains the class to create a navigation menu.
 * @author Simon Speich
 */

namespace WebsiteTemplate;

use function array_key_exists;
use function array_slice;
use function count;

/**
 * Simple recursive php menu with unlimited levels which creates an unordered list
 * based on an array.
 *
 * Notes:
 *    To increase performance only open menus are used in recursion unless you set
 *    the whole menu to be open by setting the property AutoOpen = true;
 *
 *    A menu can be open without being active, when $allChildrenToBeRendered is set to true.
 */

/**
 * Creates the navigational menu.
 * A menu is made of menu items.
 */
class Menu
{
    /**
     * Holds array of menu items.
     * @var MenuItem[] menu items
     */
    public $arrItem = [];

    /** @var string $charset character set to use when creating and encoding html */
    public $charset = 'utf-8';

    /**
     * Holds html string of created menu.
     * @var string menu string
     */
    private $strMenu = '';

    /**
     * Render all menu items.
     * @var bool render children
     */
    public $allItemsRendered = false;

    /** @var bool render all items open. */
    public $allItemsOpen = false;

    /**
     * Automatically set item and all its parents active, if url is same as of current page.
     * @var bool set item active
     */
    public $autoActive = true;

    /**
     * Sets the url matching pattern of $autoActive property.
     * 1 = item url matches path only, 2 = item url patches path + query, 3 item url matches any part of path + query
     * @var integer
     */
    private $autoActiveMatching = 1;

    /**
     * Flag to mark first ul tag in recursion when rendering HTML.
     * @var bool is first HTMLULElement
     */
    private $firstUl = true;

    /**  @var ?string prefix for item id attribute */
    public $itemIdPrefix = null;

    /** @var string CSS class name of menu */
    public $cssClass = 'menu';

    /** @var ?string CSS id of menu */
    public $cssId = null;

    /** @var string CSS class name, when item has at least one child */
    public $cssItemHasChildren = 'menuHasChild';

    /** @var string CSS class name, when item is active */
    public $cssItemActive = 'menuActive';

    /** @var string CSS class name, when menu is open. */
    public $cssItemOpen = 'menuOpen';

    /** @var string CSS class name, when item hast at least one active child */
    public $cssItemActiveChild = 'menuHasActiveChild';

    /**
     * Constructs the menu.
     * You can provide a 2-dim array with all menu items.
     * or use the add method for each item singly.
     * @param array|null [$arrItem] array with menu items
     */
    public function __construct($arrItem = null)
    {
        if ($arrItem !== null) {
            foreach ($arrItem as $item) {
                $this->arrItem[$item[0]] = new MenuItem($item[0], $item[1], $item[2], $item[3] ?? null);
            }
        }
    }

    /**
     * Add a new menu item.
     * Array has to be in the form of:
     * array(id, parentId, linkTxt, optional linkUrl);
     * You can add new items to menu as long as you haven't called the render method.
     * @param array $arr menu item
     * @param null $idAfter id of item to insert new item after
     */
    public function add(array $arr, $idAfter = null): void
    {
        // note: for position we can not just use the index. The index is dynamic depending on the number of items, which
        // can be added or removed (e.g. when logged in a different number of items is rendered)
        // -> we need to use the actual id of the item to insert after
        $newItem = new MenuItem($arr[0], $arr[1], $arr[2], $arr[3] ?? null);
        if ($idAfter === null) {
            $this->arrItem[$arr[0]] = $newItem;
        } else {
            // note: arrItem is an associative array where key and index are not the same
            $i = 0;
            foreach ($this->arrItem as $index => $item) {
                if ($item->id === $idAfter) {
                    break;
                }
                $i++;
            }
            // note: array_splice would reindex keys
            $arrBefore = array_slice($this->arrItem, 0, $i, $preserveKeys = true);
            $arrAfter = array_slice($this->arrItem, $i, null, $preserveKeys = true);
            $this->arrItem = $arrBefore + [$newItem] + $arrAfter;
        }
    }

    /**
     * Check if menu item has at least one child menu.
     * @param string|integer $id item id
     * @return bool
     */
    private function checkChildExists($id): bool
    {
        $found = false;
        foreach ($this->arrItem as $item) {
            if ($item->parentId === $id) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Sets the url matching pattern of $autoActive property.
     * 1 = item url matches path only (default)
     * 2 = item url matches path + all query variables,
     * 3 = item url matches any part of path + query
     * 4 = item url matches match path + item query
     * @param int $type
     */
    public function setAutoActiveMatching(int $type): void
    {
        $this->autoActiveMatching = $type;
    }

    /**
     * Returns the url matching pattern of $autoActive property.
     * @return int
     */
    public function getAutoActiveMatching(): int
    {
        return $this->autoActiveMatching;
    }

    /**
     * Checks if an menu item should be set to active if its url matches the set pattern.
     * Pattern can also be set globally through Menu::setAutoActiveMatching();
     * Full path means complete current url, e.g. including page
     * Patterns:
     * 1 items url matches full path
     * 2 items url matches full path + exact query string
     * 3 items url matches full path + part of query string
     *
     * Returns boolean (match no match) or the number of matched directories.
     * This function may return Boolean TRUE OR FALSE, but may also return a non-Boolean value
     * which evaluates to FALSE, such as 0 or 1 to TRUE. Use the === operator for testing
     * the return value of this function.
     *
     * If item's active property is set to null it is not considered in active check.
     *
     * @param MenuItem $item
     * @param ?int $pattern
     * @return bool
     */
    public function checkActive(MenuItem $item, ?int $pattern = null): bool
    {
        // TODO: reduce complexity
        if ($item->getActive() === null) {
            return false;    // item explicitly set to null = skip
        }

        if ($item->getActive()) {
            return true; // item explicitly set to active
        }

        $url = $_SERVER['REQUEST_URI'];
        $arrUrlPage = parse_url($url);
        $arrUrlMenu = parse_url(html_entity_decode($item->linkUrl));
        if ($pattern === null) {
            $pattern = $this->getAutoActiveMatching();
        }
        switch ($pattern) {
            case 1:
                if ($arrUrlPage['path'] === $arrUrlMenu['path']) {
                    return true;
                }
                break;
            case 2:
                if ($arrUrlPage['path'].'?'.$arrUrlPage['query'] === $item->linkUrl) {
                    return true;
                }
                break;
            case 3:
                if (array_key_exists('query', $arrUrlMenu)) {
                    parse_str($arrUrlMenu['query'], $arr);
                    // 1. check query vars
                    foreach ($arr as $var => $val) {
                        if (!array_key_exists($var, $_GET)) {
                            return false;
                        }

                        if ($_GET[$var] !== $val) {
                            return false;
                        }
                    }
                }
                // 2. check path
                return $arrUrlPage['path'] === $arrUrlMenu['path'];
            default;
                return false;
        }

        return false;
    }

    /**
     * Returns id of every item that is active.
     * Returns a string if only one item is active, an array if there are several items active or false if none is active.
     * @return mixed id
     */
    public function getActive()
    {
        $activeIds = [];
        foreach ($this->arrItem as $item) {
            if ($item->getActive()) {
                $activeIds[] = $item->id;
            }
        }
        $num = count($activeIds);
        if ($num === 0) {
            $activeIds = false;
        } elseif ($num === 1) {
            $activeIds = $activeIds[0];
        }

        return $activeIds;
    }

    /**
     * Creates the menu Html string recursively.
     * @param string|int $parentId seed
     * @return string
     */
    private function createHtml($parentId): string
    {
        $this->strMenu .= '<ul';
        if ($this->firstUl) {
            $this->strMenu .= ' class="'.$this->cssClass.'"';
            if ($this->cssId !== null) {
                $this->strMenu .= ' id="'.$this->cssId.'"';
            }
            $this->firstUl = false;
        }
        $this->strMenu .= '>';

        foreach ($this->arrItem as $item) {
            if ($item->parentId === $parentId) {
                $this->setItemCssClass($item);
                $itemIdPrefix = $this->itemIdPrefix === null ? '' : ' id="'.$this->itemIdPrefix.$item->id.'"';
                $cssClass = $item->getCssClass() === '' ? '' : ' class="'.$item->getCssClass().'"';
                $this->strMenu .= '<li'.$itemIdPrefix.$cssClass.'>';
                $tagName = $item->linkUrl === null ? 'div' : 'a';
                $this->strMenu .= '<'.$tagName;
                if ($item->linkUrl !== null) {
                    $this->strMenu .= ' href="'.htmlspecialchars($item->linkUrl, ENT_QUOTES,
                            $this->charset).'"'.($item->linkTarget === '' ? '' : ' target="'.$item->linkTarget.'"');
                }
                $this->strMenu .= '>';
                $this->strMenu .= $item->linkTxt;
                $this->strMenu .= '</'.$tagName.'>';
                if ($this->checkChildExists($item->id) && ($item->getActive() || $this->allItemsRendered)) {
                    $this->createHtml($item->id);
                    $this->strMenu .= '</ul>';
                }
                $this->strMenu .= '</li>';
            }
        }

        return $this->strMenu;
    }

    /**
     * Sets the CSS class string of the item depending on it's status
     * @param MenuItem $item
     */
    protected function setItemCssClass(MenuItem $item): void
    {
        $hasChild = $this->checkChildExists($item->id);
        if ($hasChild) {
            $item->addCssClass($this->cssItemHasChildren);
            if ($this->allItemsOpen || $item->getActive()) {
                // children can be open even when nothing is active
                $item->addCssClass($this->cssItemOpen);
            }
        }
        if ($item->getActive()) {
            $item->addCssClass($this->cssItemActive);
        }
        if ($item->getHasActiveChild()) {
            $item->addCssClass($this->cssItemActiveChild);
        }
    }

    /**
     * Sets child/parent items to render and/or active according to URL matching scheme
     * or by explicitly setting item to active.
     * Should be called before rendering if AutoInit is set to false;
     * When argument $url is provided then the item with matching url is set to active.
     * @param ?string $url
     */
    public function setActive($url = null): void
    {
        if ($url === null) {
            foreach ($this->arrItem as $item) {
                if ($this->checkActive($item)) {
                    $item->setActive();
                }

                // set also the parent items to render
                if ($this->allItemsRendered) {
                    $parentId = $item->parentId;
                    while (array_key_exists($parentId, $this->arrItem)) {
                        $this->arrItem[$parentId]->setChildToBeRendered();
                        $parentId = $this->arrItem[$parentId]->parentId;
                    }
                }
                // set also the parent items to active
                if ($item->getActive()) {
                    // set also all item's parents
                    $parentId = $item->parentId;
                    while (array_key_exists($parentId, $this->arrItem)) {
                        $this->arrItem[$parentId]->setActive();
                        $parentId = $this->arrItem[$parentId]->parentId;
                    }
                }
            }
        } // match provided url
        else {
            foreach ($this->arrItem as $item) {
                if ($item->linkUrl === $url) {
                    $item->setActive();
                    if ($this->allItemsRendered || $item->getActive()) {
                        // set also item's parents to active
                        $parentId = $item->parentId;
                        while (array_key_exists($parentId, $this->arrItem)) {
                            $this->arrItem[$parentId]->setChildToBeRendered();
                            $this->arrItem[$parentId]->setActive();
                            $parentId = $this->arrItem[$parentId]->parentId;
                        }
                    }
                }
            }
        }
    }

    /**
     * Set hasActiveChild property for all items if they have at least an active child.
     */
    public function setHasActiveChildren(): void
    {
        foreach ($this->arrItem as $item) {
            foreach ($this->arrItem as $child) {
                if ($item->id === $child->parentId && $child->getActive()) {
                    $item->setHasActiveChild(true);
                }
            }
        }
    }

    /**
     * Returns a HTML string of the menu.
     * Call init method first.
     * @return string
     */
    public function render(): string
    {
        $this->reset();
        if ($this->autoActive) {
            $this->setActive();
        }
        if (count($this->arrItem) > 0) {
            $this->setHasActiveChildren();
            $str = $this->createHtml(reset($this->arrItem)->parentId);

            return $str.'</ul>';
        }

        return '';
    }

    /**
     * Reset menu
     */
    public function reset(): void
    {
        $this->firstUl = true;
        $this->strMenu = '';
    }
}