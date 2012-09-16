<?php
session_start();
//set_time_limit(300);
if (!isset($_SESSION['LoggedIn'])) { $_SESSION['LoggedIn'] = false; }

$webRoot = '';	// set it website is in subdir

// make include paths available to pages independent on subdir they reside in
$path = $_SERVER['DOCUMENT_ROOT'].$webRoot;
$incPath = $path.'/scripts/php'.PATH_SEPARATOR;
$incPath.= $path.'/layout'.PATH_SEPARATOR;
$incPath.= $path.'/library';
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');

require_once('Website.php');
require_once('Menu.php');

$web = new Website();
$web->setWebRoot($webRoot);
$web->setLastUpdate('24.06.2010');
$web->setWindowTitle('Website default');

//require_once('inc_nav'.$web->getLangFileExt().'.php');
?>