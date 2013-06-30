<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Service_Curl {

	public static function call($url, $fields = array(), $cookiejar = false) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, Service_Config::get('timeout', 'curl'));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, Service_Config::get('connect_timeout', 'curl'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		if (Service_Config::get('ignore_invalid_ssl', 'curl')) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		}

		if (count($fields)) {
			curl_setopt($curl, CURLOPT_POST, count($fields));
			curl_setopt($curl, CURLOPT_POSTFIELDS, self::curlPostParams($fields));
		}

		if ($cookiejar) {
			curl_setopt($curl, CURLOPT_COOKIEJAR, Service_Config::get('data_dir') . '/.ptpcookie');
			curl_setopt($curl, CURLOPT_COOKIEFILE, Service_Config::get('data_dir') . '/.ptpcookie');
		}

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if (($code >= 200) && ($code < 300)) {
			if ($result) {
				if (@json_decode($result) !== false) {
					return json_decode($result, true);
				}
				throw new Exception('cURL: Broken response - ' . $url);
			}
			throw new Exception('cURL: Missing response - ' . $url);
		}
		throw new Exception('cURL: Recieved HTTP code ' . $code . ' - ' . $url);
	}

	private static function curlPostParams($params) {
		$return = '';
		foreach ($params as $k => $p) {
			$return .= $k . '=' . urlencode($p) . '&';
		}
		rtrim($return, '&');
		return $return;
	}

}

?>
