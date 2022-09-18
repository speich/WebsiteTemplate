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
 * Simple recursive menu with unlimited levels which creates an unordered list based on an array.
 * Ids of the menu items have to be unique. By default, menu items are set to active
 * when the current path of the page url matches the path of the item url. This can be changed with the
 * method setAutoActiveMatching(MATCH_FULL | MATCH_QUERY_ANY).
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
     * Item url should match only the path of the page url when setting item to active automatically.
     * @var int
     */
    public const MATCH_PATH = 1;

    /**
     * Item url should match both path and query string of page url when setting item to active automatically.
     * All query string variables and values of the item url have to occur also in the query string of the page url.
     * @var int
     */
    public const MATCH_FULL = 2;

    /**
     * Item url should match path and partially the query string of page url when setting item to active automatically.
     * Only all query string variables but not the query values of the item url have to occur also in the query string of the page url.
     * @var int
     */
    public const MATCH_QUERY_VARS = 3;

    /**
     * Hold menu items.
     * @var MenuItem[]
     */
    public array $arrItem = [];

    /** @var string $charset character set to use when creating and encoding html */
    public string $charset = 'utf-8';
    /**
     * Render all children, no matter if the parent item is open or closed.
     * @var bool render all children
     */
    public bool $allChildrenRendered = false;
    /**
     * Set the css state of all parent items to open.
     * @var bool
     */
    public bool $allChildrenOpen = false;
    /**
     * Automatically set the css state to active, if url is same as of current page.
     * @var bool
     */
    public bool $autoActive = true;
    /**  @var ?string prefix for item id attribute */
    public ?string $itemIdPrefix = null;
    /** @var string CSS class name of menu */
    public string $cssClass = 'menu';
    /** @var ?string CSS id of menu */
    public ?string $cssId = null;
    /** @var string CSS class name, when item has at least one child */
    public string $cssItemHasChildren = 'menuHasChild';
    /** @var string CSS class name, when item is active */
    public string $cssItemActive = 'menuActive';
    /** @var string CSS class name, when menu is open. */
    public string $cssItemOpen = 'menuOpen';
    /** @var string CSS class name, when item hast at least one active child */
    public string $cssItemActiveChild = 'menuHasActiveChild';
    /**
     * The html of the created menu.
     * @var string rendered html
     */
    private string $html = '';
    /**
     * Sets the url matching pattern of $autoActive property.
     * @var int
     */
    private int $autoActiveMatching = Menu::MATCH_PATH;
    /**
     * Flag to mark first ul tag in recursion when rendering HTML.
     * @var bool is first HTMLULElement
     */
    private bool $firstUl = true;

    /**
     * Constructs the menu.
     * You can provide a flat 2-dim array with all menu items, e.g.:
     * [
     *  [1, 0, 'item 1'],
     *      [11, 1, 'item 2', url],
     *      [12, 1, 'item 3', url],
     *  [2, 0, 'item 4', url],
     *  [3, 0, 'item 5],
     *      [31, 3, 'item 6'],
     *          [311, 31, 'item 7', ''],
     *      [32, 3, 'item 8'],
     *  [4, 0, 'item 9']
     * ]
     * or use the add method for each item individually.
     * @param MenuItem[]|null $arrItem menu items
     */
    public function __construct(?array $arrItem = null)
    {
        if ($arrItem !== null) {
            foreach ($arrItem as $item) {
                $this->arrItem[$item[0]] = new MenuItem($item[0], $item[1], $item[2], $item[3] ?? null);
            }
        }
    }

    /**
     * Add a new menu item.
     * If $idAfter is null, the new item will be appended.
     * Note: New items can only be added as long as the render method has not been called yet.
     * @param MenuItem $newItem item to add
     * @param int|string|null $idAfter id of item to insert new item after
     */
    public function add(MenuItem $newItem, int|string|null $idAfter = null): void
    {
        // note: for position we can not just use the index. The index is dynamic depending on the number of items, which
        // can be added or removed (e.g. when logged in a different number of items is rendered)
        // -> we need to use the actual id of the item to insert after

        if ($idAfter === null) {
            $this->arrItem[$newItem->id] = $newItem;
        } else {
            // note: arrItem is an associative array where key and index are not the same
            $i = 0;
            foreach ($this->arrItem as $item) {
                if ($item->id === $idAfter) {
                    break;
                }
                $i++;
            }
            // note: array_splice would reindex keys
            $arrBefore = array_slice($this->arrItem, 0, $i, true);
            $arrAfter = array_slice($this->arrItem, $i, null, true);
            $this->arrItem = $arrBefore + [$newItem] + $arrAfter;
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
        $this->html = '';
    }

    /**
     * Sets child/parent items to render and/or active according to URL matching scheme
     * or by explicitly setting item to active.
     * Should be called before rendering if AutoInit is set to false;
     * When argument $url is provided then the item with matching url is set to active.
     * @param ?string $url
     */
    public function setActive(string $url = null): void
    {
        if ($url === null) {
            foreach ($this->arrItem as $item) {
                if ($this->checkActive($item)) {
                    $item->setActive();
                }

                // set also the parent items to render
                if ($this->allChildrenRendered) {
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
                    if ($this->allChildrenRendered || $item->getActive()) {
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
     * Checks if a menu item should be set to active if the item url matches the pattern.
     * The matching pattern can also be set globally through Menu::setAutoActiveMatching(); By default only the path
     * of the url is matched.
     * Returns boolean (match no match) or the number of matched directories.
     * This function may return Boolean TRUE OR FALSE, but may also return a non-Boolean value
     * which evaluates to FALSE, such as 0 or 1 to TRUE. Use the === operator for testing
     * the return value of this function.
     *
     * If item's active property is set to null it is not considered in active check.
     *
     * @param MenuItem $item
     * @param ?int $type Menu::MATCH_PATH | Menu::MATCH_FULL | Menu::MATCH_QUERY_ANY
     * @return bool
     * @see Menu::setAutoActiveMatching()
     */
    public function checkActive(MenuItem $item, ?int $type = null): bool
    {
        $state = $item->getActive();

        return $state ?? $this->checkAutoActive($item, $type);
    }

    /**
     * Returns id of every item that is active.
     * Returns a string if only one item is active, an array if there are several items active or false if none is active.
     * @return int|string|array|false id
     */
    public function getActive(): bool|int|array|string
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
     * Check if menu item should be set to active.
     * @param MenuItem $item
     * @param int|null $type
     * @return bool
     */
    protected function checkAutoActive(MenuItem $item, ?int $type = null): bool
    {
        $url = $_SERVER['REQUEST_URI'];
        $urlPage = parse_url($url);
        $urlItem = parse_url(html_entity_decode($item->linkUrl));
        if ($type === null) {
            $type = $this->getAutoActiveMatching();
        }

        $autoActive = $urlPage['path'] === $urlItem['path'];    // handles case MATCH_PATH_ONLY
        if ($autoActive) {
            $query = new QueryString();
            parse_str($urlItem['query'], $arr);
            if ($type === Menu::MATCH_FULL) {
                $autoActive = $query->in($arr);
            } elseif ($type === Menu::MATCH_QUERY_VARS) {
                $keys = array_keys($arr);
                $autoActive = $query->in($keys);
            }
        }

        return $autoActive;
    }

    /**
     * Return the url matching pattern.
     * Returns the matching pattern used when automatically setting the item to active.
     * The pattern is used to compare the current page url with the item url.
     * @return int
     * @see Menu::setAutoActiveMatching()
     */
    public function getAutoActiveMatching(): int
    {
        return $this->autoActiveMatching;
    }

    /**
     * Set the url matching pattern
     * Set the matching pattern to use when automatically setting the item to active.
     * The pattern is used to compare the current page url with the item url.
     * Menu::MATCH_PATH = 1 = item url matches path only (default)
     * Menu::MATCH_FULL = 2 = item url matches path + all query variables,
     * Menu::MATCH_QUERY_ANY = 3 = item url matches path and at least on of the query parameters (name and value)
     * @param int $type
     */
    public function setAutoActiveMatching(int $type): void
    {
        $this->autoActiveMatching = $type;
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
     * Creates the menu Html string recursively.
     * @param int|string $parentId seed
     * @return string html
     */
    private function createHtml(int|string $parentId): string
    {
        $this->html .= '<ul';
        if ($this->firstUl) {
            $this->html .= ' class="'.$this->cssClass.'"';
            if ($this->cssId !== null) {
                $this->html .= ' id="'.$this->cssId.'"';
            }
            $this->firstUl = false;
        }
        $this->html .= '>';

        foreach ($this->arrItem as $item) {
            if ($item->parentId === $parentId) {
                $this->setItemCssClass($item);
                $itemIdPrefix = $this->itemIdPrefix === null ? '' : ' id="'.$this->itemIdPrefix.$item->id.'"';
                $cssClass = $item->getCssClass() === '' ? '' : ' class="'.$item->getCssClass().'"';
                $this->html .= '<li'.$itemIdPrefix.$cssClass.'>';
                $tagName = $item->linkUrl === null ? 'div' : 'a';
                $this->html .= '<'.$tagName;
                if ($item->linkUrl !== null) {
                    $this->html .= ' href="'.htmlspecialchars($item->linkUrl, ENT_QUOTES,
                            $this->charset).'"'.($item->linkTarget === null ? '' : ' target="'.$item->linkTarget.'"');
                }
                $this->html .= '>';
                $this->html .= $item->linkTxt;
                $this->html .= '</'.$tagName.'>';
                if ($this->checkChildExists($item->id) && ($item->getActive() || $this->allChildrenRendered)) {
                    $this->createHtml($item->id);
                    $this->html .= '</ul>';
                }
                $this->html .= '</li>';
            }
        }

        return $this->html;
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
            if ($this->allChildrenOpen || $item->getActive()) {
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
     * Check if menu item has at least one child menu.
     * @param int|string $id item id
     * @return bool
     */
    private function checkChildExists(int|string $id): bool
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
}