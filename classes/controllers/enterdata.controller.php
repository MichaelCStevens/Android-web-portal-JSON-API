<?php

class enterdataController {

    public $page;
    public $model;

    function __construct() {

        require_once router::$fileRoot . "classes/models/enterdata.model.php";
        $this->model = new enterdataModel;
        $this->processInput();
    }

    public function processInput() {
        if (isset($_GET['un'])) {
            $this->model->updateNote();
            router::redirectMessage('index.php?view=enterdata', 'Note Updated');
        }
     if (isset($_GET['reset'])) {
            unset($_SESSION['csv']);
            router::redirectMessage('index.php?view=enterdata', 'Reset, no changes');
        }
        if (isset($_GET['ud'])) {
            $result = $this->model->updateData();
            if (isset($result['upload'])) {
                router::redirectMessage('index.php?view=enterdata', 'Preview the Data Below and the Submit');
                return true;
            }
            if ($result['success']) {
                router::redirectMessage('index.php?view=enterdata', 'Data Updated');
            } else {
                router::redirectMessage('index.php?view=enterdata', $result['error_message']);
            }
        }
    }

}
