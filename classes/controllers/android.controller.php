<?php

class androidController extends router {

    public $page;
 

   function __construct() {
  
        require_once router::$fileRoot."classes/models/android.model.php";
        $this->model = new androidModel;
        $this->processInput();
    }

    public function processInput() {
        if (isset($_GET['hc'])) {
 
        }
       
    }

}
