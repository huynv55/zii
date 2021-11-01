<?php
function getListFileInDir($dir, &$results = array()) {
    if(empty($dir)) {
        return $results;
    }
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

function str_Limit($str, $limit) {
    $textTruncated = '';
    if( strlen($str) > $limit ) {
        $textTruncated = substr($str, 0, $limit).'...';
    } else {
        $textTruncated = $str;
    }
    return $textTruncated;
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

/**
 * Wraps array_rand call with additional checks
 *
 * TLDR; not so radom as you'd wish.
 *
 * NOTICE: the closer you get to the input arrays length, for the n parameter, the  output gets less random.
 * e.g.: array_random($a, count($a)) == $a will yield true
 * This, most certainly, has to do with the method used for making the array random (see other comments).
 *
 * @throws OutOfBoundsException – if n less than one or exceeds size of input array
 *
 * @param array $array – array to randomize
 * @param int $n – how many elements to return
 * @return array
 */
function array_random(array $array, int $n = 1): array
{
    if ($n < 1 || $n > count($array)) {
        throw new OutOfBoundsException();
    }

    return ($n !== 1)
        ? array_values(array_intersect_key($array, array_flip(array_rand($array, $n))))
        : array($array[array_rand($array)]);
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