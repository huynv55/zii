<?php

class ErrorController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function beforeAction() {
        parent::beforeAction();
        $this->setLayout('');
    }
}
?>