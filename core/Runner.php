<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Runner {

	private $imdbCache;

	public function __construct() {
		$this->imdbCache = array();
	}

	public function run() {
		try {
			Service_FS::init();

			if (!Service_FS::isLocked()) {
				Service_FS::setLock();

				$cp = new Provider_CP();
				$ptp = new Provider_PTP();

				if (Service_Config::get('test_run')) {
					Service_Logger::log('=================================================');
					Service_Logger::log('========= TEST-MODE, NOT DOING ANYTHING =========');
					Service_Logger::log('=================================================');
				}

				if ($ptp->login()) {
					Service_Logger::log('[*] Starting a new run, timelimit: ' . Service_Config::get('max_age') . ' (' . Service_Format::ttt(Service_Config::get('max_age')) . ')', true);

					$scan = $cp->scanHistory();
					$count = count($scan);
					Service_Logger::log('[*] Found ' . $count . ' snatch(es)');
					
					if ($count) {
						$sleepy = false;
						$x = 0;

						foreach ($scan as $s) {
							if ($s['imdb'] && $s['ptp'] && $s['title'] && $s['date']) {
								Service_Logger::log('[*] Checking snatch ' . ++$x . '/' . $count . ' (IMDB: ' . $s['imdb'] . ' - PTP: ' . $s['ptp'] . ')', true);

								if (!in_array($s['imdb'], $this->imdbCache)) {
									if (Service_FS::incImdb($s['imdb'])) {
										Service_Logger::log('[>] About to check: "' . $s['title'] . '" - ' . Service_Format::timeDiff($s['date']) . ' old');

										if ($sleepy) {
											Service_Logger::log('Sleeping 10 seconds to not flood PTP...', false, false);
											sleep(10);
										}
										$sleepy = true;

										if ($ptp->checkSnatch($s['imdb'], $s['ptp'])) {
											Service_Logger::log('[+] No problem, it still exists on PTP!');
										}
										else {
											Service_Logger::log('[!] Woops, this no longer exists on PTP - trying to invalidate...');
											$cp->reAddMovie($s['imdb']);
										}
									}
									else {
										Service_Logger::log('[+] Movie has reached max re-adds, ignoring!');
									}

									array_push($this->imdbCache, $s['imdb']);
								}
								else {
									Service_Logger::log('[+] Cache for ' . $s['ptp'] . ' said it has already been checked, ignoring!');
								}
							}
						}
					}
				}
				else {
					Service_Logger::log('[!] Unauthorized to enter PTP, sure credentials are corrent?');
				}

				Service_FS::setLock(true);
			}
			else {
				Service_Logger::log('[!] Lockfile exists');
			}
		}
		catch (Exception $e) {
			Service_Logger::log('[EXCEPTION] ' . $e->getMessage());
			Service_FS::setLock(true);
		}
	}

}

?>
