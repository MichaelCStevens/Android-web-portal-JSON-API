<?php

class deviceController {

    public $page;

    function __construct() {

        require_once router::$fileRoot . "classes/models/device.model.php";
        $this->model = new deviceModel;
        $this->processInput();
    }

    public function processInput() {
        if (isset($_GET['push'])) {
            $this->model->pushSettings();
             router::redirectMessage('index.php?view=device', 'Push Successful');
        }
    }

}
