<?php
use WebsiteTemplate\Menu;

/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */

/* Advanced animated horizontal menu with submenus */
$menu1 = new Menu([
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
]);
$menu1->bemModifier = 'horizontal';
$menu1->setActive('/services/load.php');
$menu1->allChildrenRendered = true;

/* Simple vertical menu showing all children */
$menu2 = new Menu([
	[1, 0, 'Home', '/index.php'],
	[2, 0, 'About'],
		[8, 2, 'Me'],
			[9, 8, 'You', '/about.php'],
	[3, 0, 'Services'],
		[4, 3, 'more'],
		[5, 3, 'any'],
			[10, 5, 'subany', '/services/load.php'],
		[6, 3, 'some'],
	[7, 0, 'Contact']
]);
$menu2->bemModifier = 'vertical';
$menu2->setActive('/services/load.php');
