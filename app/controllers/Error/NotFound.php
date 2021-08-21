<?php
AppLoader::controller('ErrorController.php');

class ActionNotFound extends ErrorController {

    public function response() {
        $this->response->view("errors/404.phtml");
    }
}
?>