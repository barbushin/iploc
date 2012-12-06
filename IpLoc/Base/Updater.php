<?php

define('INIT_TABLES_MYSQL', '

DROP TABLE IF EXISTS `iploc_locations`;

CREATE TABLE `iploc_locations` (
  `id` int(8) unsigned NOT NULL,
  `country` char(2) NOT NULL,
  `city` varchar(255) NOT NULL,
  `latitude` double(7,4) NOT NULL,
  `longitude` double(7,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;

DROP TABLE IF EXISTS `iploc_masks`;

CREATE TABLE `iploc_masks` (
  `startIp` int(8) unsigned NOT NULL,
  `locationId` int(8) unsigned NOT NULL,
  PRIMARY KEY (`startIp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;

');

/**
 * @see https://github.com/barbushin/iploc
 * @link http://www.maxmind.com/app/geolitecity
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class IpLoc_Base_Updater {

	/**
	 * @var IpLoc_Locator
	 */
	protected $detector;

	/**
	 * @var IpLoc_Base_SourceInterface
	 */
	protected $glcBlocksSource;
	/**
	 * @var IpLoc_Base_SourceInterface
	 */
	protected $glcLocationsSource;
	protected $initTablesSql;
	protected $bulkInsertLimit = 10000;
	protected $debugCallback;

	public function __construct(IpLoc_Locator $detector, $initTablesSql, IpLoc_Base_SourceInterface $glcBlocksSource, IpLoc_Base_SourceInterface $glcLocationsSource) {
		$this->detector = $detector;
		$this->initTablesSql = $initTablesSql;
		$this->glcBlocksSource = $glcBlocksSource;
		$this->glcLocationsSource = $glcLocationsSource;
	}

	public function setDebugCallback($debugCallback) {
		if(!is_callable($debugCallback)) {
			throw new Exception('Debug callback is not callable');
		}
		$this->debugCallback = $debugCallback;
	}

	protected function debug($message, $showEveryThreeSeconds = false) {
		static $lastShow;

		if($this->debugCallback) {
			if($showEveryThreeSeconds) {
				if(time() - $lastShow < 3) {
					return;
				}
				$lastShow = time();
			}

			call_user_func($this->debugCallback, $message);
		}
	}

	public function setBulkInsertLimit($bulkInsertLimit) {
		$this->bulkInsertLimit = $bulkInsertLimit;
	}

	protected function initTables() {
		foreach(explode(';', $this->initTablesSql) as $sql) {
			$sql = trim($sql, " \n\r");
			if($sql) {
				$this->detector->getDb()->query($sql);
			}
		}
	}

	public function update() {
		set_time_limit(0);
		$this->debug('Start update');
		$this->debug('Init tables');
		$this->initTables();

		$this->debug('Init locations table data');
		$this->importSourceDataToTable($this->detector->getLocationsTable(), $this->glcLocationsSource);
		$this->debug('Init IP masks table data');
		$this->importSourceDataToTable($this->detector->getIpMasksTable(), $this->glcBlocksSource);
		$this->debug('Update complete');
	}

	protected function importSourceDataToTable($table, IpLoc_Base_Csv $csv) {
		$db = $this->detector->getDb();
		$csv->reset();
		$csv->getRows(2); // ignore first rows with Copyright and columns names
		$start = microtime(true);
		while( $rows = $csv->getRows($this->bulkInsertLimit)) {
			$db->multiInsert($table, $rows);
			$timeSpent = microtime(true) - $start;
			$percentComplete = $csv->getCurrentPosition() * 100 / $csv->getTotalPositions();
			$timeLeft = $timeSpent * (100 - $percentComplete) / $percentComplete;
			$this->debug('Complete ' . floor($percentComplete) . '% Timeleft ' . floor($timeLeft), true);
		}
	}
}