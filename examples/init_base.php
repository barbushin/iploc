<?php

require_once ('config.php');

$glcBlocksSource = new IpLoc_Base_Csv(GEO_LITE_CITY_BLOCKS_CSV, 2);
$glcBlocksSource->setColumnsNames(array ('startIp', 2 => 'locationId'));

$glcLocationsSource = new IpLoc_Base_Csv(GEO_LITE_CITY_LOCATIONS_CSV, 2);
$glcLocationsSource->setColumnsNames(array ('id', 'country', 3 => 'city', 5 => 'latitude', 6 => 'longitude'));

$updater = new IpLoc_Base_Updater(IpLocator::getInstance(), INIT_TABLES_MYSQL, $glcBlocksSource, $glcLocationsSource);
$updater->setBulkInsertLimit(DB_BULK_INSERT_LIMIT);
$updater->setDebugCallback('debug');

$updater->update();