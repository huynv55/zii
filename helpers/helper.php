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

$listFileHelpers = getListFileInDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'inc');
foreach($listFileHelpers as $help) {
	require $help;
}
?>