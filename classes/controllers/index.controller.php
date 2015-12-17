<?php

class indexController {

    public $page;

    function __construct() {

        require_once router::$fileRoot."classes/models/index.model.php";
        $this->model = new indexModel;
        $this->processInput();
    }

    public function processInput() {
        
    }

}
