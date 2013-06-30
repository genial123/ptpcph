<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Cronjob {

	private $repeatable;
	private $interval;
	private $start;

	public function __construct() {
		$this->repeatable = (is_numeric(Service_Config::get('run_every')) && (Service_Config::get('run_every') > 0));
		$this->interval = (Service_Config::get('run_every') ? Service_Config::get('run_every') : 30)*60;

		if ($this->repeatable) {
			Service_Logger::log('[*] Run-interval set to: ' . $this->interval . ' seconds');
		}
	}

	public function start() {
		$this->start = time();

		$run = new Runner();
		$run->run();

		unset($run);

		if ($this->repeatable) {
			$this->sleep();
		}
	}

	private function sleep() {
		$sleep = ($this->start+$this->interval)-time();
		Service_Logger::log('Sleeping for ' . $sleep . ' seconds until next run' . "\n", true);

		sleep($sleep);
		$this->start();
	}

}

?>
