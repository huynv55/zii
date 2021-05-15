<?php
class Response {
    const TYPE_RESPONSE_VIEW = 'view';
    const TYPE_RESPONSE_JSON = 'json';
    const TYPE_RESPONSE_REDIRECT = 'redirect';

    private $content;
    private $status;
    private $type;
    private $headers;
    private $layout;
    private $data_into_view;

    public function __construct() {
        $this->content = '';
        $this->status = 200;
        $this->type = self::TYPE_RESPONSE_VIEW;
        $this->headers = [];
        $this->layout = null;
        $this->data_into_view = [];
        return $this;
    }

    public function setLayout($view_layout) {
        $this->layout = $view_layout;
        return $this;
    }

    public function setContent($res_content) {
        $this->content = $res_content;
        return $this;
    }

    public function setType($res_type) {
        $this->type = $res_type;
        return $this;
    }

    public function setStatus($res_status) {
        $this->status = intval($res_status);
        return $this;
    }

    public function addHeader($header, $value) {
        $this->headers[strval($header)] =  strval($value);
        return $this;
    }

    public function setDataIntoView($data = []) {
        foreach ($data as $key => $value) {
            $this->data_into_view[$key] = $value;
        }
    }

    public function view($view, $data = []) {
        $this->setDataIntoView($data);
        if(!empty($this->layout)) {
            $this->data_into_view['yield_content'] = View::render($view, $this->data_into_view);
            $html = View::render($this->layout, $this->data_into_view);
        } else {
            $html = View::render($view, $this->data_into_view);
        }
        return $this->setType(self::TYPE_RESPONSE_VIEW)->setContent($html);
    }

    public function json($json_data, $status = 200) {
        return $this->setType(self::TYPE_RESPONSE_JSON)->setStatus($status)->setContent(json_encode($json_data));
    }

    public function redirect($url) {
        return $this->setType(self::TYPE_RESPONSE_REDIRECT)->setStatus(301)->setContent($url);
    }

    public function redirectRouter($controller, $action = 'index', $params = []) {
        $url = UrlHelper::base() . "/" . $controller . "/" . $action;
        if(!empty($params)) {
            foreach ($params as $key => $value) {
                $url = $url."/".$value;
            }
        }
        return $this->redirect($url);
    }

    public function send() {
        foreach($this->headers as $header => $value) {
            header($header.": ".$value);
        }
        if( $this->type == self::TYPE_RESPONSE_REDIRECT ) {
            http_response_code(301);
            header("Location: ".$this->content);
            exit();
        } else if ($this->type == self::TYPE_RESPONSE_VIEW) {
            header('Content-Type: text/html');
            http_response_code($this->status);
            echo $this->content;
            die();
        } if ($this->type == self::TYPE_RESPONSE_JSON) {
            header('Content-Type: application/json');
            http_response_code($this->status);
            echo $this->content;
            die();
        }
        http_response_code(500);
        die('Empty');
    }
}
?>