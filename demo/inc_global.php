<?php
/**
 * Sets global variables, includes classes, and initializes main objects.
 * This file needs to be included in every page
 */

// set PHP default values and autoload classes
require_once __DIR__.'/inc_php.php';

// instantiate default classes
require_once __DIR__.'/inc_default.php';

// include layout and navigation
require_once __DIR__.'/layout/inc_layout.php';