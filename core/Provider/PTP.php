<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Provider_PTP {

	public function login() {
		if (!$this->loginAction()) {
			if (!$this->loginAction(true)) {
				return false;
			}
		}
		return true;
	}

	private function loginAction($full = false) {
		$data = array();
		if ($full) {
			$data = array(
				'username' => Service_Config::get('username', 'ptp'),
				'password' => Service_Config::get('password', 'ptp'),
				'passkey' => Service_Config::get('passkey', 'ptp'),
				'keeplogged' => '1',
				'login' => 'Login'
			);
		}

		$login = Service_Curl::call(Service_Config::get('login_url', 'ptp'), $data, true);

		if (isset($login['Result']) && ($login['Result'] == 'Ok')) {
			return true;
		}
		return false;
	}

	public function checkSnatch($imdb, $ptp) {
		$data = Service_Curl::call(sprintf(Service_Config::get('search_url', 'ptp'), $imdb), array(), true);

		if (isset($data['Movies']) && count($data['Movies'])) {
			foreach ($data['Movies'] as $movie) {
				if (isset($movie['Torrents']) && count($movie['Torrents'])) {
					foreach ($movie['Torrents'] as $torrent) {
						if (isset($torrent['Id']) && $torrent['Id']) {
							if ($torrent['Id'] == $ptp) {
								return true;
							}
						}
					}
				}
			}
			return false;
		}
		throw new Exception('Got a weird result from PTP, are we still logged in?');
	}

}

?>
