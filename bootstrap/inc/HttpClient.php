<?php

class HttpClient {
    protected $config;
    protected $headers;
    protected $client;
    protected $response;
    protected $exception;

    public function __construct($config = [], $headers = []) {
        if(empty($config['base_url'])) {
            $config['base_url'] = UrlHelper::base();
        }
        if(empty($config['timeout'])) {
            $config['timeout'] = 360;
        }
        $this->client = new \GuzzleHttp\Client([
			// Base URI is used with relative requests
			'base_uri' => $config['base_url'],
			// You can set any number of default request options.
			'timeout'  => $config['timeout'],
			'headers' => $headers
		]);
        $this->config = $config;
        $this->headers = $headers;
        $this->response = null;
        $this->exception = null;
		return $this;
	}

	public function getClient() {
		return $this->client;
	}

    public function getResponse() {
        if (!empty($this->exception)) {
            return null;
        }
        if (empty($this->response)) {
            return null;
        }
		return $this->response;
	}

    public function done() {
		$res = $this->getResponse();
        if(!empty($res)) {
            return (string) $res->getBody();
        } else {
            return null;
        }
	}

    public function status() {
        $res = $this->getResponse();
        if(!empty($res)) {
            return $res->getStatusCode();
        } else {
            return null;
        }
	}

    public function getHeadersResponse() {
        $res = $this->getResponse();
        if(!empty($res)) {
            return $res->getHeaders();
        } else {
            return null;
        }
	}

	public function get($url, $query_data = null, $headers = null) {
        try {
            $res = $this->client->request('GET', $url, ['query' => $query_data, 'headers' => $headers]);
            $this->response = $res;
            $this->exception = null;
        } catch (Exception $e) {
            $this->exception = $e;
            $this->response = null;
        }
        return $this;
	}

	public function post($url, $post_fields = null, $headers = null) {
        try {
            $res = $this->client->request('POST', $url, ['form_params' => $post_fields, 'headers' => $headers]);
            $this->response = $res;
            $this->exception = null;
        } catch (Exception $e) {
            $this->exception = $e;
            $this->response = null;
        }
        return $this;
	}

	public function put($url, $put_fields = null, $headers = null) {
        try {
            $res = $this->client->request('PUT', $url, ['form_params' => $put_fields, 'headers' => $headers]);
            $this->response = $res;
            $this->exception = null;
        } catch (Exception $e) {
            $this->exception = $e;
            $this->response = null;
        }
        return $this;
	}

	public function delete($url, $delete_fields = null, $headers = null) {
        try {
            $res = $this->client->request('DELETE', $url, ['form_params' => $delete_fields, 'headers' => $headers]);
            $this->response = $res;
            $this->exception = null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->exception = $e;
            $this->response = null;
        }
        return $this;
	}

	public function requestPostPayload($url, $data = null, $headers = []) {
        try {
            $res = $this->client->request('POST', $url, ['json' => $data, 'headers' => $headers]);
            $this->response = $res;
            $this->exception = null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->exception = $e;
            $this->response = null;
        }
        return $this;
	}
}
?>