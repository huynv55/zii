<?php
class Request {
    protected $method;
    protected $host;
    protected $url;
    protected $router;
    protected $query;
    protected $data;
    protected $files;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->host = $_SERVER['SERVER_NAME'];
        $this->url = $_SERVER['REQUEST_URI'];
        $this->query = $_GET;
        $this->data = $_POST;
        $this->files = $_FILES;
        if ( empty($this->data) ) {
            $this->data = $this->getJSONpayload();
        }
    }

    public function getMethod() {
        return $this->method;
    }

    public function isMethod($method) {
        return $this->method == $method;
    }

    public function isPost() {
        return $this->isMethod('POST');
    }

    public function isGet() {
        return $this->isMethod('GET');
    }

    public function getHost() {
        return $this->host;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getQuery($key = null) {
        if (empty($key)) {
            return $this->query;
        } else {
            if ( !empty($this->query[$key]) ) {
                return $this->query[$key];
            } else {
                return '';
            }
        }
    }

    public function getData($key = null) {
        if(empty($key)) {
            return $this->data;
        } else {
            if ( !empty($this->data[$key]) ) {
                return $this->data[$key];
            } else {
                return '';
            }
        }
        
    }

    public function getJSONpayload() {
        $request_body = file_get_contents('php://input');
        return json_decode($request_body, true);
    }

    public function getFileUploadByName($name) {
        if ( !empty($this->files[$name]) ) {
            return $this->files[$name];
        } else {
            return null;
        }
    }
}
?>