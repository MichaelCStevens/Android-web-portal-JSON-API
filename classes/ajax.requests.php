<?php

error_reporting(E_ALL);

class ajax {

    public $request;
    public $uid;
    public $lastInspected;
    public $returnData;
    public $data;
    public $report;
    public $fileRoot;
    public $uriRoot;
    public $last;

    function __construct() {
        session_start();
        $this->returnData = array();
        $this->returnData['valid'] = false;
        $this->fileRoot = 'C:/DWASFiles/Sites/androidtest101/VirtualDirectory0/site/wwwroot/';
        $this->uriRoot = 'http://kmdgideas.com/symantec-android/';
        if (isset($_GET['request'])) {
            // print_r(json_decode(stripslashes($_POST['data'])));
            $this->data = json_decode(stripslashes($_POST['data']), true);
            $this->request = filter_input(INPUT_GET, 'request', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->processRequest($this->request);
        }
        if (isset($_GET['portalRequest'])) {
            $this->request = filter_input(INPUT_GET, 'portalRequest', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->processRequest($this->request);
        }
        if (isset($_GET['reportRequest'])) {
            $this->data = json_decode(stripslashes($_POST['data']), true);
            $this->request = filter_input(INPUT_GET, 'reportRequest', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->processRequest($this->request);
        }
    }

    public function logData() {
        $sql = "INSERT INTO api_data_log SET `get`=:get, `post`=:post, `date`='" . date('Y-m-d H:i:s') . "', `files`= :files ";
        $query = DB::prepare($sql);
        $query->bindParam(':get', json_encode($_GET));
        $query->bindParam(':post', json_encode($_POST));
        $query->bindParam(':files', json_encode($_FILES));
        $query->execute();
    }

    public function processRequest($r) {
        switch ($r) {
            default:
                //do nothing
                break;
            case "signin":
                $this->signIn();
                break;
            case "register":
                $this->register();
                break;
            case "inspection":
                $this->inspection();
                break;
            case "send_processes":
                $this->sendProcesses();
                break;
            case "send_processes_snapshot":
                $this->send_processes_snapshot();
                break;
            case "check_package":
                $this->check_package();
                break;
            case "send_packages":
                $this->sendPackages();
                break;
            case "check_process":
                $this->check_process();
                break;
            case "getSettings":
                $this->getSettings();
                break;
            case "editSettingsDefinition":
                $this->editSettingsDefinition();
                break;
            case "getDeviceApps":
                $this->getDeviceApps();
                break;
            case"getDeviceFS":
                $this->getDeviceFS();
                break;
            case "report":
                $this->generateReport();
                break;
            case "send_files":
                $this->sendFiles();
                break;
            case "get_blacklist":
                $this->getBlackList();
                break;
            case "log_blacklist":
                $this->logBlackList();
                break;
            case"blacklist_last_update":
                $this->blacklist_last_update();
                break;
            case"loadReport":
                $this->loadReport();
                break;
            case "getSettingsMatches":
                $this->getSettingsMatches();
                break;
            case "getBLMatches":
                $this->getBLMatches();
                break;
            case "getAllPush":
                $this->getAllPush();
                break;
            case "removePush":
                $this->removePush();
                break;
            case "sort":
                $this->sort();
                break;
            case "check_settings_push":
                $this->check_settings_push();
                break;
            case "push_report":
                $this->push_report();
                break;
            case "loadSnapshot":
                $this->loadSnapshot();
                break;
            case "log_filesystem":
                $this->log_filesystem();
                break;
            case "send_tcpdump":
                $this->send_tcpdump();
                break;
        }
    }

    public function log_filesystem() {
        $this->data['device_id'] = $_GET['device_id'];
        $this->data['email'] = $_GET['email'];
        $this->data['snapshot_id'] = $_GET['snapshot_id'];
        $file = $_FILES['file'];
        //print_r($file);
        $uploaddir = $this->fileRoot . 'assets/filesystem/';
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);
        $path_parts = pathinfo($_FILES["file"]["name"]);
        $file_path = $path_parts['filename'] . '_' . $this->data['device_id'] . '_' . time() . '.' . $path_parts['extension'];
        $uploadfile = $uploaddir . basename($file_path);
        // echo '<pre>';
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            //   echo "File is valid, and was successfully uploaded.\n";
            $this->readZip($uploadfile, $file_path, '2');
        } else {
            $this->returnData['valid'] = false;
        }
        $this->returnData['valid'] = true;
    }

    public function send_tcpdump() {
        $this->data['device_id'] = $_GET['device_id'];
        $this->data['email'] = $_GET['email'];
        $this->data['snapshot_id'] = $_GET['snapshot_id'];
        $file = $_FILES['file'];
        //print_r($file);
        $uploaddir = $this->fileRoot . 'assets/ipcaptures/';
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);
        $path_parts = pathinfo($_FILES["file"]["name"]);
        $file_path = $path_parts['filename'] . '_' . $this->data['device_id'] . '_' . time() . '.zip';
        $uploadfile = $uploaddir . basename($file_path);
        // echo '<pre>';
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            //   echo "File is valid, and was successfully uploaded.\n";
            $this->readZip($uploadfile, $file_path, '1');
        } else {
            $this->returnData['valid'] = false;
        }
        $this->returnData['valid'] = true;
    }

    public function readZip($file, $file_path, $opt) {
        if ($opt == 1) {
            $path = 'ipcaptures';
            $path2 = 'capture';
        } else {
            $path = 'filesystem';
            $path2 = 'scanFS';
        }
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            $zip->extractTo($this->fileRoot . 'assets/' . $path . '/');
            $zip->close();
            // echo 'file extracted';
            $txtfile = $this->fileRoot . 'assets/' . $path . '/' . $path2 . '_' . $this->data['device_id'] . '_' . time() . '.txt';
            rename($this->fileRoot . 'assets/' . $path . '/' . $path2 . '.txt', $txtfile);
            //  echo"file renamed<br/>";
            if ($opt == 1) {
                $this->analyzeIPs($txtfile);
            } else {
                $this->analyzeFileStructure($txtfile);
            }
            // unlink($file);
        } else {
            //echo 'failed, code:' . $res;
            $this->returnData['valid'] = false;
        }
    }

    public function analyzeFileStructure($file) {
        $data = file_get_contents($file); //read the file
        $lines = explode("\n", $data); //create array separate by new line
        $data = null;
        $sql = "DELETE FROM user_file_structure  WHERE device_id= :id";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':id', $this->data['device_id']);
        $query->execute();
        foreach ($lines as $line) {
            if (!empty($line)) {
                $line = str_replace('/', '', $line);
                $sql = "INSERT INTO user_file_structure SET device_id= :id, value=:value";
                //echo$sql;
                $query = DB::prepare($sql);
                $query->bindParam(':id', $this->data['device_id']);
                $query->bindParam(':value', $line);
                $query->execute();
            }
        }
          unlink($file);
        $this->returnData['valid'] = true;
    }

    public function analyzeIPs($file) {
        $data = file_get_contents($file); //read the file
        $lines = explode(" IP ", $data); //create array separate by new line
        $data = null;
        $blacklist = $this->getBlackList('ip');
        $ports = $this->getBlackList('port');
        $domains = $this->getBlackList('url');
        $c = 0;
        $matchesIP = array();
        $matchesPorts = array();
        $matchesURL = array();
        $i = 0;
        //rekey array
        $i = 1;
        foreach ($lines as $line) {
            $line = preg_split("/\\r\\n|\\r|\\n/", $line);
            $c = 0;
            foreach ($line as $l) {
                if (substr($l, 0, 4) === "Host") {
                    $host = $line[$c];
                    $host = explode(' ', $host);
                    $host = $host[1];
                }
                if (substr($l, 0, 8) === "Referer:") {
                    $ref = $line[$c];
                    $ref = explode(' ', $ref);
                    $ref = $ref[1];
                }
                $c++;
            }
            $line = array_values($line);
            if (count($line) >= 0) {
                //print_r($line);
                $ip = $line[0];
                $ip2 = explode(' ', $ip);
                $ipIN = $ip2[0];
                $ipOUT = $ip2[2];
                $port = explode('.', $ipOUT);
                $port = $port[4];
                $port = explode(':', $port);
                $port = trim($port[0]);
                //echo "ip is $ipOUT host is $host ref is $ref port is $port!";
                $search = in_array($port, $ports);
                // echo"is $port in ";print_r($ports);
                if ($search == true) {
                    $matchesPorts[] = array('ip' => $ipOUT, 'host' => $host, 'ref' => $ref, 'port' => $port);
                    //   echo"match found";
                }
                $search = in_array($host, $blacklist);
                // print_r($blacklist);
                //echo"looking for " . $ip . " in $blacklist";
                if ($search == true) {
                    $matchesIP[] = array('ip' => $ipOUT,'ipIN' => $ipIN, 'host' => $host, 'ref' => $ref, 'port' => $port);
                }
                $search = in_array($host, $domains);
                $search2 = in_array($ref, $domains);
                //echo"looking for " . $ip . " in $blacklist";
                if ($search == true || $search2 == true) {
                    $matchesURL[] = array('ipOUT' => $ipOUT, 'ipIN' => $ipIN, 'host' => $host, 'ref' => $ref, 'port' => $port);
                    //   echo"match found";
                }
                if (is_array($matchesIP)) {
                    foreach ($matchesIP as $line) {
                        print_r($line);
                     //   echo"match found logging blacklist";
                        $this->data['log_type'] = 'ip';
                        $this->data['log_value'] = $line['host'];
                        $this->data['value_source'] = $line['ipIN'];
                        $this->logBlackList();
                    }
                }
                if (is_array($matchesPorts)) {
                    foreach ($matchesPorts as $line) {
            
                        $this->data['log_type'] = 'port';
                        $this->data['log_value'] = $line['port'];
                        $this->data['value_source'] = 'IP:' . $line['ipIN'] . ' HOST:' . $line['host']. ' Ref:' . $line['ref'];
                        $this->logBlackList();
                    }
                    //unlink($file);
                }
                if (is_array($matchesURL)) {
                    foreach ($matchesURL as $line) {

                        $this->data['log_type'] = 'url';
                        $this->data['log_value'] = $line['host'];
                        $this->data['value_source'] = 'IP:' . $line['ipOUT'] . ' Ref:' . $line['ref'];
                        ;
                        $this->logBlackList();
                    }
                }
                $lines[$i] = null;
            }
        }
       unlink($file);
    }

    public function loadSnapshot() {
        $sid = filter_input(INPUT_GET, 'sid', FILTER_SANITIZE_SPECIAL_CHARS);
        $did = filter_input(INPUT_GET, 'did', FILTER_SANITIZE_SPECIAL_CHARS);
        $sql = "SELECT * FROM device_snapshots WHERE snapshot_id = :sid AND device_id = :did GROUP BY snapshot_id";
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':sid', $sid);
        $query->bindParam(':did', $did);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function push_report() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $deviceInfo = $this->getUsersDevice($deviceID, $email);
        ob_start();
        require_once $this->fileRoot . "classes/views/report.push.view.php";
        $this->report = ob_get_contents();
        ob_end_clean();

        echo $this->report;

        $this->returnData = 'no';
    }

    public function check_settings_push() {
        $device = $this->getUsersDevice($this->data['device_id'], $this->data['email']);
        $this->data['device_id'] = $device[0]['ud_id'];
        $i = 0;
        $c = 0;
        foreach ($this->getDeviceSettings() as $setting) {
            $push = $this->getPushSettings($setting['id']);
            $varSet = $setting['setting'];
            $curDeviceSetting = $device[0][$varSet];
            if (isset($push[0]['setting_value_id'])) {
                $i++;
                //this setting has a push, now check to see if push has been updated
                if ($push[0]['setting_value_id'] == $curDeviceSetting) {
                    //setting is already set remove push from db
                    $c++;
                    $this->data['delete_id'] = $push[0]['id'];
                    $this->removePush();
                } else {
                    //user has yet to change, keep push
                }
            }
        }

        if ($this->checkForPushSettings() == true) {
            $this->returnData['valid'] = true;
        } else {
            $this->returnData['valid'] = false;
        }
        //$this->returnData['valid'] = true;
    }

    public function sort() {
        require_once($this->fileRoot . '/classes/device.sort.php');
        $sorter = new sortDevices;
        $this->returnData['returnList'] = $sorter->differences;
        $this->returnData['valid'] = true;
    }

    public function removePush() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!isset($id)) {
            $id = $this->data['delete_id'];
        }
        $sql = "DELETE FROM settings_pushed   WHERE id= :id";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        return true;
    }

    public function getAllPush() {
        $deviceID = filter_input(INPUT_GET, 'did', FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT a.*, a.id as spid, c.*, d.* FROM settings_pushed as a "
                . "LEFT JOIN settings as c on a.setting_id=c.id "
                . "LEFT JOIN settings_reputation as d on a.setting_value_id = d.setting_id "
                . "WHERE a.device_id= :di GROUP BY  a.id ";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function getBLMatches() {
        $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
        $where = '';
        $settingVal = filter_input(INPUT_GET, 'settingVal', FILTER_SANITIZE_SPECIAL_CHARS);
        $did = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_SPECIAL_CHARS);
        //echo "did $did";
        if (!empty($did)) {
            $where = ' AND b.device_identifier = "' . $did . '" ';
        }

        $sql = "SELECT * FROM blacklist_log as a "
                . "LEFT JOIN user_devices as b on a.device_id=b.device_identifier "
                . "LEFT JOIN users as c on b.user_id=c.user_id "
                . " WHERE a.type='$type' AND a.value_source LIKE  '%$settingVal%' $where OR "
                . "a.type='$type' AND a.value LIKE  '%$settingVal%' $where GROUP BY a.value, b.device_id ORDER BY datetimestamp_first DESC";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function getSettingsMatches() {
        $where = '';
        $setting = filter_input(INPUT_GET, 'setting', FILTER_SANITIZE_SPECIAL_CHARS);
        $did = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!empty($did)) {
            $where = ' AND a.device_identifier = "' . $did . '" ';
        }
        $settingVal = filter_input(INPUT_GET, 'settingVal', FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT * FROM user_devices as a "
                . "LEFT JOIN users as b on a.user_id=b.user_id "
                . " WHERE a.$setting ='$settingVal'  $where  GROUP BY a.imei";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function loadReport() {

        $report = filter_input(INPUT_GET, 'report', FILTER_SANITIZE_SPECIAL_CHARS);
        $device = filter_input(INPUT_GET, 'device', FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_GET, 'value', FILTER_SANITIZE_SPECIAL_CHARS);

        switch ($report) {
            default:
                //do nothing
                break;
            case "blacklist":

                $this->getBlackListReport($device, $value);
                break;
            case "setting":
                $this->getSettingsReport($device, $value);
                break;
        }
    }

    public function getSettingsReport($device, $value) {

        if (!empty($device) && $device != '0') {
//                   $sql = "SELECT *, b.value AS deviceValue FROM settings as a "
//                . "LEFT JOIN settings_reputation as b on a.id =b.setting_id "
//                . "LEFT JOIN user_devices as c on a.setting=c.$value  "
//                . "WHERE 1=1 AND a.setting='$value' AND c.device_identifier= '$device' GROUP BY c.device_identifier ";
        }
        $sql = "SELECT *, b.value AS deviceValue FROM settings as a "
                . "LEFT JOIN settings_reputation as b on a.id =b.setting_id "
                . "WHERE 1=1 AND a.setting='$value'   ";

        //  echo $sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function getBlackListReport($device, $value) {
        $sql = "SELECT value, note FROM blacklist as a "
                . "WHERE 1=1 AND a.type='$value' ";
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        $this->returnData = $rows;
    }

    public function logBlackList() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $logType = $this->data['log_type'];
        $logValue = $this->data['log_value'];
        $source = $this->data['value_source'];
        $sid = 0;
        echo "logging black list, type:$logType, value: $logValue ";
        if (isset($this->data['snapshot_id'])) {
            $sid = $this->data['snapshot_id'];
        } else {
            $sid = 0;
        }
        if ($logType == 'sms' || $logType == 'phone') {
            $logValue = explode(':', $logValue);
            $source = $logValue[1];
            $logValue = $logValue[0];
        }
        if (isset($this->data['direction'])) {
            $dir = $this->data['direction'];
            if ($dir == 'outgoing') {
                $dir = 1;
            } else {
                $dir = 0;
            }
        } else {
            $dir = -1;
        }
        if ($source == '') {
            $source = 'N/A';
        }
        $sql = "SELECT * FROM blacklist_log WHERE device_id= :di AND `type`= :type  AND `direction`= :dir AND `value`= :value AND `value_source`= :value_source ";
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->bindParam(':type', $logType);
        $query->bindParam(':value', $logValue);
        $query->bindParam(':value_source', $source);
        $query->bindParam(':dir', $dir);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $id = $query->fetchObject()->id;
                $sql = "UPDATE blacklist_log SET  `datetimestamp_last`= '" . date('Y-m-d H:i:s') . "', snapshot_id=:sid WHERE `id`= :id";
                $query = DB::prepare($sql);
                $query->bindParam(':sid', $sid);
                $query->bindParam(':id', $id);
                $query->execute();
            } else {
                $sql = "INSERT INTO blacklist_log SET device_id= :di, `type`= :type, direction= :dir, `value`= :value, "
                        . "`value_source`= :value_source, `datetimestamp_first`= '" . date('Y-m-d H:i:s') . "', snapshot_id=:sid ";
                $query = DB::prepare($sql);
                //echo $sql;
                $query->bindParam(':di', $deviceID);
                $query->bindParam(':type', $logType);
                $query->bindParam(':value', $logValue);
                $query->bindParam(':value_source', $source);
                $query->bindParam(':sid', $sid);
                $query->bindParam(':dir', $dir);
                $query->execute();
            }
        }
        $this->returnData['valid'] = true;
    }

    public function recordSnapshot($snapshotData, $type, $ssid, $deviceID) {
        $snapshotData = json_encode($snapshotData);
        $sql = "INSERT INTO device_snapshots SET device_id= :di, snapshot_id= :ss, data= :data, datetime_stamp='" . date('Y-m-d H:i:s') . "', type= :type ";
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->bindParam(':ss', $ssid);
        $query->bindParam(':data', $snapshotData);
        $query->bindParam(':type', $type);
        $query->execute();
        $_SESSION[$type] = DB::lastInsertId('id');
    }

    public function blacklist_last_update() {
        $array['last_update'] = 100;
        $this->returnData['data'] = $array;
        $this->returnData['valid'] = true;
    }

    public function getBlackList($type = null) {
//        if(isset($_POST['ip'])){
//            $type=
//        }
        $sql = "SELECT * FROM blacklist WHERE type='ip' OR type='url' OR type='sms' OR type='phone' OR type='block'";
        if ($type != null) {
            $sql = "SELECT * FROM blacklist WHERE type='$type'";
        }
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        $items = array();
        $i = 0;
        foreach ($rows as $row) {
            if ($type != null) {
                $items[] = $row['value'];
            } else {
                $items[$i]['type'] = $row['type'];
                $items[$i]['match'] = $row['value'];
            }
            $i++;
        }
        if ($type != null) {
            return $items;
        } else {
            $this->returnData['data'] = $items;
            $this->returnData['valid'] = true;
        }
    }

    public function editSettingsDefinition() {
        $values = '';
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $sql = "SELECT title FROM settings WHERE id= :id ";
        $query = DB::prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        $title = $query->fetchObject();
        $sql = "SELECT * FROM settings_reputation WHERE setting_id= :id";
        $query = DB::prepare($sql);
        $query->bindParam(':id', $id);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                foreach ($query->fetchAll() as $row) {
                    $values[] = array('value_id' => $row['id'], 'title' => $row['value_title'], 'value' => $row['value'], 'reputation' => $row['reputation']);
                }
            }
        }
        $this->returnData = array('title' => $title->title, $values);
    }

    public function checkAppPermBlackList($permission, $appname, $appmd5) {
        $sql = "SELECT * FROM blacklist WHERE type='permissions' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $permission);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->data['log_type'] = 'permissions';
                $this->data['log_value'] = $permission;
                $this->data['value_source'] = $appname . '::' . $appmd5;
                $this->logBlackList();
            }
        }
    }

    public function checkAppActivityBlackList($activity, $appname, $appmd5) {
        $sql = "SELECT * FROM blacklist WHERE type='activities' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $activity);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->data['log_type'] = 'activities';
                $this->data['log_value'] = $activity;
                $this->data['value_source'] = $appname . '::' . $appmd5;
                $this->logBlackList();
            }
        }
    }

    public function checkAppReceiverBlackList($receiver, $appname, $appmd5) {
        $sql = "SELECT * FROM blacklist WHERE type='receivers' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $receiver);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->data['log_type'] = 'receivers';
                $this->data['log_value'] = $receiver;
                $this->data['value_source'] = $appname . '::' . $appmd5;
                $this->logBlackList();
            }
        }
    }

    public function checkAppBlackList($fileName, $fileAppName, $version, $md5) {
        $sql = "SELECT * FROM blacklist WHERE type='app' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $fileName);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                //  echo "rowsfound";
                $this->data['log_type'] = 'app';
                $this->data['log_value'] = $fileName;
                $this->data['value_source'] = "$fileAppName::$md5";
                $this->logBlackList();
            }
        }
        $sql = "SELECT * FROM blacklist WHERE type='app' AND value = :value2 LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value2', $md5);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                //  echo "rowsfound";
                $this->data['log_type'] = 'app';
                $this->data['log_value'] = $md5;
                $this->data['value_source'] = "$fileAppName::$md5";
                $this->logBlackList();
            }
        }
    }

    public function sendPackages() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $package_list = $this->data['package_list'];
        $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);
        $snapshotData = $package_list;
        foreach ($package_list as $package) {
            $fileName = $package['package_name'];
            $fileAppName = $package['package_app_name'];
            if (isset($package['package_versionName'])) {
                $version = $package['package_versionName'];
            } else {
                $version = "N/A";
            }

            $md5 = $package['package_sha256'];
            $permissions = $package['package_permissions'];
            $lastupdate = $package['package_updated_timestamp'];
            $activities = $package['package_activities'];
            $receivers = $package['package_receivers'];
            //check if app is defined in database, if not create
            $packageID = $this->packageExist($fileName, $fileAppName, $version, $md5);
            if (intval($packageID) > 0) {
                //check if app is 'owned' by user, add to ownership if not and return user app id
                $this->checkPermissions($fileName, $fileAppName, $version, $md5, $permissions, $packageID);
                $userPackageID = $this->userOwnPackage($packageID, $deviceIdentifier, $fileName, $fileAppName, $md5, $version, $lastupdate);
                if (intval($userPackageID) > 0) {
                    $this->checkActivities($activities, $userPackageID, $fileAppName, $md5);
                    $this->checkReceivers($receivers, $userPackageID, $fileAppName, $md5);
                    $this->returnData['valid'] = true;
                }
            }
        }
    }

    public function checkReceivers($receivers, $userPackageID, $appname, $appmd5) {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        foreach ($receivers as $receiver) {
            $this->checkAppReceiverBlackList($receiver, $appname, $appmd5);
            // echo $permission;
            $sql = "SELECT id FROM user_app_receivers WHERE receiver= :act AND user_app_id= :uap_id AND email= :email AND device_id= :di ";
            $query = DB::prepare($sql);
            $query->bindParam(':act', $receiver);
            $query->bindParam(':uap_id', $userPackageID);
            $query->bindParam(':email', $email);
            $query->bindParam(':di', $deviceID);
            $query->execute();
            $rows = $query->rowCount();
            if (isset($rows)) {
                if ($rows > 0) {
                    $permid = $query->fetchObject()->id;
                } else {
                    $sql = "INSERT INTO user_app_receivers SET receiver= :act, user_app_id= :uap_id, email= :email, device_id= :di  ";
                    $query = DB::prepare($sql);
                    $query->bindParam(':act', $receiver);
                    $query->bindParam(':uap_id', $userPackageID);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':di', $deviceID);
                    $query->execute();
                    $permid = DB::lastInsertId('id');
                }
            }
        }
    }

    public function checkActivities($activities, $fileID, $appname, $appmd5) {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $device = $this->getUsersDevice($deviceID, $email);
        foreach ($activities as $activity) {
            $this->checkAppActivityBlackList($activity, $appname, $appmd5);
            // echo $permission;
            $sql = "SELECT id FROM user_app_activities WHERE activity= :act AND user_app_id= :uap_id AND email= :email AND device_id= :di ";
            $query = DB::prepare($sql);
            $query->bindParam(':act', $activity);
            $query->bindParam(':uap_id', $fileID);
            $query->bindParam(':email', $email);
            $query->bindParam(':di', $device[0]['ud_id']);
            $query->execute();
            $rows = $query->rowCount();
            if (isset($rows)) {
                if ($rows > 0) {
                    $permid = $query->fetchObject()->id;
                } else {
                    $sql = "INSERT INTO user_app_activities SET activity= :act, user_app_id= :uap_id, email= :email, device_id= :di  ";
                    $query = DB::prepare($sql);
                    $query->bindParam(':act', $activity);
                    $query->bindParam(':uap_id', $fileID);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':di', $device[0]['ud_id']);
                    $query->execute();
                    $permid = DB::lastInsertId('id');
                }
            }
        }
    }

    public function checkPermissions($fileName, $fileAppName, $version, $md5, $permissions, $fileID) {
        foreach ($permissions as $permission) {
            $this->checkAppPermBlackList($permission, $fileName, $md5);
            $sql = "SELECT id FROM permissions WHERE permission= :perm LIMIT 0, 1";
            $query = DB::prepare($sql);
            $query->bindParam(':perm', $permission);
            $query->execute();
            $rows = $query->rowCount();
            if (isset($rows)) {
                if ($rows > 0) {
                    $permid = $query->fetchObject()->id;
                } else {
                    $sql = "INSERT INTO permissions SET permission= :perm ";
                    $query = DB::prepare($sql);
                    $query->bindParam(':perm', $permission);
                    $query->execute();
                    $permid = DB::lastInsertId('id');
                }
                $this->checkUserAppPermission($permid, $this->data['device_id'], $fileID);
            }
        }
    }

    public function checkUserAppPermission($permid, $device_id, $fileID) {
        $device_id = $this->data['device_id'];
        $email = $this->data['email'];
        $device = $this->getUsersDevice($device_id, $email);
        $sql = "SELECT uap_id FROM user_app_permissions WHERE perm_id= :perm AND device_id= :di AND app_id= :appid AND user_id=:ui";
        $query = DB::prepare($sql);
        $query->bindParam(':perm', $permid);
        $query->bindParam(':appid', $fileID);
        $query->bindParam(':di', $device[0]['device_id']);
        $query->bindParam(':ui', $device[0]['user_id']);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $permid = $query->fetchObject()->uap_id;
            } else {
                $sql = "INSERT INTO user_app_permissions SET perm_id= :perm, device_id= :di, user_id=:ui, app_id=:appid";
                $query = DB::prepare($sql);
                $query->bindParam(':perm', $permid);
                $query->bindParam(':di', $device[0]['ud_id']);
                $query->bindParam(':ui', $device[0]['user_id']);
                $query->bindParam(':appid', $fileID);
                $query->execute();
                return $permid = DB::lastInsertId('perm_id');
            }
        }
    }

    public function sendProcesses() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $process_list = $this->data['process_list'];
        $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);
        $this->clearData();
        foreach ($process_list as $package) {
            $processName = $package['process_name'];
            $this->checkProcessBlackList($processName);
            //check if app is defined in database, if not create
            $processID = $this->processExist($processName);
            if (intval($processID) > 0) {
                //check if app is 'owned' by user, add to ownership if not and return user app id
                $userProcessID = $this->userOwnProcess($processID, $deviceIdentifier, $processName);
                if (intval($userProcessID) > 0) {
                    $this->data['snapshotData'] = $this->returnData['valid'] = true;
                }
            }
        }
    }

    public function send_processes_snapshot() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $process_list = $this->data['process_list'];
        $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);
        $ssid = $this->data['snapshot_id'];
        $snapshotData = $this->data;
        $this->recordSnapshot($snapshotData, 'process', $ssid, $deviceID);
    }

    public function clearData() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);

        $query = DB::prepare("DELETE FROM user_apps WHERE device_id= :di");
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();

        $query = DB::prepare("DELETE FROM user_files WHERE device_id= :di");
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();

        $query = DB::prepare("DELETE FROM user_app_activities WHERE device_id= :di");
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();

        $query = DB::prepare("DELETE FROM user_app_permissions WHERE device_id= :di");
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();

        $query = DB::prepare("DELETE FROM user_app_receivers WHERE device_id= :di");
        $query->bindParam(':di', $deviceID);
        $query->execute();

        $query = DB::prepare("DELETE FROM user_processes WHERE device_id= :di");
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();

        $query = DB::prepare("DELETE FROM blacklist_log WHERE device_id= :di AND type != 'sms' AND type != 'process'");
        $query->bindParam(':di', $deviceID);
        $query->execute();
    }

    public function checkProcessBlackList($processName) {
        $sql = "SELECT * FROM blacklist WHERE type='process' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $processName);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->data['log_type'] = 'process';
                $this->data['log_value'] = $processName;
                $this->data['value_source'] = "$processName";
                $this->logBlackList();
            }
        }
    }

    public function processExist($processName) {
        $query = DB::prepare("SELECT * FROM processes WHERE process_name = :pn");
        $query->bindParam(':pn', $processName);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $fID = $result['id'];
                return $fID;
            } else {
                $sql = "INSERT INTO processes SET process_name = :pn";
                //echo $fileName;
                $query = DB::prepare($sql);
                $query->bindParam(':pn', $processName);
                // $query->bindParam(':md5', $filemd5);
                $query->execute();
                return DB::lastInsertId('id');
            }
        }
        return false;
    }

    public function userOwnProcess($processID, $deviceIdentifier, $processName) {
        //TODO add version in check
        $query = DB::prepare("SELECT * FROM user_processes WHERE process_id = :pid AND device_id= :di ");
        $query->bindParam(':pid', $processID);
        $query->bindParam(':di', $deviceIdentifier);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $appID = $result['process_id'];
                return $appID;
            } else {
                $query = DB::prepare("INSERT INTO user_processes SET process_id = :pid, device_id= :di ");
                $query->bindParam(':pid', $processID);
                $query->bindParam(':di', $deviceIdentifier);
                $query->execute();
                return DB::lastInsertId('process_id');
            }
        }
        return false;
    }

    public function sendFiles() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $files_list = $this->data['files_list'];
        // $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);
        $device = $this->getUsersDevice($deviceID, $email);
        $deviceIdentifier = $device[0]['device_identifier'];
        $userid = $device[0]['user_id'];
        // print_r($this->data);
        foreach ($files_list as $file) {
            $fileName = $file['file_name'];
            $filemd5 = $file['file_sha256'];
            $size = $file['file_size'];
            // echo $fileName;
            //check if app is defined in database, if not create
            $fileID = $this->fileExist($fileName, $filemd5, $size);
            if (intval($fileID) > 0 && $deviceIdentifier != '') {
                //check if app is 'owned' by user, add to ownership if not and return user app id
                $userFileID = $this->userOwnFile($fileID, $deviceIdentifier, $fileName, $filemd5, $size, $userid);
                $this->checkFileBlackList($fileName, $filemd5);
                if (intval($userFileID) > 0) {
                    $this->returnData['valid'] = true;
                }
            }
        }
    }

    public function userOwnFile($fileID, $deviceIdentifier, $fileName, $filemd5, $userid) {
        $data = "$fileName::$filemd5";
        $query = DB::prepare("INSERT INTO user_files SET file_id = :fid, device_id= :di, `data`= :data, `user_id`= :uid ");
        $query->bindParam(':fid', $fileID);
        $query->bindParam(':di', $deviceIdentifier);
        $query->bindParam(':data', $data);
        $query->bindParam(':uid', $userid);
        $query->execute();
        return DB::lastInsertId('uf_id');
    }

    public function checkFileBlackList($fileName, $md5) {
        $sql = "SELECT * FROM blacklist WHERE type='file' AND value = :value LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value', $fileName);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                //  echo "rowsfound";
                $this->data['log_type'] = 'file';
                $this->data['log_value'] = $fileName;
                $this->data['value_source'] = "$fileAppName::$md5";
                $this->logBlackList();
            }
        }

        $sql = "SELECT * FROM blacklist WHERE type='file' AND value = :value2 LIMIT 0,1";
        $query = DB::prepare($sql);
        $query->bindParam(':value2', $md5);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                //  echo "rowsfound";
                $this->data['log_type'] = 'file';
                $this->data['log_value'] = $md5;
                $this->data['value_source'] = "$fileAppName::$md5";
                $this->logBlackList();
            }
        }
    }

    public function fileExist($fileName, $filemd5, $size) {
        $query = DB::prepare("SELECT * FROM files WHERE filename = :fn AND md5= :md5");
        $query->bindParam(':fn', $fileName);
        $query->bindParam(':md5', $filemd5);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $fID = $result['f_id'];
                return $fID;
            } else {
                $sql = "INSERT INTO files SET filename = :fn,  md5= :md5, file_size = :fs";
                echo $fileName;
                $query = DB::prepare($sql);
                $query->bindParam(':fn', $fileName);
                $query->bindParam(':md5', $filemd5);
                $query->bindParam(':fs', $size);
                $query->execute();
                return DB::lastInsertId('f_id');
            }
        }
        return false;
    }

    public function updateScore($score) {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $deviceInfo = $this->getUsersDevice($deviceID, $email);
        $query = DB::prepare("UPDATE user_devices SET last_score = :score WHERE device_id= :di ");
        $query->bindParam(':score', $score);
        $query->bindParam(':di', $deviceInfo[0]['device_id']);
        $query->execute();
        return true;
    }

    public function computeAvgScore() {
        $sql = "SELECT last_score FROM user_devices";
        //echo$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $results = $query->fetchAll();
                $lastScore = 0;
                $c = count($results);
                foreach ($results as $result) {
                    $lastScore = $lastScore + $result['last_score'];
                }
                return $avgScore = number_format($lastScore / $c);
            }
        }
    }

    public function getBadUserAppPerms() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $sql = "SELECT a.*, f.* FROM blacklist as a "
                . "LEFT JOIN permissions as b on a.value = b.permission "
                . "LEFT JOIN user_app_permissions as c on b.id = c.perm_id "
                . "LEFT JOIN user_apps as e on c.app_id= e.app_id  "
                . "LEFT JOIN apps as f on c.app_id= f.app_id  "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='permissions' GROUP BY c.app_id ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            } else {
                return"None found";
            }
        }
    }

    public function getBadUserAppActs() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $sql = "SELECT a.*, f.* FROM blacklist as a "
                . "LEFT JOIN user_app_activities as b on a.value = b.activity "
                . "LEFT JOIN user_apps as e on b.user_app_id= e.id  "
                . "LEFT JOIN apps as f on e.app_id= f.app_id  "
                . "LEFT JOIN user_devices as d on e.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='activities'  GROUP BY e.app_id  ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            } else {
                return"None found";
            }
        }
    }

    public function getBadUserAppRecs() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $sql = "SELECT a.*, f.* FROM blacklist as a "
                . "LEFT JOIN user_app_receivers as b on a.value = b.receiver "
                . "LEFT JOIN user_apps as e on b.user_app_id= e.id  "
                . "LEFT JOIN apps as f on e.app_id= f.app_id  "
                . "LEFT JOIN user_devices as d on e.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='receivers'  GROUP BY e.app_id  ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            } else {
                return"None found";
            }
        }
    }

    public function getBadUserApps() {
        $query1 = 'None found';
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        //search by app name
        $sql = "SELECT * FROM blacklist as a "
                . "LEFT JOIN apps as b on a.value = b.android_name "
                . "LEFT JOIN user_apps as c on b.app_id = c.app_id "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='app'  GROUP BY c.app_id ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                unset($query1);
                $query1 = $query->fetchAll();
            }
        }
        //search by app md5
        $sql = "SELECT * FROM blacklist as a "
                . "LEFT JOIN apps as b on a.value = b.md5 "
                . "LEFT JOIN user_apps as c on b.app_id = c.app_id "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='app'  GROUP BY c.app_id  ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $query2 = $query->fetchAll();
            } else {
                return $query1;
            }
        } else {
            return $query1;
        }
        return array_merge($query1, $query2);
    }

    public function getBadUserFiles() {
        $query1 = 'None found';
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        //search by file name
        $sql = "SELECT * FROM blacklist as a "
                . "LEFT JOIN files as b on a.value = b.filename "
                . "LEFT JOIN user_files as c on b.f_id = c.file_id "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='file'  GROUP BY c.file_id ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                unset($query1);
                $query1 = $query->fetchAll();
            }
        }
        //search by file md5
        $sql = "SELECT * FROM blacklist as a "
                . "LEFT JOIN files as b on a.value = b.md5 "
                . "LEFT JOIN user_files as c on b.f_id = c.file_id "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='file'  GROUP BY c.file_id ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $query2 = $query->fetchAll();
            } else {
                return $query1;
            }
        } else {
            return $query1;
        }
        return array_merge($query1, $query2);
    }

    public function getBadUserProcesses() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $sql = "SELECT * FROM blacklist as a "
                . "LEFT JOIN processes as b on a.value = b.process_name "
                . "LEFT JOIN user_processes as c on b.id = c.process_id "
                . "LEFT JOIN user_devices as d on c.device_id = d.ud_id "
                . "WHERE d.device_identifier = '$deviceID' AND a.type='process'  GROUP BY c.process_id ";
        // echo$sql;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchAll();
            } else {
                return"None found";
            }
        }
    }

    public function generateReport() {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $deviceInfo = $this->getUsersDevice($deviceID, $email);
        ob_start();
        require_once $this->fileRoot . "classes/views/report.view.php";
        $this->report = ob_get_contents();
        ob_end_clean();
        ob_start();
        require_once $this->fileRoot . "classes/views/report.email.view.php";
        $this->emailReport = ob_get_contents();
        ob_end_clean();
        echo $this->report;
        if (!isset($this->data['portal'])) {
            $this->emailReport($this->emailReport, $email);
        }
        $this->returnData = 'no';
    }

    public function emailReport($reportHTML, $email) {
        require $this->fileRoot . "classes/library/phpMailer/PHPMailerAutoload.php";
        $mail = new PHPMailer(true);
        $mail->From = 'reports@no-reply-symantec.com';
        $mail->FromName = 'App Report';
        $mail->addAddress($email);               // Name is optional
        $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Your Mobile Device Report';
        $mail->Body = $reportHTML;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }
    }

    public function getDeviceSettings() {
        $sql = "SELECT * FROM settings";
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

    public function getSettingsGoodPossibilities($setting) {
        $sql = "SELECT * FROM settings_reputation as a "
                . "LEFT JOIN settings as b on a.setting_id=b.id "
                . " WHERE a.setting_id='$setting' AND reputation='0' ";
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

//    public function check_process() {
//        $deviceID = $this->data['device_id'];
//        // print_r($this->data);
//        $androidName = $this->data['package_name'];
//        // echo 'step1 '.$androidName;
//        $deviceIdentifier = $this->getDBDeviceID($deviceID);
//        //check if app is defined in database, if not create
//        $appID = $this->packageExist($androidName);
//        if (intval($appID) > 0) {
//            //check if app is 'owned' by user, add to ownership if not and return user app id
//            $userAppID = $this->userOwnPackage($appID, $deviceIdentifier, $md5, $androidName);
//            if (intval($userAppID) > 0) {
//                $this->returnData['valid'] = true;
//            }
//        }
//    }

    public function getUsersDevice($deviceIdentifier, $email) {
        $query = DB::prepare("SELECT * FROM user_devices as a "
                        . "LEFT JOIN users as b on a.user_id=b.user_id  "
                        . " WHERE a.device_identifier = :di AND b.email= :email ");
        $query->bindParam(':di', $deviceIdentifier);
        $query->bindParam(':email', $email);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetchAll();
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getDBDeviceID($deviceIdentifier, $email) {
        $query = DB::prepare("SELECT * FROM users as a "
                        . "LEFT JOIN user_devices as b on a.user_id=b.user_id "
                        . "WHERE a.email=:email AND b.device_identifier = :di ");
        $query->bindParam(':di', $deviceIdentifier);
        $query->bindParam(':email', $email);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $deviceID = $result['ud_id'];
                return $deviceID;
            } else {
                return false;
            }
        }
        return false;
    }

//    public function check_package() {
//        $deviceID = $this->data['device_id'];
//        // print_r($this->data);
//        $androidName = $this->data['package_name'];
//        // echo 'step1 '.$androidName;
//        $deviceIdentifier = $this->getDBDeviceID($deviceID);
//        //check if app is defined in database, if not create
//        $appID = $this->packageExist($androidName);
//        if (intval($appID) > 0) {
//            //check if app is 'owned' by user, add to ownership if not and return user app id
//            $userAppID = $this->userOwnPackage($appID, $deviceIdentifier, $androidName);
//            if (intval($userAppID) > 0) {
//                $this->returnData['valid'] = true;
//            }
//        }
//    }

    public function userOwnPackage($appID, $deviceIdentifier, $fileAppName, $androidName, $md5, $version, $lastupdate) {
        //TODO add version in check
        $ISvar = 0;
        $ISvar1 = substr($fileAppName, 0, 15);
        $ISvar2 = substr($androidName, 0, 15);
        if ($ISvar1 == 'com.sec.android' || $ISvar2 == 'com.sec.android') {
            $ISvar = 3;
        }
        $ISvar1 = substr($fileAppName, 0, 10);
        if ($ISvar1 == 'com.sprint') {
            $ISvar = 1;
        }

        $ISvar1 = substr($fileAppName, 0, 7);
        if ($ISvar1 == 'com.att') {
            $ISvar = 1;
        }
        $ISvar1 = substr($androidName, 0, 4);
        if ($ISvar1 == 'AT&T') {
            $ISvar = 1;
        }
        ///////////////////////////////////
        $ISvar1 = substr($fileAppName, 0, 11);
        if ($ISvar1 == 'com.android') {
            $ISvar = 2;
        }
        if ($ISvar1 == 'com.verizon') {
            $ISvar = 1;
        }
        if ($ISvar1 == 'com.samsung') {
            $ISvar = 3;
        }
        if ($ISvar1 == 'com.tmobile') {
            $ISvar = 1;
        }
        //////////////////////////////////
        $ISvar1 = substr($fileAppName, 0, 12);
        if ($ISvar1 == 'com.motorola') {
            $ISvar = 3;
        }
        $ISvar1 = substr($fileAppName, 0, 7);
        $ISvar2 = substr($androidName, 0, 7);
        if ($ISvar1 == 'com.sec' || $ISvar2 == 'com.sec' || $ISvar1 == 'com.htc') {
            $ISvar = 3;
        }
        $ISvar1 = substr($androidName, 0, 8);
        if ($ISvar1 == 'T-Mobile') {
            $ISvar = 1;
        }
        $ISvar1 = substr($androidName, 0, 6);
        if ($ISvar1 == 'Sprint') {
            $ISvar = 1;
        }
        $ISvar1 = substr($androidName, 0, 7);
        if ($ISvar1 == 'Verizon') {
            $ISvar = 1;
        }
        $query = DB::prepare("INSERT INTO user_apps SET app_id = :appid, device_id= :di, `install_source`='$ISvar', updated_timestamp=:ts ");
        $query->bindParam(':appid', $appID);
        $query->bindParam(':di', $deviceIdentifier);
        $query->bindParam(':ts', $lastupdate);
        $query->execute();
        return DB::lastInsertId('id');
    }

    public function packageExist($androidName, $appname, $version, $md5) {
        if ($version == null) {
            $version = 'N/A';
        }
        $query = DB::prepare("SELECT * FROM apps WHERE android_name = :an AND `name`= :appname AND `version`= :version AND md5= :md5");
        $query->bindParam(':an', $androidName);
        $query->bindParam(':appname', $appname);
        $query->bindParam(':version', $version);
        $query->bindParam(':md5', $md5);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $appID = $result['app_id'];
                return $appID;
            } else {
                $query = DB::prepare("INSERT INTO apps SET android_name = :an, `name`= :appname, `version`= :version,  md5= :md5");
                $query->bindParam(':an', $androidName);
                $query->bindParam(':appname', $appname);
                $query->bindParam(':version', $version);
                $query->bindParam(':md5', $md5);
                $query->execute();
                return DB::lastInsertId('app_id');
            }
        }
        return false;
    }

    public function register() {
        $deviceID = $this->data['device_id'];
        $query = DB::prepare("SELECT * FROM devices WHERE carrier_model = :cm");
        $query->bindParam(':cm', $this->data['device_model']);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetch();
                $dbDevID = $result['id'];
            } else {
                $dbDevID = '0';
            }
        }
        if ($this->data['device_rooted'] == 'true') {
            $rooted = 1;
        } else {
            $rooted = 0;
        }
        $firmware = $this->data['device_android_build'];
        $query = DB::prepare("UPDATE user_devices SET `device_id`= :devid, `device_manufacturer`= :device_manufacturer, `device_model`=:device_model, "
                        . "`device_bootloader`=:device_bootloader, `android_version`=:android_version, `android_api_version`=:android_api_version, "
                        . " `baseband_version`=:baseband_version, `imei`=:device_imei, `line_number`= :line_number, `device_carrier`= :device_carrier, "
                        . "`last_inspection`= :last_inspection, `unknown_sources`=:marketapps, `pass_encryption_settings`= :pe, `password_visible`= :pv, "
                        . "`screenlock`= :autolock, network_sharing= :device_wifi_tethering, nfc=:nfc, rooted= $rooted, firmware = :firm, usb_debug= :adb  WHERE device_identifier = :deviceID");
        $query->bindParam(':device_manufacturer', $this->data['device_manufacturer']);
        $query->bindParam(':device_model', $this->data['device_model']);
        $query->bindParam(':device_bootloader', $this->data['device_bootloader']);
        $query->bindParam(':android_version', $this->data['device_android_version']);
        $query->bindParam(':android_api_version', $this->data['device_api_level']);
        $query->bindParam(':baseband_version', $this->data['device_baseband']);
        $query->bindParam(':device_imei', $this->data['device_imei']);
        $query->bindParam(':device_carrier', $this->data['device_carrier']);
        $query->bindParam(':adb', $this->data['device_adb']);
        $query->bindParam(':line_number', $this->data['device_number']);
        $query->bindParam(':marketapps', $this->data['device_non_market_apps']);
        $query->bindParam(':pe', $this->data['device_encryption']);
        $query->bindParam(':autolock', $this->data['device_autolock']);
        $query->bindParam(':pv', $this->data['device_show_password']);
        $query->bindParam(':firm', $firmware);
        $query->bindParam(':device_wifi_tethering', $this->data['device_wifi_tethering']);
        $query->bindParam(':nfc', $this->data['device_nfc']);
        $query->bindParam(':deviceID', $deviceID);
        $query->bindParam(':devid', $dbDevID);
        $query->bindParam(':last_inspection', date('Y-m-d H:i:s'));
        $query->execute();
        if ($this->checkForPushSettings() == true) {
            $this->returnData['pushReport'] = true;
        } else {
            $this->returnData['valid'] = true;
        }
    }

    public function checkForPushSettings() {
        $query = DB::prepare("SELECT * FROM settings_pushed WHERE device_id = :di");
        $query->bindParam(':di', $this->data['device_id']);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function inspection() {
        $this->returnData['valid'] = true;
    }

    public function signIn() {
        $email = $this->data['email'];
        $deviceID = $this->data['device_id'];
        //echo"$email";
        if ($this->findUser($email, $deviceID) != false) {

            $this->returnData['valid'] = true;
            $this->returnData['lastInspection'] = $this->lastInspected;
            $this->returnData();
        } else {
            return false;
        }
    }

    public function getDeviceApps() {
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_SPECIAL_CHARS);
        $sql = "SELECT a.ud_id, d.name,d.android_name, c.install_source, c.data_location, d.md5 FROM user_devices AS a "
                . "LEFT JOIN users AS b ON a.user_id=b.user_id "
                . "LEFT JOIN user_apps AS c ON a.ud_id=c.device_id  "
                . "LEFT JOIN apps as d on c.app_id=d.app_id "
                . "WHERE a.ud_id = :ud_id ORDER BY d.name ASC";
        //echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':ud_id', filter_input(INPUT_GET, 'did', FILTER_SANITIZE_SPECIAL_CHARS));
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->returnData = $query->fetchAll();
                $this->returnData();
            } else {
                return false;
            }
        }
    }

    public function getDeviceFS() {
        $sql = "SELECT value FROM user_file_structure WHERE device_id= :did ORDER BY value ASC";
        // echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':did', filter_input(INPUT_GET, 'did', FILTER_SANITIZE_SPECIAL_CHARS));
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->returnData = $query->fetchAll();
                $this->returnData();
            } else {
                return false;
            }
        }
    }

    public function getSettings() {
        $sql = "SELECT * FROM settings_reputation WHERE setting_id = :setting";
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':setting', filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $this->returnData = $query->fetchAll();
                $this->returnData();
            } else {
                return false;
            }
        }
    }

    function returnData() {
        return json_encode($this->returnData);
    }

    function findUser($email, $deviceID) {
        $sql = "SELECT * FROM users as a WHERE a.email = :email ";
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':email', $email);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetchAll();
                $this->uid = $result[0]['user_id'];
                if ($this->checkUserDevice($this->uid, $deviceID) == true) {
                    return $this->uid;
                } else {
                    //add device
                    $this->addDevice($this->uid, $deviceID);
                    $this->lastInspected = 'never';
                }
            } else {
                //add email
                if ($email != '') {
                    $this->addNewEmail($email, $deviceID);
                    $this->uid = 0;
                    $this->lastInspected = 'never';
                    $this->returnData['valid'] = true;
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    public function checkUserDevice($userid, $deviceID) {
        $sql = "SELECT * FROM user_devices as a WHERE a.user_id = :uid AND device_identifier= :did";
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->bindParam(':uid', $userid);
        $query->bindParam(':did', $deviceID);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $result = $query->fetchAll();
                $this->lastInspected = $result[0]['last_inspection'];
                return true;
            }
        }
        return false;
    }

    public function getPushSettings($id) {
        $deviceID = $this->data['device_id'];
        $email = $this->data['email'];
        $deviceIdentifier = $this->getDBDeviceID($deviceID, $email);
        $sql = "SELECT * FROM settings_pushed as a "
                . "LEFT JOIN settings_reputation as b on a.setting_id=b.setting_id "
                . "WHERE a.device_id= :di AND a.setting_id=:sid ";
        //echo$sql.'---------------'.$deviceIdentifier.'--------------- '.$id;
        $query = DB::prepare($sql);
        $query->bindParam(':di', $deviceIdentifier);
        $query->bindParam(':sid', $id);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                $results = $query->fetchAll();
                return $results;
            }
        }
        return true;
    }

    function addNewEmail($email, $deviceID) {
        $query = DB::prepare("INSERT INTO users SET email = :email");
        $query->bindParam(':email', $email);
        $query->execute();
        $userID = DB::lastInsertId();
        $this->addDevice($userID, $deviceID);
    }

    function addDevice($userID, $deviceID) {
        $query = DB::prepare("INSERT INTO user_devices SET user_id = :userID, device_identifier = :deviceID");
        $query->bindParam(':userID', $userID);
        $query->bindParam(':deviceID', $deviceID);
        $query->execute();
        $this->returnData['valid'] = true;
        return true;
    }

    function processInspectionData($data) {
        //TODO process data and add it to database, related by associated device
    }

}
