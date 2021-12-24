<?php
AppLoader::controller('HomeController.php');

class ActionHomeSlug extends HomeController {
    public function __construct() {
        parent::__construct();
    }

    public function response($page, $slug = 'about') {
        Log::debug("--------------------------------------");
        Log::debug("Load Home controller and action slug");
        Log::debug("Page : ".$page);
        Log::debug("Slug : ".$slug);
        Log::debug("--------------------------------------");
        $this->response->view("pages/home");
    }
}
?>