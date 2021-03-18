<?php
use WebsiteTemplate\Menu;

/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */

/* simple horizontal menu */
$menu1 = new Menu([
	[1, 0, 'Home', $web->getWebRoot().'index.php'],
	[2, 0, 'About', $web->getWebRoot().'about.php'],
	[3, 0, 'Services', $web->getWebRoot().'services.php'],
	[7, 0, 'Contact', $web->getWebRoot().'contact.php']
]);
$menu1->setActive($web->getWebRoot().'services.php');
$menu1->cssClass = 'menu menu1';

/* Advanced animated horizontal menu with submenus */
$menu2 = new Menu([
	[1, 0, 'Home', $web->getWebRoot().'index.php'],
	[2, 0, 'About'],
		[8, 2, 'Me...'],
			[9, 8, 'You', $web->getWebRoot().'about.php'],
		[13, 2, 'CV', $web->getWebRoot().'cv.php'],
	[3, 0, 'Services'],
		[4, 3, 'more', $web->getWebRoot().'services/more.php'],
		[5, 3, 'any...'],
			[10, 5, 'subany', $web->getWebRoot().'services/load.php'],
			[11, 5, 'subsome', $web->getWebRoot().'services/load.php'],
			[12, 5, 'submore', $web->getWebRoot().'services/load.php'],
		[6, 3, 'some', $web->getWebRoot().'services/save.php'],
	[7, 0, 'Contact', $web->getWebRoot().'contact.php']
]);
$menu2->allChildrenRendered = true;
$menu2->setActive($web->getWebRoot().'services/more.php');
$menu2->cssClass = 'menu menu2';

/* Simple vertical menu */
$menu3 = new Menu([
	[1, 0, 'Home', $web->getWebRoot().'index.php'],
	[2, 0, 'About', $web->getWebRoot().'about.php'],
		[8, 2, 'Me', $web->getWebRoot().'about.php'],
			[9, 8, 'You', $web->getWebRoot().'about.php'],
	[3, 0, 'Services', $web->getWebRoot().'services/services.php'],
		[4, 3, 'more', $web->getWebRoot().'services/more.php'],
		[5, 3, 'any', $web->getWebRoot().'services/load.php'],
			[10, 5, 'subany', $web->getWebRoot().'services/load.php'],
		[6, 3, 'some', $web->getWebRoot().'services/save.php'],
	[7, 0, 'Contact', $web->getWebRoot().'contact.php']
]);
$menu3->setActive($web->getWebRoot().'services/services.php');
$menu3->cssClass = 'menu menu3';


/* Simple vertical menu showing all children */
$menu4 = new Menu([
	[1, 0, 'Home', $web->getWebRoot().'index.php'],
	[2, 0, 'About'],
		[8, 2, 'Me'],
			[9, 8, 'You', $web->getWebRoot().'about.php'],
	[3, 0, 'Services'],
		[4, 3, 'more', $web->getWebRoot().'services/more.php'],
		[5, 3, 'any'],
			[10, 5, 'subany', $web->getWebRoot().'services/load.php'],
		[6, 3, 'some', $web->getWebRoot().'services/save.php'],
	[7, 0, 'Contact', $web->getWebRoot().'contact.php']
]);
$menu4->allChildrenRendered = true;
$menu4->setActive($web->getWebRoot().'services/load.php');
$menu4->cssClass = 'menu menu4';