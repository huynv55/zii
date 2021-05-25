<?php

class HttpClientService {
	const BASE_URL = '';
	const CURL_TIMEOUT = 360;

	public static function client($headers = []) {
		$client = new HttpClient(['base_url' => self::BASE_URL, 'timeout' => self::CURL_TIMEOUT], $headers);
		return $client;
	}

	public static function get($url, $query_data = null, $headers = null) {
		$result = self::client()->get($url, $query_data, $headers);
		return $result->done();
	}

	public static function post($url, $post_fields = null, $headers = null) {
		$result = self::client()->post($url, $post_fields, $headers);
		return $result->done();
	}

	public static function put($url, $put_fields = null, $headers = null) {
		$result = self::client()->put($url, $put_fields, $headers);
		return $result->done();
	}

	public static function delete($url, $delete_fields = null, $headers = null) {
		$result = self::client()->delete($url, $delete_fields, $headers);
		return $result->done();
	}

	public static function requestPostPayload($url, $data = null, $headers = []) {
		$result = self::client()->requestPostPayload($url, $data, $headers);
		return $result->done();
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