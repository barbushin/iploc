<?php

/**
 * @see http://code.google.com/p/iploc
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
class IpLoc_Base_Csv implements IpLoc_Base_SourceInterface {

	protected $fileHandle;
	protected $fileSize;
	protected $delimiter;
	protected $enclosure;
	protected $lineLimit;
	protected $columnsNames;

	public function __construct($filepath, $skipRows = 0, $lineLimit = 0, $delimiter = ',', $enclosure = '"') {
		if(!is_file($filepath)) {
			throw new Exception('File "' . $filepath . '" not found');
		}
		if(!is_readable($filepath)) {
			throw new Exception('File "' . $filepath . '" is not readable');
		}
		$this->fileHandle = fopen($filepath, 'r');
		$this->fileSize = filesize($filepath);
		$this->skipRows = $skipRows;
		$this->delimiter = $delimiter;
		$this->enclosure = $enclosure;
		$this->lineLimit = $lineLimit;
		$this->reset();
	}

	public function setColumnsNames(array $columnsNames) {
		$this->columnsNames = $columnsNames;
	}

	public function reset() {
		rewind($this->fileHandle);
		$this->getRows($this->skipRows);
	}

	public function getCurrentPosition() {
		return ftell($this->fileHandle);
	}

	public function getTotalPositions() {
		return $this->fileSize;
	}

	public function getRows($count) {
		$rows = array ();
		for(; $count; $count--) {
			$row = fgetcsv($this->fileHandle, $this->lineLimit, $this->delimiter, $this->enclosure);
			if($row === false) {
				break;
			}
			if($this->columnsNames) {
				$newRow = array ();
				foreach($this->columnsNames as $i => $column) {
					$newRow[$column] = isset($row[$i]) ? $row[$i] : null;
				}
				$rows[] = $newRow;
			}
			else {
				$rows[] = $row;
			}
		}
		return $rows;
	}

}