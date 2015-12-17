<?php 
ini_set('memory_limit', '-1'); 
class sortDevices {

    public $returnData;
    public $data;
    public $report;
    public $fileRoot;
    public $devices;
    public $deviceListApps;
    public $deviceListMD5;
    public $masterList;
    public $differences;
    public $compareBy;
    public $sort;

    function __construct() {
        // session_start();
        $this->returnData = array();
        $this->returnData['valid'] = false;
        $this->fileRoot = '/home/reseller/public_html/symantec-android/';
        $this->uriRoot = 'http://kmdgideas.com/symantec-android/';
        $this->data = json_decode(stripslashes($_POST['data']));
        if (isset($_GET['sort'])) {
            $this->sort = $_GET['sort'];
        }
        $this->checkMasterList();
        $this->returnList();
    }

    public function checkMasterList() {
        $sort = 0;
        if (isset($_SESSION['masterList'])) {
            $this->masterList = $_SESSION['masterList'];
        } else {
            $this->masterList = array();
        }
        if (isset($_GET['sendMasterList'])) {
            $_SESSION['masterList'] = array();
            $this->masterList = array();
            foreach ($this->data as $data) {
                $androidName = $data[1];
                $md5 = $data[2];
                $this->masterList[$androidName] = array($androidName, $md5);
            }
        }
        if (isset($_GET['compareBy'])) {
            $this->compareBy = $_GET['compareBy'];
            $this->sortData($sort);
        }
    }

    public function sortData($sort) {
        $this->devices = $this->data;
        unset($this->devices[0]);
        $cc = 0;
        foreach ($this->devices as $data) {
            $cc++;
            //iterating through each device
            //    echo "This is device # $cc";
            //       echo"--------------------------------------------------------------------------------------------";
            $deviceArray = $data;
            if (is_array($deviceArray)) {
                $c = 0;
                foreach ($deviceArray as $da) {
                    //  echo "This is device # $cc AND section $c";
                    //   echo"--------------------------------------------------------------------------------------------";
                    if (is_array($da)) {
                        foreach ($da as $app) {
                            $this->deviceListApps[$cc][] = $app[1]; // $appinfo;
                            $this->deviceListMD5[$cc][] = $app[2]; // $appinfo;
                        }
                    }
                }
            }
        }
        $this->setupList();
    }

    public function setupList() {
        $this->differences = '';
        foreach ($this->masterList as $value) {
            $tvalue = $value[0];
            $name = $value[0];
            if ($this->compareBy == 2) {
                if ($value[1] != 0 && !empty($tvalue)) {
                    $tvalue = $value[1];
                }
                $c = count($this->deviceListMD5);
                $x = 0;
                $c2 = 0;
                foreach ($this->deviceListMD5 as $dlist) {
                    $c2++;
                    if (in_array($tvalue, $dlist)) {
                        $x++;
                    }
                }
                if ($this->sort > 1) {
                    if ($x == $c) {
                        //if x == c then app found on all devices, so its similar
                        $this->differences[] = $name;
                    }
                } else {
                    if ($x != $c) {
                         //if x != c then app NOT found on all devices, so its no a similar app
                        $this->differences[] = $name;
                    }
                }
            } else {
                $c = count($this->deviceListApps);
                $x = 0;
                $c2 = 0;
                foreach ($this->deviceListApps as $dlist) {
                    $c2++;
                    if (in_array($tvalue, $dlist)) {
                        $x++;
                    }
                }
                if ($this->sort > 1) {
                    if ($x == $c) {
                         //if x == c then app found on all devices, so its similar
                        $this->differences[] = $name;
                    }
                } else {
                    if ($x != $c) {
                         //if x != c then app NOT found on all devices, so its no a similar app
                        $this->differences[] = $name;
                    }
                }
            }
        } 
    }

    public function returnList() {
        $_SESSION['masterList'] = $this->masterList;
        $_SESSION['differences'] = $this->differences; 
        ///return list of difference and apply
    }

}
