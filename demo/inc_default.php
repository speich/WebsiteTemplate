<?php

use WebsiteTemplate\Language;
use WebsiteTemplate\Website;

$domains = ['websitetemplatenafidas.wsl.ch', 'nafidasdev.wsl.ch', 'nafidas.test', 'nafidasdev.test'];
$web = new Website($domains);
$web->indexPage = 'index.php';
$language = new Language();
$langShort = 'de';
$language->set($langShort);