<?php

class HomeController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function beforeAction() {
        parent::beforeAction();
        //$this->setLayout('layouts/main.phtml');
    }
}
?>