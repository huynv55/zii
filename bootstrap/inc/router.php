<?php
class Router {

	protected $uri;

	protected $controller;

	protected $action;

	protected $params = [];

	public static $routers = [];
	public static $prefixs = [];

	protected function getRequestUri() {
		return $_SERVER['REQUEST_URI'];
	}

	protected function getHost() {
		return $_SERVER['SERVER_NAME'];
	}

	protected function getMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * 
	 * @return mixed
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * 
	 * @return mixed
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * 
	 * @return mixed
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * 
	 * @return mixed
	 */
	public function getParams() {
		return $this->params;
	}

	public function getRoutes() {
		return self::$routers;
	}

	public static function get($path, $controller_name, $action_name) {
		self::addRouter($path, 'GET', $controller_name, $action_name);
	}

	public static function post($path, $controller_name, $action_name) {
		self::addRouter($path, 'POST', $controller_name, $action_name);
	}

	public static function delete($path, $controller_name, $action_name) {
		self::addRouter($path, 'DELETE', $controller_name, $action_name);
	}

	public static function put($path, $controller_name, $action_name) {
		self::addRouter($path, 'PUT', $controller_name, $action_name);
	}

	public static function options($path, $controller_name, $action_name) {
		self::addRouter($path, 'OPTIONS', $controller_name, $action_name);
	}

	public static function all($path, $controller_name, $action_name) {
		self::get($path, $controller_name, $action_name);
		self::post($path, $controller_name, $action_name);
		self::delete($path, $controller_name, $action_name);
		self::put($path, $controller_name, $action_name);
		self::options($path, $controller_name, $action_name);
	}

	public static function prefix($prefix, $func) {
		$prefix = trim($prefix);
		array_push(self::$prefixs , trim($prefix, '/'));
		$func();
		if ( !empty(self::$prefixs) ) {
			array_pop(self::$prefixs);
		}
	}

	public static function addRouter($path, $type, $controller_name, $action_name) {
		$path = trim($path);
		if ( !empty(self::$prefixs) ) {
			$path =  implode('/', self::$prefixs)."/".trim($path);
		}
		$path_parts = explode('/', $path);
		$r = self::$routers;
		$tmp_r = &$r;
		while(!empty($path_parts)) {
			$key_route = array_shift($path_parts);
			if (!empty($key_route)) {
				$tmp_r = &$tmp_r[$key_route];
				if ( empty($tmp_r) ) {$tmp_r = [];}
			}
		}
		$tmp_r[$type] = ['controller' => $controller_name, 'action' => $action_name];
		self::$routers = $r;
	}

	public static function defaultMap($controller_name, $action_name) {
		self::$routers['/'] = [
			'controller' => $controller_name,
			'action' => $action_name
		];
	}

	public function __construct($uri = null) {
		if(empty($uri)) $uri = $this->getRequestUri();
		$this->uri = trim($uri, "/");
		//defaults
		$routes = $this->getRoutes();
		$method = $this->getMethod();
		$controller_route = '';
		$action_route = '';
		$tmp_uri = explode('?', $this->uri);
		$tmp_paths = explode("/", $tmp_uri[0]);
		$is_route = false;
		do {
			if (empty($tmp_paths)) {break;}
			$key_route = array_shift($tmp_paths);
			if (empty($key_route)) {break;}
			if ( !empty($routes[$key_route]) ) {
				$routes = $routes[$key_route];
			} else {
				array_unshift($tmp_paths, $key_route);
				break;
			}
			if(!empty($routes[$method])) {
				$controller_route = !empty($routes[$method]['controller']) ? $routes[$method]['controller'] : null;
				$action_route = !empty($routes[$method]['action']) ? $routes[$method]['action'] : null;
			} else {
				$controller_route = !empty($routes['controller']) ? $routes['controller'] : null;
				$action_route = !empty($routes['action']) ? $routes['action'] : null;
			}
			$is_route = !empty($controller_route) && !empty($action_route);
		} while (1);
		$path_parts = json_decode(json_encode($tmp_paths), true);
		if (!$is_route) {
			$tmp_paths = explode("/", $tmp_uri[0]);
			if(current($tmp_paths)){
				$con = ucwords(current($tmp_paths),"-");
				$controller_route = str_replace("-", "", $con);
				array_shift($tmp_paths);
			}
			if(current($tmp_paths)){
				$act = ucwords(current($tmp_paths),"-");
				$action_route = str_replace("-", "", $act);
				array_shift($tmp_paths);
			}
		}
		$pathController = realpath(APP_PATH.'controllers/'.$controller_route);
		if (empty($pathController)) {
			$controller_route = $this->getRoutes()["/"]['controller'];
		}
		$pathAction = realpath(APP_PATH.'controllers/'.$controller_route.'/'.$action_route.'.php');
		if (empty($pathAction)) {
			$action_route = $this->getRoutes()["/"]['action'];
		}
		/* load default controller */
		if(empty($controller_route)) {
			$controller_route = $this->getRoutes()["/"]['controller'];
		}
		/* load default action */
		if(empty($action_route)) {
			$action_route = $this->getRoutes()["/"]['action'];
		}
		// add surfix Controller
		$this->controller = $controller_route;

		// add prefix action
		$this->action = $action_route;
		$this->params = $path_parts;
		return $this;
	}
}
?>