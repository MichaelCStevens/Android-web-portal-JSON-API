<?php

class androidModel {

    public function getDeviceDefinitions() {
        $sql = "SELECT * FROM settings WHERE status='1'  ";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            }
        }
    }

    public function getScripts() {
        $sql = "SELECT * FROM user_devices AS a "
                . "LEFT JOIN users AS b ON a.user_id=b.user_id "
                . "LEFT JOIN devices AS c ON a.device_id=c.id GROUP BY a.ud_id ";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            }
        }
    }

    public function getDevices() {
        $sql = "SELECT * FROM user_devices AS a "
                . "LEFT JOIN users AS b ON a.user_id=b.user_id "
                . "LEFT JOIN devices AS c ON a.device_id=c.id GROUP BY a.ud_id ";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            }
        }
    }

}

?>