<?php 
require realpath(__DIR__.'/inc/config.php');
require realpath(__DIR__.'/inc/log.php');
require realpath(__DIR__.'/inc/NotORMModel.php');
require realpath(__DIR__.'/inc/DBModel.php');
require realpath(__DIR__.'/inc/MongoDBModel.php');
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
		
		$class = 'Action'.$method;

		$this->loadControllerAndAction($controller, $method);
		

		if(method_exists($class, 'response')){
			$reflection = new ReflectionMethod($class, 'response');

			$controller_obj = new $class();

			$params = $this->router->getParams();
			
			$controller_obj->beforeAction();
			
			$reflection->invokeArgs($controller_obj, $params);

			$controller_obj->afterAction();
		} else {
			throw new Exception("Called method does not exists!");
		}
	}
}
$ZiiApp = new ZiiAppFramework();
return $ZiiApp;
?>