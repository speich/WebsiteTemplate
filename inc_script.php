<?php
use WebsiteTemplate\Language;

session_start();
//set_time_limit(300);
if (!isset($_SESSION['loggedIn'])) { $_SESSION['loggedIn'] = false; }

// make include paths available to pages independent on subdir they reside in
$path = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/';	// may have trailing slash depending of environment
$incPath = $path.'scripts/php'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');

require_once 'Language.php';
require_once 'Menu.php';

$web = new Language();
$web->lastUpdate = '15.12.2013';
$web->pageTitle = 'Website default';

require_once 'inc_nav.php';