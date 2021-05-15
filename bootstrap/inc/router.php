<?php

class Router {

	protected $uri;

	protected $controller;

	protected $action;

	protected $params = [];

	public static $routers = [];

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

	public static function getRoutes() {
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

	public static function any($path, $controller_name, $action_name) {
		if (empty(self::$routers)) {
			self::$routers = new \Phroute\Phroute\RouteCollector();
		}
		$func = function() use ($controller_name, $action_name) {
			$arg_list = func_get_args();
			return [
				'controller' => $controller_name,
				'action' => $action_name,
				'params' => $arg_list
			];
		};
		self::$routers->any($path, $func);
	}

	public static function addRouter($path, $type, $controller_name, $action_name) {
		if (empty(self::$routers)) {
			self::$routers = new \Phroute\Phroute\RouteCollector();
		}
		$func = function() use ($controller_name, $action_name) {
			$arg_list = func_get_args();
			return [
				'controller' => $controller_name,
				'action' => $action_name,
				'params' => $arg_list
			];
		};
		if ( $type == 'GET' ) {
			self::$routers->get($path, $func);
		} else if( $type == 'POST') {
			self::$routers->post($path, $func);
		} else if( $type == 'DELETE') {
			self::$routers->delete($path, $func);
		} else if( $type == 'PUT') {
			self::$routers->delete($path, $func);
		} else if( $type == 'OPTIONS') {
			self::$routers->options($path, $func);
		}
	}

	public static function defaultMap($controller_name, $action_name) {
		if (empty(self::$routers)) {
			self::$routers = new \Phroute\Phroute\RouteCollector();
		}
		$func = function() use ($controller_name, $action_name) {
			$arg_list = func_get_args();
			return [
				'controller' => $controller_name,
				'action' => $action_name,
				'params' => $arg_list
			];
		};
		self::$routers->any('/', $func);
	}

	public function __construct() {
		$uri = $this->getRequestUri();
		$this->uri = trim($uri, "/");

		if (empty(self::$routers)) {
			self::$routers = new \Phroute\Phroute\RouteCollector();
		}
		$dispatcher = new \Phroute\Phroute\Dispatcher(self::$routers->getData());
		$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$this->controller = $response['controller'];
		$this->action = $response['action'];
		$this->params = !empty($response['params']) ? $response['params'] : [];
		return $this;
	}
}
?>