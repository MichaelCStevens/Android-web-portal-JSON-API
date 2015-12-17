<?php

class deviceModel {

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

    public function pushSettings() {
        $sql = "DELETE FROM settings_pushed WHERE `device_id` = :di AND `setting_id` = :settingsselect ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $_POST['device_id']);
        $query->bindParam(':settingsselect', $_POST['settings-select']);
        $query->execute();
        $sql = "INSERT INTO settings_pushed SET `device_id` = :di, `setting_id` = :settingsselect, `setting_value_id` = :settingsvalueselect";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $_POST['device_id']);
        $query->bindParam(':settingsselect', $_POST['settings-select']);
        $query->bindParam(':settingsvalueselect', $_POST['settings-value-select']);
        $query->execute();
        return true;
    }

    public function getSettings() {
        $sql = "SELECT * FROM settings  ";
        //  echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            }
        }
    }

    public function getSettingsPossibilities($setting) {
        $sql = "SELECT * FROM settings_reputation WHERE setting='$setting'  ";
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
