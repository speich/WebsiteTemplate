<?php
use WebsiteTemplate\Language;
use WebsiteTemplate\Website;


session_start();
if (!isset($_SESSION['loggedIn'])) { $_SESSION['loggedIn'] = false; }

$path = __DIR__.'/';
$incPath = $path.'scripts/php'.PATH_SEPARATOR;
$incPath.= $path.'layout'.PATH_SEPARATOR;
$incPath.= $path.'library';
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');

require_once 'Website.php';
require_once 'Language.php';
require_once 'Menu.php';

$lang = new Language();
$lang->set();

$web = new Website();
$web->lastUpdate = '27.02.2017';
$web->pageTitle = 'Website Template';

require_once __DIR__.'/layout/inc_nav.php';