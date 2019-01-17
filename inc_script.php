<?php

use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

require_once 'library/vendor/autoload.php';


$path = __DIR__ . '/';
$incPath = $path . 'layout' . PATH_SEPARATOR;
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');


$lang = new Language();
$lang->set();

$web = new Website();
$web->lastUpdate = '27.02.2017';
$web->pageTitle = 'Website Template';

require_once __DIR__ . '/layout/inc_nav.php';