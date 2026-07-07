<?php
use WebsiteTemplate\Menu;

/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */

/* Advanced animated horizontal menu with submenus */
$menu2 = new Menu([
    [1, 0, 'Home', '/index.php'],
        [11, 1, 'my home'],
        [12, 1, 'your home', '/about.php'],
        [13, 1, 'his home', '/about.php'],
        [14, 1, 'our home', '/about.php'],
    [2, 0, 'About'],
        [21, 2, 'Me...'],
            [211, 21, 'You', '/about.php'],
            [212, 21, 'Who', '/about.php'],
        [22, 2, 'CV', '/cv.php'],
        [23, 2, 'item'],
    [3, 0, 'Services'],
        [31, 3, 'more', '/services/more.php'],
        [32, 3, 'any...'],
            [321, 32, '3rd level subany', '/services/load.php'],
            [322, 32, 'subsome', '/services/load.php'],
            [323, 32, 'submore', '/services/load.php'],
        [33, 3, 'some', '/services/save.php'],
    [4, 0, 'Portfolio', '/contact.php'],
    [5, 0, 'Contact', '/contact.php']
]);
$menu2->allChildrenRendered = true;
$menu2->bemModifier = 'horizontal';
$menu2->setActive('/services/more.php');


/* Simple vertical menu */
$menu3 = new Menu([
	[1, 0, 'Home', '/index.php'],
	[2, 0, 'About', '/about.php'],
		[8, 2, 'Me', '/about.php'],
			[9, 8, 'You', '/about.php'],
	[3, 0, 'Services', '/services/services.php'],
		[4, 3, 'more', '/services/more.php'],
		[5, 3, 'any', '/services/load.php'],
			[10, 5, 'subany', '/services/load.php'],
		[6, 3, 'some', '/services/save.php'],
	[7, 0, 'Contact', '/contact.php']
]);
$menu3->bemModifier = 'vertical';
$menu3->setActive('/services/services.php');


/* Simple vertical menu showing all children */
$menu4 = new Menu([
	[1, 0, 'Home', '/index.php'],
	[2, 0, 'About'],
		[8, 2, 'Me'],
			[9, 8, 'You', '/about.php'],
	[3, 0, 'Services'],
		[4, 3, 'more', '/services/more.php'],
		[5, 3, 'any'],
			[10, 5, 'subany', '/services/load.php'],
		[6, 3, 'some', '/services/save.php'],
	[7, 0, 'Contact', '/contact.php']
]);
$menu4->bemModifier = 'vertical-open';
$menu4->allChildrenRendered = true;
$menu4->setActive('/services/load.php');
