<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Service_Format {

	public static function timeDiff($then) {
		$d = time()-$then;
		return self::ttt($d);
	}

	public static function ttt($d) {
		if ($d < 60)
			return $d . ' seconds';
		if ($d < 3600)
			return round($d/60, 2) . ' minutes';
		if ($d < 86400)
			return round($d/3600, 2) . ' hours';
		return round($d/86400, 2) . ' days';
	}

}

?>
