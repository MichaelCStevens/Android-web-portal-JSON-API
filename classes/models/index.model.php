<?php

class indexModel {

    public $id;

    function __construct() {
        $this->id = '0';
    }

    public function getHomeCopy() {
        $query = DB::prepare("SELECT value FROM portal_settings WHERE `key`='homecopy'");
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $result = $query->fetchAll();
            }
        }
    }

}

?>