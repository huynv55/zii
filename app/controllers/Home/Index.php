<?php
AppLoader::controller('HomeController.php');

class ActionIndex extends HomeController {
    public function __construct() {
        parent::__construct();
    }

    public function response($slug = 'home', $page = 'about') {
        Log::debug("--------------------------------------");
        Log::debug("Load Home controller and action index");
        Log::debug("Slug : ".$slug);
        Log::debug("Page : ".$page);
        Log::debug("--------------------------------------");
        $this->response->view("pages/home", compact('page', 'slug'));
    }
}
?>