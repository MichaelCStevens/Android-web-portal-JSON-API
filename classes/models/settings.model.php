<?php

class settingsModel {

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

    public function getBlackList($type) {
        $query = DB::prepare("SELECT value, note FROM blacklist WHERE `type`='$type'");
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $result = $query->fetchAll();
            }
        }
    }

    public function updateHomeCopy() {
        $content = filter_input(INPUT_POST, 'homecopy', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='homecopy'");
        $query->bindParam(':value', $content);
        $query->execute();
        return true;
    }

    public function getSettings($key) {
        $sql = "SELECT `value` FROM portal_settings WHERE `key`='$key'";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchObject();
            }
        }
    }

    public function getDeviceDefinitions() {
        $sql = "SELECT * FROM settings  ";
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

    public function updateDeviceSettingsDefs() {

        $settings = array();
        $sID = $_POST['setting_id'];
        $query = DB::prepare("DELETE FROM settings_reputation WHERE `setting_id`='$sID'");
        $query->execute();
        for ($i = 0; $i < count($_POST['title']); $i++) {
            $sTitle = $_POST['title'][$i];
            $sReputation = $_POST['reputation'][$i];
            $sValue = $_POST['value'][$i];
            $query = DB::prepare("INSERT INTO settings_reputation SET `value`= :val, `value_title`= :title, `setting_id`= :sid,  `reputation`= :rep ");
            $query->bindParam(':title', $sTitle);
            $query->bindParam(':sid', $sID);
            $query->bindParam(':rep', $sReputation);
            $query->bindParam(':val', $sValue);

            $query->execute();
        }
        return true;
    }

    public function getDeviceDefinitionsValues($id) {
        $sql = "SELECT * FROM settings_reputation WHERE setting_id='$id' ORDER BY value ASC";
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

    public function clearData() {

        if (isset($_POST['clearAPI'])) {
            if ($_POST['clearAPI'] == 'api') {
                $query = DB::prepare("DELETE FROM api_data_log");
                $query->execute();
            }
        }
        if (isset($_POST['clearAppDefs'])) {
            if ($_POST['clearAppDefs'] == 'clearAppDefs') {
                $query = DB::prepare("DELETE FROM apps");
                $query->execute();
            }
        }
        if (isset($_POST['clearFileDefs'])) {
            if ($_POST['clearFileDefs'] == 'clearFileDefs') {
                $query = DB::prepare("DELETE FROM files");
                $query->execute();
            }
        }
        if (isset($_POST['clearBlacklist'])) {
            if ($_POST['clearBlacklist'] == 'blacklist') {
                $query = DB::prepare("DELETE FROM blacklist_log");
                $query->execute();
            }
        }
        if (isset($_POST['clearUsers'])) {
            if ($_POST['clearUsers'] == 'users') {

                $query = DB::prepare("DELETE FROM users");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_apps");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_app_activities");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_app_permissions");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_app_receivers");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_app_reputation");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_devices");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_files");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_file_structure");
                $query->execute();
                $query = DB::prepare("DELETE FROM user_processes");
                $query->execute();
                $query = DB::prepare("DELETE FROM settings_pushed");
                $query->execute();
                $query = DB::prepare("DELETE FROM blacklist_log");
                $query->execute();
                $query = DB::prepare("DELETE FROM device_snapshots");
                $query->execute();
            }
        }
    }

    public function updateSettings() {


        $content = filter_input(INPUT_POST, 'date_range', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='date_range'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'date_range_start', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='date_range_start'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'date_range_end', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='date_range_end'");
        $query->bindParam(':value', $content);
        $query->execute();







        $content = filter_input(INPUT_POST, 'app_range', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='app_range'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'app_range_start', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='app_range_start'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'app_range_end', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='app_range_end'");
        $query->bindParam(':value', $content);
        $query->execute();






        $content = filter_input(INPUT_POST, 'sms_range', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='sms_range'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'sms_range_start', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='sms_range_start'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'sms_range_end', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='sms_range_end'");
        $query->bindParam(':value', $content);
        $query->execute();




        $content = filter_input(INPUT_POST, 'chatty_range_end', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='chatty_range_end'");
        $query->bindParam(':value', $content);
        $query->execute();


        $content = filter_input(INPUT_POST, 'chatty_range_start', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='chatty_range_start'");
        $query->bindParam(':value', $content);
        $query->execute();



        $content = filter_input(INPUT_POST, 'charts', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='charts'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = filter_input(INPUT_POST, 'theme', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='theme'");
        $query->bindParam(':value', $content);
        $query->execute();

        $content = $_POST['published_pages'];
        //add home and seetings to array, these pages are always available
        $content[] = 'index';
        $content[] = 'settings';
        $content = json_encode($content);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='published_pages'");
        $query->bindParam(':value', $content);
        $query->execute();

        $sql = "DELETE FROM blacklist ";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        //exit(0);

        foreach (router::$blacklistItems as $bi) {
            $post = $bi . '-blacklist';
            if (isset($_POST[$post])) {
                $content = $_POST[$post];
                foreach ($content as $c) {
                    $c = explode('::', $c);
                    $note = $c[1];
                    $c = $c[0];
                    $sql = "INSERT INTO blacklist SET type= '$bi', value= :value, note= :note";
                    //   echo $sql;
                    $query = DB::prepare($sql);
                    $query->bindParam(':value', $c);
                    $query->bindParam(':note', $note);
                    $query->execute();
                }
            }
        }
        //   exit(0);
        return true;
    }

}
