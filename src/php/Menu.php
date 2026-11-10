<?php
/**
 * This file contains the class to create a navigation menu.
 */

namespace WebsiteTemplate;

use WebsiteTemplate\Html\CssBlockTrait;
use WebsiteTemplate\Http\QueryString;
use function array_key_exists;
use function array_slice;
use function count;
use function is_array;

/**
 * Simple recursive menu with unlimited levels, which creates an unordered list based on an array.
 * Ids of the menu items have to be unique. By default, menu items are set to active
 * when the current path of the page url matches the path of the item url. This can be changed with the
 * method setAutoActiveMatching(Menu::MATCH_FULL | MATCH_QUERY_VARS).
 * Notes:
 *    To increase performance, only open menus are used in the recursion, unless you set
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
    use CssBlockTrait;

    /**
     * Hold menu items.
     * The array keys correspond to the unique ID of the MenuItem.
     * @var array<int|string, MenuItem>
     */
    public array $arrItem = [];

    /** @var string $charset character set to use when creating and encoding HTML */
    public string $charset = 'utf-8';

    /**
     * Render all children, no matter if the parent item is open or closed.
     * @var bool render all children
     */
    public bool $allChildrenRendered = false;

    /**
     * Automatically set the CSS state to active if the url is the same as of the current page.
     * @var bool
     */
    public bool $autoActive = true;

    /**  @var ?string prefix for item ID attribute */
    public ?string $itemIdPrefix = null;

    /** @var ?string CSS ID of the menu */
    public ?string $cssId = null;

    /**
     * @var Orientation render the menu group horizontally or vertically
     */
    public Orientation $orientation = Orientation::Horizontal;


    /**
     * The html of the created menu.
     * @var string rendered html
     */
    private string $html = '';

    /**
     * Sets the url matching pattern of $autoActive property.
     * @var MenuItemUrl
     */
    private MenuItemUrl $autoActiveMatching = MenuItemUrl::MatchPath;
    /**
     * Flag to mark the first ul tag in recursion when rendering HTML.
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
     * @param array<int, array{0: int|string, 1: int|string, 2: string, 3?: string|null}> $menuData
     *         An array of menu items where each item is defined by:
     *         [0] => id (int|string)
     *         [1] => parentId (int|string)
     *         [2] => linkTxt (string)
     *         [3] => linkUrl (string|null)
     */
    public function __construct(?array $menuData = null)
    {
        // Initialize css class defaults here (not as property overrides) because CssBlockTrait is composed directly;
        $this->blockName = 'menu';
        if ($menuData !== null) {
            $this->addAll($menuData);
        }
    }

    /**
     * Add all items to the menu.
     * @param array<int, array{0: int|string, 1: int|string, 2: string, 3?: string|null}> $menuData
     * @return void
     */
    public function addAll(array $menuData): void
    {
        foreach ($menuData as $item) {
            $this->arrItem[$item[0]] = $this->itemFromArray($item);
        }
    }

    /**
     * Create a menu item from an array.
     * @param array $item
     * @return MenuItem
     */
    private function itemFromArray(array $item): MenuItem
    {
        return new MenuItem($item[0], $item[1], $item[2], $item[3] ?? null);
    }

    /**
     * Add a new menu item.
     * If $idAfter is null, the new item will be appended.
     * Note: New items can only be added as long as the render method has not been called yet.
     * @param MenuItem|array $newItem item to add
     * @param int|string|null $idAfter id of item to insert new item after
     */
    public function add(MenuItem|array $newItem, int|string|null $idAfter = null): void
    {
        if (is_array($newItem)) {
            $newItem = $this->itemFromArray($newItem);
        }

        if ($idAfter === null) {
            $this->arrItem[$newItem->id] = $newItem;
        } else {
            // note: arrItem is an associative array where key and index are not the same.
            $this->insert($newItem, $idAfter);
        }
    }

    /**
     * Insert an item after the given ID.
     * @param MenuItem $newItem
     * @param int|string $idAfter
     * @return void
     */
    private function insert(MenuItem $newItem, int|string $idAfter): void
    {
        // Note: for position we cannot just use the index. The index is dynamic depending on the number of items, which
        // can be added or removed (e.g., when logged in a different number of items is rendered)
        // -> we need to use the actual ID of the item to insert after
        // note: array_splice would reindex keys.
        $idx = array_search($idAfter, $this->arrItem, true);
        $arrBefore = array_slice($this->arrItem, 0, $idx, true);
        $arrAfter = array_slice($this->arrItem, $idx, null, true);
        $this->arrItem = $arrBefore + [$newItem] + $arrAfter;
    }

    /**
     * Returns an HTML string of the menu.
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
     * Sets child/parent items to render and/or to active, according to the URL matching scheme
     * or by explicitly setting an item to active.
     * Should be called before rendering if AutoInit is set to false;
     * When argument $url is provided, then the item with matching url is set to active.
     * @param ?string $url
     */
    public function setActive(?string $url = null): void
    {
        if ($url === null) {
            foreach ($this->arrItem as $item) {
                if ($item->linkUrl !== null && $this->checkActive($item)) {
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
        } // match the provided url
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
     * The matching pattern can also be set globally through Menu::setAutoActiveMatching();
     * By default, only the path of the url is matched.
     * Returns boolean (match no match), or the number of matched directories.
     * This function may return Boolean TRUE OR FALSE, but may also return a non-Boolean value,
     * which evaluates to FALSE, such as 0 or 1 to TRUE. Use the === operator for testing
     * the return value of this function.
     *
     * If the item's active property is set to null, it is not considered in active check.
     *
     * @param MenuItem $item
     * @param ?int $type Menu::MATCH_PATH | Menu::MATCH_FULL | Menu::MATCH_QUERY_VARS
     * @return bool
     * @see Menu::setAutoActiveMatching()
     */
    public function checkActive(MenuItem $item, ?int $type = null): bool
    {
        $state = $item->getActive();

        return $state ?? $this->checkAutoActive($item, $type);
    }

    /**
     * Check if the menu item should be set to active.
     * @param MenuItem $item
     * @param MenuItemUrl|null $type
     * @return bool
     */
    protected function checkAutoActive(MenuItem $item, ?MenuItemUrl $type = null): bool
    {
        $url = $_SERVER['REQUEST_URI'];
        $urlPage = parse_url($url);
        $urlItem = parse_url(html_entity_decode($item->linkUrl ?? ''));
        if ($type === null) {
            $type = $this->getAutoActiveMatching();
        }

        $autoActive = $urlPage['path'] === $urlItem['path'];    // handles case MATCH_PATH_ONLY
        if ($type !== MenuItemUrl::MatchPath && $autoActive && array_key_exists('query', $urlItem)) {
            parse_str($urlItem['query'], $arr);
            $query = new QueryString(array_keys($arr)); // whitelist query param used in item->linkUrl
            if ($type === MenuItemUrl::MatchFull) {
                $autoActive = $query->in($arr);
            } elseif ($type === MenuItemUrl::MatchQueryVars) {
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
     * @return MenuItemUrl
     * @see Menu::setAutoActiveMatching()
     */
    public function getAutoActiveMatching(): MenuItemUrl
    {
        return $this->autoActiveMatching;
    }

    /**
     * Set the url matching pattern.
     * Set the matching pattern to use when automatically setting the item to active.
     * The pattern is used to compare the current page url with the item url.
     * @param MenuItemUrl $type
     */
    public function setAutoActiveMatching(MenuItemUrl $type): void
    {
        $this->autoActiveMatching = $type;
    }

    /**
     * Creates the menu HTML string recursively.
     * @param int|string $parentId seed
     * @return string html
     */
    private function createHtml(int|string $parentId): string
    {
        $this->html .= '<ul';
        if ($this->firstUl) {
            $ulClasses = [
                $this->blockClass(),
                $this->blockClass($this->orientation->value),
            ];
            $this->html .= ' class="'.implode(' ', $ulClasses).'"';

            if ($this->cssId !== null) {
                $this->html .= ' id="'.$this->cssId.'"';
            }
            $this->firstUl = false;
        }
        $this->html .= '>';

        foreach ($this->arrItem as $item) {
            if ($item->parentId === $parentId) {
                $itemIdPrefix = $this->itemIdPrefix === null ? '' : ' id="'.$this->itemIdPrefix.$item->id.'"';
                $cssClass = $item->getCssClass() === '' ? '' : ' class="'.$item->getCssClass().'"';
                $this->html .= '<li'.$itemIdPrefix.$cssClass.'>';
                $tagName = $item->linkUrl === null ? 'div' : 'a';
                $this->html .= '<'.$tagName;
                if ($item->linkUrl !== null) {
                    $this->html .= ' href="'.htmlspecialchars($item->linkUrl, ENT_QUOTES,
                            $this->charset).'"'.($item->linkTarget === null ? '' : ' target="'.$item->linkTarget.'"');
                }
                if ($item->getActive()) {
                    $this->html .= ' aria-current="page"';
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
     * Check if a menu item has at least one child menu.
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

    /**
     * Returns a menu item by its ID.
     * @param string|int $id The unique ID of the menu item
     * @return ?MenuItem Returns the item, or null if the ID does not exist
     */
    public function byId(string|int $id): ?MenuItem
    {
        return $this->arrItem[$id] ?? null;
    }
}