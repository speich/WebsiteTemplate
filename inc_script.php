<?php
session_start();
//set_time_limit(300);
if (!isset($_SESSION['loggedIn'])) { $_SESSION['loggedIn'] = false; }

// make include paths available to pages independent on subdir they reside in
$path = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/';	// may have trailing slash depending of environment
$incPath = $path.'scripts/php'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');

require_once 'Website.php';
require_once 'Menu.php';

$web = new Website();
$web->lastUpdate = '20.06.2013';
$web->pageTitle = 'Website default';

require_once 'inc_nav.php';