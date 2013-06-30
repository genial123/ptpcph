<?php
/**
 *
 * @package PTPCPH
 * @version 0.1
 * @author genial
 * @copyright Copyright (c) 2013 genial
 *
 */

include_once('core/Bootstrap.php');
include_once('config.inc.php');

if (isset($config)) {
	Service_Config::set($config);

	$cron = new Cronjob();
	$cron->start();
	
}

?>
