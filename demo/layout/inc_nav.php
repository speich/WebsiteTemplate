<?php

use WebsiteTemplate\Menu;
use WebsiteTemplate\Orientation;

/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */

/* Advanced animated horizontal menu with submenus */
$data = [
    [1, 0, 'Home', '/index.php'],
    [11, 1, 'my home'],
    [12, 1, 'your home'],
    [13, 1, 'his home'],
    [14, 1, 'our home'],
    [2, 0, 'About'],
    [21, 2, 'Me...'],
    [211, 21, 'You'],
    [212, 21, 'Who'],
    [22, 2, 'CV'],
    [23, 2, 'item'],
    [3, 0, 'Services'],
    [31, 3, 'more'],
    [32, 3, 'any...'],
    [321, 32, '3rd level subany'],
    [322, 32, 'subsome', '/services/load.php'],
    [323, 32, 'submore'],
    [33, 3, 'some'],
    [4, 0, 'Portfolio'],
    [5, 0, 'Contact']
];
$menu1 = new Menu($data);
$menu1->setActive('/services/load.php');
$menu1->allChildrenRendered = true;

$menu2 = new Menu($data);
$menu2->orientation = Orientation::Vertical;
$menu2->setActive('/services/load.php');
$menu2->allChildrenRendered = false;