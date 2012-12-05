<?php

class IpLocator {

	protected static $iplocDetectorInstance;

	protected function __construct() {
	}

	/**
	 * @return IpLoc_Locator
	 */
	public function getInstance() {
		if(!self::$iplocDetectorInstance) {

			$dbClass = DB_CLASS;
			$db = new $dbClass(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE, DB_PERSISTANT);

			self::$iplocDetectorInstance = new IpLoc_Locator($db);
		}
		return self::$iplocDetectorInstance;
	}
}