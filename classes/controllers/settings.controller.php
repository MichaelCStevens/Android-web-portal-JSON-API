<?php

class settingsController {

    public $page;
    public $model;

    function __construct() {
        require_once router::$fileRoot . "classes/models/settings.model.php";

        $this->model = new settingsModel;
        $this->processInput();
    }

    public function processInput() {
        if (isset($_GET['hc'])) {
            $this->model->updateHomeCopy();
            router::redirectMessage('index.php?view=settings', 'Home Page Copy Updated');
        }
        if (isset($_GET['us'])) {
            $this->model->updateSettings();
            router::redirectMessage('index.php?view=settings', 'Settings Updated');
        }
        if (isset($_GET['cd'])) {
            $this->model->clearData();
            router::redirectMessage('index.php?view=settings', 'Data Cleared');
        }
        if (isset($_GET['edv'])) {
            $this->model->updateDeviceSettingsDefs();
            router::redirectMessage('index.php?view=settings', 'Device Settings Definitions Updated');
        }
    }

}
