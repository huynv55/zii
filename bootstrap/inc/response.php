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

    public function useLibJs($js) {
        if(empty($this->data_into_view[View::LIB_JS])) {
            $this->data_into_view[View::LIB_JS] = [];
        }
        if(is_array($js)) {
            foreach ($js as $key => $j) {
                $this->data_into_view[View::LIB_JS][] = $j;
            }
        } else if(is_string($js)) {
            $this->data_into_view[View::LIB_JS][] = $js;
        }
        return $this;
    }

    public function useLibCss($css) {
        if(empty($this->data_into_view[View::LIB_CSS])) {
            $this->data_into_view[View::LIB_CSS] = [];
        }
        if(is_array($css)) {
            foreach ($css as $key => $c) {
                $this->data_into_view[View::LIB_CSS][] = $c;
            }
        } else if(is_string($css)) {
            $this->data_into_view[View::LIB_CSS][] = $css;
        }
        return $this;
    }

    public function useJs($js) {
        if(empty($this->data_into_view[View::JS])) {
            $this->data_into_view[View::JS] = [];
        }
        if(is_array($js)) {
            foreach ($js as $key => $j) {
                $this->data_into_view[View::JS][] = $j;
            }
        } else if(is_string($js)) {
            $this->data_into_view[View::JS][] = $js;
        }
        return $this;
    }

    public function useCss($css) {
        if(empty($this->data_into_view[View::CSS])) {
            $this->data_into_view[View::CSS] = [];
        }
        if(is_array($css)) {
            foreach ($css as $key => $c) {
                $this->data_into_view[View::CSS][] = $c;
            }
        } else if(is_string($css)) {
            $this->data_into_view[View::CSS][] = $css;
        }
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

    public function getDataIntoView($key = null) {
        if(empty($key)) {
            return $this->data_into_view;
        } else {
            return $this->data_into_view[$key];
        }
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
            if ($this->content != $_SERVER['REQUEST_URI'] ) {
                http_response_code(301);
                header("Location: ".$this->content);
                exit();
            }
        } else if ($this->type == self::TYPE_RESPONSE_VIEW) {
            header('Content-Type: text/html');
            http_response_code($this->status);
            echo $this->content;
            die();
        } else if ($this->type == self::TYPE_RESPONSE_JSON) {
            header('Content-Type: application/json');
            http_response_code($this->status);
            echo $this->content;
            die();
        } else {
            http_response_code(500);
            die('Empty');
        }
    }
}
?>