<?php
/**
 * This include file creates the navigation menu.
 * Each item needs its unique id for the menu to function properly.
 */
require_once 'Menu.php';

$mainNav = array(
	array(1, 0, 'Home', $web->getWebRoot().'index.php'),
	array(2, 0, 'About', $web->getWebRoot().'about.php'),
	array(3, 0, 'Services'),
		array(4, 3, 'more', $web->getWebRoot().'services/more.php'),
		array(5, 3, 'any', $web->getWebRoot().'services/load.php'),
		array(6, 3, 'some', $web->getWebRoot().'servcies/save.php'),
	array(7, 0, 'Contact', $web->getWebRoot().'contact.php')
);
$mainNav = new Menu('navMain', 'navMenu', $mainNav);
$mainNav->allChildrenToBeRendered = true;