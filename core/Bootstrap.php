<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

class Bootstrap {
	
	public function __construct() {
		spl_autoload_register(array($this, 'autoload'));
	}

	private function autoload($class) {
		$file = 'core/' . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		if (file_exists($file)) {
			if (is_readable($file)) {
				require_once($file);
				return true;
			}
		}
		return false;
	}

}

$b = new Bootstrap();

?>
