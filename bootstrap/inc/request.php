<?php
class Request
{
    protected $method;
    protected $host;
    protected $url;
    protected $router;
    protected $query;
    protected $data;
    protected $files;
    protected $request_headers;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->host = $_SERVER['SERVER_NAME'];
        $this->url = $_SERVER['REQUEST_URI'];
        $this->query = $_GET ?? [];
        $this->data = $_POST ?? [];
        $this->files = $_FILES ?? [];
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
                if (strtolower(substr($name, 0, 5)) == 'http_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            $this->request_headers = $headers;
        } else {
            $this->request_headers = getallheaders();
        }
        if ( empty($this->data) ) {
            $this->data = $this->getJSONpayload();
        }
        return $this;
    }

    public function get_header(string|null $headerName = null) : array|string|null
    {
        if(empty($headerName)) {
            return $this->request_headers;
        } else {
            foreach($this->request_headers as $key => $header) {
                if(strtolower($key) == strtolower($headerName)) {
                    return $this->request_headers[$key];
                }
            }
            return null;
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isMethod($method)
    {
        return $this->method == $method;
    }

    public function isPost()
    {
        return $this->isMethod('POST');
    }

    public function isGet()
    {
        return $this->isMethod('GET');
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getQuery($key = null, $default_value = '')
    {
        if (empty($key)) {
            return $this->query;
        } else {
            if ( isset($this->query[$key]) ) {
                return $this->query[$key];
            } else {
                return $default_value;
            }
        }
    }

    public function getData($key = null, $default_value = '')
    {
        if(empty($key)) {
            return $this->data;
        } else {
            if ( isset($this->data[$key]) ) {
                return $this->data[$key];
            } else {
                return $default_value;
            }
        }
        
    }

    public function getJSONpayload()
    {
        $request_body = file_get_contents('php://input');
        return json_decode($request_body, true);
    }

    public function getFileUploadByName($name)
    {
        if ( !empty($this->files[$name]) ) {
            return $this->files[$name];
        } else {
            return null;
        }
    }
}
?>