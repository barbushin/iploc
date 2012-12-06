<?php

/**
 * @see https://github.com/barbushin/iploc
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class IpLoc_Locator {

	protected $ipMasksTable;
	protected $locationsTable;
	protected $db;

	public function __construct(IpLoc_Db_Abstract $db = null, $ipMasksTable = 'iploc_masks', $locationsTable = 'iploc_locations') {
		$this->ipMasksTable = $ipMasksTable;
		$this->locationsTable = $locationsTable;
		$this->db = $db;
	}

	/**
	 * @return IpLoc_Db_Abstract
	 */
	public function getDb() {
		if(!$this->db) {
			throw new Exception('Property "db" must be initialized as object of IpLoc_Db_Abstract');
		}
		return $this->db;
	}

	public function getIpMasksTable() {
		return $this->ipMasksTable;
	}

	public function getLocationsTable() {
		return $this->locationsTable;
	}

	/**
	 * @return IpLoc_Location|null
	 */
	public function getIpLocationByDbFetchCallback($ip, $dbFetchCallback) {
		if(!is_callable($dbFetchCallback)) {
			throw new Exception('Argument $dbFetchCallback must be callback');
		}

		$locationIdRows = call_user_func($dbFetchCallback, $this->sqlGetLocationIdByIp($ip));
		if($locationIdRows) {
			$locationRows = call_user_func($dbFetchCallback, $this->sqlLocationById($locationIdRows[0]['id']));
			return $this->initLocationByRow(reset($locationRows));
		}
	}

	public function getIpLocation($ip) {
		return $this->getIpLocationByDbFetchCallback($ip, array ($this->getDb(), 'fetch'));
	}

	/*
		 * DON'T USE QUERY LIKE THIS BECAUSE IT'S VERY SLOW
		 * 'SELECT ' . $this->locationsTable . '.* FROM ' . $this->ipMasksTable . ' LEFT JOIN ' . $this->locationsTable . ' ON ' . $this->locationsTable . '.id = '.$this->ipMasksTable.'.locationId WHERE startIp < "' . $ipLong . '"  AND endIp > "' . $ipLong . '" LIMIT 1';
		 * DON'T USE QUERY LIKE THIS BECAUSE SOMETIME YOU WILL HAVE ERROR: The SELECT would examine more than MAX_JOIN_SIZE rows; check your WHERE and use SET SQL_BIG_SELECTS=1 or SET SQL_MAX_JOIN_SIZE=# if the SELECT is okay
		 * 'SELECT ' . $this->locationsTable . '.* FROM ' . $this->ipMasksTable . ' LEFT JOIN ' . $this->locationsTable . ' ON ' . $this->locationsTable . '.id = ' . $this->ipMasksTable . '.locationId WHERE startIp < "' . $ipLong . '" ORDER BY ' . $this->ipMasksTable . '.startIp DESC LIMIT 1'
		 * USE THIS TWO QUERIES, THEY WORK VERY FAST AND STABLE:
	 	*/
	public function sqlGetLocationIdByIp($ip) {
		return 'SELECT locationId as id FROM ' . $this->ipMasksTable . ' WHERE startIp < "' . sprintf('%u', ip2long($ip)) . '" ORDER BY startIp DESC LIMIT 1';
	}

	public function sqlLocationById($locationId) {
		return 'SELECT * FROM ' . $this->locationsTable . ' WHERE id = ' . $locationId . ' LIMIT 1';
	}

	/**
	 * @return IpLoc_Location
	 */
	public function initLocationByRow($row) {
		if($row) {
			$location = new IpLoc_Location();
			$location->latitude = $row['latitude'];
			$location->longitude = $row['longitude'];
			$location->latitude = $row['latitude'];
			$location->country = $row['country'];
			$location->city = $row['city'];
			return $location;
		}
	}
}