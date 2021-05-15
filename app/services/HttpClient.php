<?php

class HttpClient {

	const CURL_TIMEOUT = 360;

	public static function get($url, $query_data = null, $headers = null) {
		return self::request('GET', $url, $query_data, $headers);
	}

	public static function post($url, $post_fields = null, $headers = null) {
		return self::request('POST', $url, $post_fields, $headers);
	}

	public static function put($url, $put_fields = null, $headers = null) {
		return self::request('PUT', $url, $put_fields, $headers);
	}

	public static function delete($url, $delete_fields = null, $headers = null) {
		return self::request('DELETE', $url, $delete_fields, $headers);
	}

	public static function upload($url, $field, $path_file, $data = null, $headers = null) {
		if ($data && !empty($data)) {
			$data[$field] = curl_file_create($path_file);
		} else {
			$data = [];
			$data[$field] = curl_file_create($path_file);
		}
		return self::request('POST', $url, $data, $headers);
	}

	public static function requestPostPayload($url, $data = null, $headers = array('Content-Type:application/json')) {
		$ch = curl_init();

		// don't return headers
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		if ($data && !empty($data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}
		if ($headers && !empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
		$body = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			$msg = 'Error:' . curl_error($ch);
			trigger_error( $msg, E_USER_WARNING);
			return null;
		}
		curl_close($ch);
		return compact('status', 'body');
	}


	public static function request($method, $url, $data = null, $headers = null) {
		$ch = curl_init();

		// don't return headers
		curl_setopt($ch, CURLOPT_HEADER, false);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			if ($data && !empty($data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}

		} else if ($method == 'GET') {
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			if (!empty($data)) {
				$request_url = $url . '?' . http_build_query($data);
				curl_setopt($ch, CURLOPT_URL, $request_url);
			} else {
				curl_setopt($ch, CURLOPT_URL, $url);
			}

		} else {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			if ($data && !empty($data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
		}
		if ($headers && !empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
		$body = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			$msg = 'Error:' . curl_error($ch);
			trigger_error( $msg, E_USER_WARNING);
			return null;
		}
		curl_close($ch);
		return compact('status', 'body');
	}
}
?>