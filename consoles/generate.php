<?php
require realpath(__DIR__.'/../bootstrap/inc/config.php');
class Generate {

    public static function process() {
        echo "\n";
        echo "Generate controller and action";
        echo "\n";
        $controller_class = self::getController();
        $action_class = self::getAction();

        $controller_path = self::getControllerPath($controller_class);
        $action_path = self::getActionPath($controller_class, $action_class);

        $controller_content = self::getContentController($controller_class);
        $action_content = self::getContentAction($controller_class, $action_class);

        if ( !is_file($controller_path) ) {
            if(file_put_contents($controller_path, $controller_content)) {
                echo "Create ".$controller_class." successfully";
            } else {
                echo "Create ".$controller_class." failed";
            }
        }

        if ( !is_file($action_path) ) {
            if(file_put_contents($action_path, $action_content)) {
                echo "\n ----------------------------------------- \n";
                echo "Create ".$action_class." successfully";
            } else {
                echo "-----------------------------------------\n";
                echo "Create ".$action_class." failed";
            }
        }

        self::initView($controller_class, $action_class);
    }

    public static function getController() {
        if ( empty($_SERVER['argv'][2]) ) {
        print("Enter controller: ");
            $controller = str_replace("-", "", ucwords(trim(fgets(STDIN)),"-"));
        } else {
            $controller = str_replace("-", "", ucwords($_SERVER['argv'][2],"-"));
        }
        $controller_class = $controller."Controller";
        return $controller_class;
    }

    public static function getAction() {
        if ( empty($_SERVER['argv'][3]) ) {
            print("Enter action: ");
            $action = str_replace("-", "", ucwords(trim(fgets(STDIN)),"-"));
        } else {
            $action = str_replace("-", "", ucwords($_SERVER['argv'][3],"-"));
        }
        $action_class = "Action".$action;
        return $action_class;
    }

    public static function getControllerPath($controller_class) {
        return str_replace("/", DIRECTORY_SEPARATOR, APP_PATH."controllers/".$controller_class.".php");
    }

    public static function getActionPath($controller_class, $action_class) {
        $controller = str_replace('Controller', '', $controller_class);
        $action = str_replace('Action', '', $action_class);
        $action_folder_path = str_replace("/", DIRECTORY_SEPARATOR, APP_PATH."controllers/".$controller);
        if ( !is_dir( $action_folder_path ) ) {
            if (!mkdir($action_folder_path, 0777, true)) {
                die('Failed to create folders...');
            }
        }
        $action_path = str_replace("/", DIRECTORY_SEPARATOR, $action_folder_path."/".$action.".php");
        return $action_path;
    }

    public static function initView($controller_class, $action_class) {
        $controller = str_replace('Controller', '', $controller_class);
        $action = str_replace('Action', '', $action_class);
        $view_path = str_replace("/", DIRECTORY_SEPARATOR, ROOT_PATH."resources/views/pages/home.phtml");
        if ( !is_file( $view_path ) ) {
            if ( !is_dir(dirname($view_path)) ) {
                if (!mkdir(dirname($view_path), 0777, true)) {
                    die('Failed to create views folders...');
                }
            }
            $view_content = '<h1>Controller : <?= $controller ?></h1><h2>Action : <?= $action ?></h2>';
            if(file_put_contents($view_path, $view_content)) {
                echo "\n ----------------------------------------- \n";
                echo "Create view pages/home.phtml successfully";
            } else {
                echo "-----------------------------------------\n";
                echo "Create view pages/home.phtml failed";
            }
        }
    }

    public static function getContentController($controller_class) {
        $controller_content = '<?php

class HomeController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function beforeAction() {
        parent::beforeAction();
        $this->setLayout(\'layouts/main.phtml\');
    }
}
?>';    
        $controller_content = str_replace("HomeController", $controller_class, $controller_content);
        return $controller_content;
    }

    public static function getContentAction($controller_class, $action_class) {
        $controller = str_replace('Controller', '', $controller_class);
        $action = str_replace('Action', '', $action_class);
        $action_content = '<?php
AppLoader::controller(\'HomeController.php\');

class ActionIndex extends HomeController {
    public function __construct() {
        parent::__construct();
    }

    public function response() {
        $this->response->view("pages/home.phtml", ["controller" => "Home", "action" => "Index"]);
    }
}
?>';
        $action_content = str_replace("HomeController", $controller_class, $action_content);
        $action_content = str_replace("ActionIndex", $action_class, $action_content);
        $action_content = str_replace("Index", $action, $action_content);
        $action_content = str_replace("Home", $controller, $action_content);
        return $action_content;
    }

}

// run generate
Generate::process();

die();
?>