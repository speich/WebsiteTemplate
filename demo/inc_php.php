<?php
// set php default values
$timeout = 10;
set_time_limit($timeout);
date_default_timezone_set('Europe/Zurich');
ini_set('default_charset', 'UTF-8');
ini_set('session.use_strict_mode', 1);

require_once __DIR__.'/../vendor/autoload.php';