<?php
AppLoader::controller('HomeController.php');

class ActionIndex extends HomeController {
    public function __construct() {
        parent::__construct();
    }

    public function response($page = 'home') {
        Log::debug("--------------------------------------");
        Log::debug("Load Home controller and action index");
        Log::debug("Page : ".$page);
        Log::debug("--------------------------------------");
        $this->response->view("pages/home.phtml");
    }
}
?>