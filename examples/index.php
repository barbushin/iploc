<?php

require_once ('config.php');
$clientIp = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_ADDR'];

?>
<form method="post">IP: <input type="text"
	value="<?= empty($_POST['ip']) ? $clientIp : $_POST['ip'] ?>"
	name="ip" /> <input type="submit" value="Find location" /></form>
<pre>
<?php

if(!empty($_POST['ip'])) {
	$startTime = microtime(true);
	try {
		$location = IpLocator::getInstance()->getIpLocation($_POST['ip']);
	}
	catch(Exception $e) {
		echo 'There is Exception in getIpLocation request. May be it\'s because you did not initialized database for IpLoc. Before using IpLoc you need to <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity_CSV/">download</a> last version of CSV base with IP locations and extract it to ' . dirname(__FILE__) . '
Then run <a href="init_base.php">init_base.php</a>';
		throw $e;
	}
	$searchTime = microtime(true) - $startTime;
	echo 'Searching time: ' . $searchTime . '<br />';

	if(!$location) {
		echo 'Location not found';
	}
	else {
		print_r($location);
	}
}