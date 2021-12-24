<?php 
require realpath(__DIR__.'/inc/config.php');
require realpath(__DIR__.'/inc/log.php');
require realpath(__DIR__.'/inc/NotORMModel.php');
require realpath(__DIR__.'/inc/DBModel.php');
require realpath(__DIR__.'/inc/MongoDBModel.php');
require realpath(__DIR__.'/inc/HttpClient.php');
require realpath(__DIR__.'/inc/router.php');
require realpath(__DIR__.'/inc/view.php');
require realpath(__DIR__.'/inc/request.php');
require realpath(__DIR__.'/inc/response.php');
require realpath(__DIR__.'/inc/controller.php');

require realpath(__DIR__.'/../app/kernel.php');

class ZiiAppFramework {
	protected $router;

	public function getRouter() {
		return $this->router;
	}

	public function __construct() {
		$this->router = new Router();
	}

	public function loadControllerAndAction($controller, $action) {
		$path = realpath(APP_PATH.'controllers/'.$controller.'/'.$action.'.php');
		require_once $path;
	}

	public function run() {
		if (!SessionService::init()) {
			throw new Exception("Can not init session!");
			exit();
		}
		//get controller class
		$controller = $this->router->getController();
		//get method
		$method = $this->router->getAction();
		$class = 'Action'.str_replace("/", "", $controller).$method;
		$this->loadControllerAndAction($controller, $method);
		if(method_exists($class, 'response')){
			$reflection = new ReflectionMethod($class, 'response');
			$controller_obj = new $class();
			$params = $this->router->getParams();
			$controller_obj->beforeAction();
			$pass = array();
			$args = $reflection->getParameters();
			$index = 0;
			foreach($args as $param) {
				if(!empty($params[$index])) {
					$pass[] = $params[$index];
				} else if($param->isOptional()) {
					$pass[] = $param->getDefaultValue();
				} else {
					$pass[] = '';
				}
				$index = $index + 1;
			}
			$reflection->invokeArgs($controller_obj, $pass);
			$controller_obj->afterAction();
		} else {
			throw new Exception("Called method $controller : $class does not exists!");
		}
	}
}
$ZiiApp = new ZiiAppFramework();
return $ZiiApp;
?>