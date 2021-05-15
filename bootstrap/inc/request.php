<?php
class Request {
    protected $method;
    protected $host;
    protected $url;
    protected $router;
    protected $query;
    protected $data;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->host = $_SERVER['SERVER_NAME'];
        $this->url = $_SERVER['REQUEST_URI'];
        $this->query = $_GET;
        $this->data = $_POST;
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

    public function isGet($method) {
        return $this->isMethod('GET');
    }

    public function getHost() {
        return $this->host;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getQuery() {
        return $this->query;
    }

    public function getData() {
        return $this->data;
    }
}
?>