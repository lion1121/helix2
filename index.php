<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 9000);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
define('ROOT', dirname(__FILE__));
define('TEMPLATE_DIR', ROOT.'/resourses/views/');

require_once 'vendor/autoload.php';
require_once 'public/index.php';
