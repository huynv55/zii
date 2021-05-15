<?php

class Controller {
    public $request;
    public $response;
    public $layout;

    public function __construct() {
        $this->request = new Request();
        $this->response = new Response();
        $this->layout = null;
    }

    public function setLayout($layout) {
        $this->response->setLayout($layout);
    }

    public function beforeAction() {

    }

    public function afterAction() {
        return $this->response->send();
    }

    public function getRouterAction() {
        //$route = new Router();
        return $GLOBALS['ZiiApp']->getRouter()->getAction();
    }

    public function getRouterController() {
        //$route = new Router();
        return $GLOBALS['ZiiApp']->getRouter()->getController();
    }
}
?>