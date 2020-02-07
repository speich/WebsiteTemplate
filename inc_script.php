<?php

use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

require_once 'vendor/autoload.php';


$path = __DIR__ . '/';
$incPath = $path . 'layout' . PATH_SEPARATOR;
set_include_path($incPath);
date_default_timezone_set('Europe/Zurich');


$language = new Language();
$language->autoDetect();

$domains = array('websitetemplate');
$web = new Website($domains);
$web->setLastUpdate('07.02.2019');
$web->pageTitle = 'Website Template';

require_once __DIR__ . '/layout/inc_nav.php';