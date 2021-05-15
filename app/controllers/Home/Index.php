<?php
AppLoader::controller('HomeController.php');

class ActionIndex extends HomeController {
    public function __construct() {
        parent::__construct();
    }

    public function response($page = 'home') {
        $this->response->view("pages/home.phtml", ["controller" => "Home", "action" => "index", 'page' => $page]);
    }
}
?>