<?php

/**
 * @see https://github.com/barbushin/iploc
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
interface IpLoc_Base_SourceInterface {

	public function reset();

	public function getRows($count);

	public function getCurrentPosition();

	public function getTotalPositions();
}