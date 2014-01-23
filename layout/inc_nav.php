<?php
use WebsiteTemplate\Menu;

/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */
require_once 'Menu.php';

/* simple horizontal menu */
$menu1 = new Menu('menu1', 'menu menu1', array(
	array(1, 0, 'Home', $web->getWebRoot().'index.php'),
	array(2, 0, 'About', $web->getWebRoot().'about.php'),
	array(3, 0, 'Services', $web->getWebRoot().'services.php'),
	array(7, 0, 'Contact', $web->getWebRoot().'contact.php')
));
$menu1->setActive($web->getWebRoot().'services.php');

/* Advanced animated horizontal menu with submenus */
$menu2 = new Menu('menu2', 'menu menu2', array(
	array(1, 0, 'Home', $web->getWebRoot().'index.php'),
	array(2, 0, 'About'),
		array(8, 2, 'Me...'),
			array(9, 8, 'You', $web->getWebRoot().'about.php'),
		array(13, 2, 'CV', $web->getWebRoot().'cv.php'),
	array(3, 0, 'Services'),
		array(4, 3, 'more', $web->getWebRoot().'services/more.php'),
		array(5, 3, 'any...'),
			array(10, 5, 'subany', $web->getWebRoot().'services/load.php'),
			array(11, 5, 'subsome', $web->getWebRoot().'services/load.php'),
			array(12, 5, 'submore', $web->getWebRoot().'services/load.php'),
		array(6, 3, 'some', $web->getWebRoot().'servcies/save.php'),
	array(7, 0, 'Contact', $web->getWebRoot().'contact.php')
));
$menu2->allChildrenToBeRendered = true;
$menu2->setActive($web->getWebRoot().'services/more.php');


/* Simple vertical menu */
$menu3 = new Menu('menu3', 'menu menu3', array(
	array(1, 0, 'Home', $web->getWebRoot().'index.php'),
	array(2, 0, 'About', $web->getWebRoot().'about.php'),
		array(8, 2, 'Me', $web->getWebRoot().'about.php'),
			array(9, 8, 'You', $web->getWebRoot().'about.php'),
	array(3, 0, 'Services', $web->getWebRoot().'services/services.php'),
		array(4, 3, 'more', $web->getWebRoot().'services/more.php'),
		array(5, 3, 'any', $web->getWebRoot().'services/load.php'),
			array(10, 5, 'subany', $web->getWebRoot().'services/load.php'),
		array(6, 3, 'some', $web->getWebRoot().'servcies/save.php'),
	array(7, 0, 'Contact', $web->getWebRoot().'contact.php')
));
$menu3->setActive($web->getWebRoot().'services/services.php');
//$menu3->setActive($web->getWebRoot().'services/load.php');


/* Simple vertical menu showing all children */
$menu4 = new Menu('menu4', 'menu menu4', array(
	array(1, 0, 'Home', $web->getWebRoot().'index.php'),
	array(2, 0, 'About'),
		array(8, 2, 'Me'),
			array(9, 8, 'You', $web->getWebRoot().'about.php'),
	array(3, 0, 'Services'),
		array(4, 3, 'more', $web->getWebRoot().'services/more.php'),
		array(5, 3, 'any'),
			array(10, 5, 'subany', $web->getWebRoot().'services/load.php'),
		array(6, 3, 'some', $web->getWebRoot().'servcies/save.php'),
	array(7, 0, 'Contact', $web->getWebRoot().'contact.php')
));
$menu4->allChildrenToBeRendered = true;
$menu4->setActive($web->getWebRoot().'services/load.php');