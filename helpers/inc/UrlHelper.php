<?php
class UrlHelper {

	/**
	* Function used to create a slug associated to an "ugly" string.
	*
	* @param string $string the string to transform.
	*
	* @return string the resulting slug.
	*/
	public static function createSlug($string) {
		$string = preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$string);
		$table = array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
		);
		// -- Remove duplicated spaces
		$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);
		// -- Returns the slug
		return strtolower(strtr($stripped, $table));
	}

	public static function getUrlBack() {
		return !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : self::base();
	}

	public static function getProtocol() {
		if ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			return 'https';
		}
		if ( !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
			return 'https';
		}
		return 'http';
	}

	public static function fullUrl() {
		return self::base().$_SERVER['REQUEST_URI'];
	}

	public static function asset($url = '') {
		$host = $_SERVER['SERVER_NAME'];
		$base_url = self::getProtocol() . "://{$host}";
		$url = trim($url);
		return $base_url."/".trim($url, '/');
	}

	public static function base() {
		$host = $_SERVER['SERVER_NAME'];
		$base_url = self::getProtocol() . "://{$host}";
		return $base_url;
	}

	public static function hostname() {
		return $_SERVER['SERVER_NAME'];
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