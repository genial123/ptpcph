<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Service_FS {

	private static $logdir;
	private static $datadir;
	private static $imdbfile;
	private static $lockfile;

	public static function init() {
		self::$logdir = Service_Config::get('log_dir');
		self::$datadir = Service_Config::get('data_dir');
		self::$imdbfile = self::$datadir . '/imdb.json';
		self::$lockfile = self::$datadir . '/lock';

		if (!file_exists(self::$logdir)) {
			mkdir(self::$logdir);
		}
		if (!file_exists(self::$datadir)) {
			mkdir(self::$datadir);
		}

		self::initImdbStore();
	}

	private static function initImdbStore() {
		if ((is_writable(self::$imdbfile) || !file_exists(self::$imdbfile)) && is_writable(self::$datadir)) {
			if (!file_exists(self::$imdbfile)) {
				file_put_contents(self::$imdbfile, json_encode(array()));
			}
			return true;
		}
		throw new Exception('Cannot write to data-folder or imdb.json, exiting.');
	}

	public static function incImdb($imdb, $save = false) {
		$data = file_get_contents(self::$imdbfile);
		if ($data && (@json_decode($data) !== false)) {
			$json = json_decode($data, true);
			if (!isset($json[$imdb])){
				$json[$imdb] = 0;
			}

			if (!Service_Config::get('max_readds') || ($json[$imdb] < Service_Config::get('max_readds'))) {
				if ($save) {
					$json[$imdb]++;
					file_put_contents(self::$imdbfile, json_encode($json));
				}
				return true;
			}
		}
		return false;
	}

	public static function isLocked() {
		if (file_exists(self::$lockfile)) {
			return true;
		}
		return false;
	}

	public static function setLock($unset = false) {
		if ((is_writable(self::$lockfile) || !file_exists(self::$lockfile)) && is_writable(self::$datadir)) {
			if ($unset) {
				unlink(self::$lockfile);
			}
			else {
				touch(self::$lockfile);
			}
			return true;
		}
		throw new Exception('Cannot write to data-folder or lock-file, exiting.');
	}

}

?>
