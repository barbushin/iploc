<?php

/**
 * @see http://code.google.com/p/iploc
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
interface IpLoc_Base_SourceInterface {

	public function reset();

	public function getRows($count);

	public function getCurrentPosition();

	public function getTotalPositions();
}