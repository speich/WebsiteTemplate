<?php
use WebsiteTemplate\Language;

session_start();
if (!isset($_SESSION['loggedIn'])) { $_SESSION['loggedIn'] = false; }

$path = __DIR__.'/';
$incPath = $path.'scripts/php'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');

require_once 'Language.php';
require_once 'Menu.php';

$web = new Language();
$web->lastUpdate = '22.01.2014';
$web->pageTitle = 'Website default';

require_once 'inc_nav.php';