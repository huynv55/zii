<?php
abstract class Config {
	public static function get($config) {
		$conf = require realpath(__DIR__.'/../../app/config/'.$config.'.php');
		return $conf;
	}
}
?>