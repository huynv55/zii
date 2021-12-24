<?php
AppLoader::controller('ErrorController.php');

class ActionIndex extends ErrorController {

    public function response() {
        $this->response->view("errors/500");
    }
}
?>