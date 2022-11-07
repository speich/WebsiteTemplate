<?php

use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

require_once __DIR__.'/vendor/autoload.php';


date_default_timezone_set('Europe/Zurich');

$language = new Language();
$language->autoDetect();

$domains = ['websitetemplate.test'];
$web = new Website($domains);
$web->setLastUpdate('03.08.2020');
$web->pageTitle = 'Website Template';

require_once __DIR__.'/layout/inc_nav.php';