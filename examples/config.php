<?php

// Database
define('DB_CLASS', 'IpLoc_Db_MySQL'); // IpLoc_Db_MySQL or IpLoc_Db_PostgreSQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'letsgo');
define('DB_PERSISTANT', true);
define('DB_BULK_INSERT_LIMIT', 5000);

// You should download latest version from:
//   http://geolite.maxmind.com/download/geoip/database/GeoLiteCity_CSV/
// or
//   http://www.maxmind.com/app/geolitecity
define('GEO_LITE_CITY_BLOCKS_CSV', 'GeoLiteCity-Blocks.csv');
define('GEO_LITE_CITY_LOCATIONS_CSV', 'GeoLiteCity-Location.csv');

function debug($message) {
	echo $message . '<br />';
	flush();
}

// Autoload classes
define('ROOT_DIR', dirname(dirname(__FILE__)));

function autoloadClasses($class) {
	$filePath = ROOT_DIR . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	if(is_file($filePath)) {
		return require_once ($filePath);
	}
	$filePath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	if(is_file($filePath)) {
		return require_once ($filePath);
	}
}
spl_autoload_register('autoloadClasses');

ini_set('display_errors', 'on');
error_reporting(E_ALL);