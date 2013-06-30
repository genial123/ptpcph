<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Service_Config {

	private static $config = array();
	private static $defaults = array(
		'ptp' => array(
			'login_url' => 'https://tls.passthepopcorn.me/ajax.php?action=login',
			'search_url' => 'https://tls.passthepopcorn.me/torrents.php?json=noredirect&searchstr=%s&order_by=relevance&order_way=descending'
		),
		'max_readds' => 3,
		'max_age' => 86400,
		'run_every' => 60,
		'test_run' => true,
		'curl' => array(
			'connect_timeout' => 5,
			'timeout' => 15,
			'ignore_invalid_ssl' => false
		),
		'data_dir' => 'data',
		'log_dir' => 'logs'
	);

	public static function set($config) {
		self::$config = $config;
	}

	public static function get($key, $section = false) {
		if ($section) {
			if (isset(self::$config[$section][$key])) {
				return self::$config[$section][$key];
			}
			return self::$defaults[$section][$key];
		}
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		return self::$defaults[$key];
	}

}

?>
