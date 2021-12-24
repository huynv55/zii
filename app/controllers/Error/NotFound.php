<?php
AppLoader::controller('ErrorController.php');

class ActionErrorNotFound extends ErrorController {

    public function response() {
        $this->response->view("errors/404");
    }
}
?>