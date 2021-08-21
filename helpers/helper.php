<?php
function getListFileInDir($dir, &$results = array()) {
	$files = scandir($dir);
	foreach ($files as $key => $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		if (is_file($path)) {
			$results[] = $path;
		} else if ($value != "." && $value != "..") {
			getListFileInDir($path, $results);
			//$results[] = $path;
		}
	}
	return $results;
}

function h($str) {
	return htmlspecialchars($str);
}

function env($key, $default_value = null) {
	if(isset($_ENV[$key])) {
		return $_ENV[$key];
	} else if(isset($_SERVER[$key])) {
		return $_SERVER[$key];
	} else {
		return $default_value;
	}
}

function get_timezone_offset($remote_tz, $origin_tz = null) {
	if($origin_tz === null) {
		if(!is_string($origin_tz = date_default_timezone_get())) {
			return false; // A UTC timestamp was returned -- bail out!
		}
	}
	$origin_dtz = new DateTimeZone($origin_tz);
	$remote_dtz = new DateTimeZone($remote_tz);
	$origin_dt = new DateTime("now", $origin_dtz);
	$remote_dt = new DateTime("now", $remote_dtz);
	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	return $offset;
}

$listFileHelpers = getListFileInDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'inc');
foreach($listFileHelpers as $help) {
	require $help;
}
?>