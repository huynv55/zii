<?php
AppLoader::controller('ErrorController.php');

class ActionErrorIndex extends ErrorController {

    public function response() {
        $this->response->view("errors/500");
    }
}
?>