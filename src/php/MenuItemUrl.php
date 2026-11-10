<?php

namespace WebsiteTemplate;

enum MenuItemUrl: int
{
    /**
     * Item url should match only the path of the page url when setting an item to active automatically.
     * @var int
     */
    case MatchPath = 1;

    /**
     * Item url should match both the path and query string of the page url when setting the item to active automatically.
     * All query string variables and values of the item url have to occur also in the query string of the page url.
     * @var int
     */
    case MatchFull = 2;

    /**
     * Item url should match the path and partially the query string of page url when setting item to active automatically.
     * Only all query string variables but not the query values of the item url have to occur also in the query string of the page url.
     * @var int
     */
    case MatchQueryVars = 3;

}
