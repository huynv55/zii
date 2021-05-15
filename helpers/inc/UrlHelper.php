<?php
class UrlHelper {

	public static function asset($url = '') {
		$host = $_SERVER['SERVER_NAME'];
		$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$host}";
		$url = trim($url);
		return $base_url."/".trim($url, '/');
	}

	public static function base() {
		$host = $_SERVER['SERVER_NAME'];
		$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$host}";
		return $base_url;
	}

	public static function mix($file = null) {
		$mix = file_get_contents(ROOT_PATH.'mix-manifest.json');
		if (empty($mix)) {
			return null;
		}
		$mix_json = json_decode($mix, true);
		$path = '/public'.$file;
		if (!empty($mix_json[$path])) {
			return self::asset(str_replace('/public', '', $mix_json[$path]));
		}
		return null;
	}

	public static function next_page_link($page, $pageMax, $param = 'page') {
		if(empty($param)) {
			$param = 'page';
		}
		$queryStr = $_GET;
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$current_url = explode('?', $actual_link)[0];
		if ($page <= $pageMax) {
			$queryStr[$param] = $page + 1;
			return $current_url."?".http_build_query($queryStr);
		} else {
			$queryStr[$param] = $page;
			return $current_url."?".http_build_query($queryStr);
		}
	}

	public static function pre_page_link($page, $param = 'page') {
		if(empty($param)) {
			$param = 'page';
		}
		$queryStr = $_GET;
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$current_url = explode('?', $actual_link)[0];
		if ($page > 0) {
			$queryStr[$param] = $page - 1;
			return $current_url."?".http_build_query($queryStr);
		} else {
			$queryStr[$param] = $page;
			return $current_url."?".http_build_query($queryStr);
		}
	}
}
?>