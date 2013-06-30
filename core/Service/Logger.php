<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Service_Logger {

	public static function log($text, $prenl = false, $tofile = true) {
		$o = '';
		if ($prenl) $o .= "\n";
		$o .= '[' . date('Y-m-d H:i') . '] ' . $text . "\n";

		$file = sprintf('%s/%s.log', Service_Config::get('log_dir'), date('Y-m-d'));
		if ($tofile && (is_writable($file) || !file_exists($file)) && is_writable('logs')) {
			file_put_contents($file, $o, FILE_APPEND);
		}

		echo $o;
	}

}

?>
