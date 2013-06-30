<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Provider_CP {

	public function scanHistory() {
		$url = sprintf('%s/%s/movie.list/', Service_Config::get('url', 'cp'), Service_Config::get('apikey', 'cp'));
		$movies = Service_Curl::call($url);

		$results = array();
		$time = time();

		if (isset($movies['movies']) && $movies['movies'] && count($movies['movies'])) {
			foreach ($movies['movies'] as $movie) {
				if (isset($movie['releases']) && $movie['releases'] && count($movie['releases'])) {
					foreach ($movie['releases'] as $release) {
						if (isset($release['info']['provider']) && $release['info']['provider'] && ($release['info']['provider'] == 'PassThePopcorn')) {
							if (isset($release['status_id']) && $release['status_id'] && ($release['status_id'] == 4)) {
								if (isset($release['last_edit']) && $release['last_edit'] && ($release['last_edit'] > ($time-Service_Config::get('max_age')))) {
									array_push($results, array(
										'imdb' => $movie['library']['info']['imdb'],
										'ptp' => $release['info']['id'],
										'title' => $movie['library']['info']['original_title'],
										'date' => $release['last_edit']
									));
								}
							}
						}
					}
				}
			}
		}

		return $results;
	}

	public function reAddMovie($imdb) {
		Service_Logger::log('[~] Re-adding invalid CouchPotato movie so it can re-scan PTP!');
		$url = sprintf('%s/%s/movie.add/?identifier=%s&ignore_previous=1', Service_Config::get('url', 'cp'), Service_Config::get('apikey', 'cp'), $imdb);

		if (!Service_Config::get('test_run')) {
			$add = Service_Curl::call($url);
			if (isset($add['success']) && ($add['success'] == 'true')) {
				Service_FS::incImdb($imdb, true);
				Service_Logger::log('[+] Movie re-added, should be re-snatching a valid torrent right about now! :)');
			}
		}
		else {
			Service_Logger::log('[+] Running in TEST-MODE, not re-adding anything');
		}
	}

}

?>
