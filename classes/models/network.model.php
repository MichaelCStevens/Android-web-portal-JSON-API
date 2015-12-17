<?php

class networkModel extends router {

    public $id;
    public $rangehigh = 0;
    public $rangelow = 0;
    public $daterangehigh = 0;
    public $daterangelow = 0;

    function __construct() {
        $this->id = '0';
        $this->rangehigh = $this->getRangeType('chatty_range_start');
        $this->rangelow = $this->getRangeType('chatty_range_end');
        $this->daterangehigh = $this->getRangeType('date_range_end');
        $this->daterangelow = $this->getRangeType('date_range_start');
    }

    function __destruct() {
        
    }

    public function getRangeType($type) {
        $sql = "SELECT value FROM  portal_settings WHERE `key`='$type' ";
        $query = DB::prepare($sql);
        $query->execute();
        $row = $query->fetchObject();
        return $row->value;
    }

    public function getSMSRep($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            // $where = " score <= $this->rangehigh AND score >= $this->rangelow AND ";
        }

        $sql = "SELECT * FROM  `data_sms_multimedia` WHERE $where type='sms' ORDER BY score DESC LIMIT 0,5 ";
        if ($dataSource == 2) {


            if ($rangeType == 'sms') {
                $where.= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where.= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score  FROM  `network_data` WHERE 1=1 $where  ORDER BY " . $data_type . "_score DESC LIMIT 0,5 ";
        }
        //  echo $dataSource;//$sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        return $rows;
    }

    public function getPhoneRep($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            // $where = "  WHERE score <= $this->rangehigh AND score >= $this->rangelow  ";
        }

        $sql = "SELECT * FROM  `data_phone_reputation` $where ORDER BY score DESC LIMIT 0,5 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score  FROM  `network_data` WHERE 1=1 $where ORDER BY " . $data_type . "_score DESC LIMIT 0,5 ";
        }
        //  echo $sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        return $rows;
    }

    public function getPhoneSpam($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //   $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_3rd_party_rbl`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score  FROM  `network_data` WHERE  sms_malware ='YES' $where   ORDER BY " . $data_type . "_score DESC LIMIT 0,5 ";
        }
        //   echo $sql; 
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        return $rows;
    }

    public function get_malware_apps($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score  FROM  `network_data` WHERE  mms_malware ='YES' $where ORDER BY " . $data_type . "_score DESC LIMIT 0,5 ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        return $rows;
    }

    public function get_top5_apps($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES'  $where "
                    . "GROUP BY app_name ORDER BY counted ";
        }
       // echo $sql;
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // print_r($rows[5]);
        return $rows;
    }

    public function get_top5_phishing($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND phishing_url != 'N/A'"
                    . "GROUP BY phishing_url ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // print_r($rows);
        return $rows;
    }

    public function get_top5_isp($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND top_isp != 'N/A'"
                    . "GROUP BY top_isp ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        //  print_r($rows);
        return $rows;
    }

    public function get_top5_domain($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND domain != 'N/A' "
                    . "GROUP BY domain ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // echo $sql;
        //print_r($rows);
        return $rows;
    }

    public function get_top5_ip($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE " . $data_type . "_malware ='YES'  $where  AND  top_ip != 'N/A' "
                    . "GROUP BY top_ip ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // echo $sql;
        //print_r($rows);
        return $rows;
    }

    public function get_top5_phiship($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND sms_score <= " . $this->getRangeType('sms_range_end') . " AND sms_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }
            if ($rangeType == 'app') {
                $where .= " AND  lower(app_name) REGEXP '^[" . $this->getRangeType('app_range_start') . "-" . $this->getRangeType('app_range_end') . "]'";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND  phishing_ip != 'N/A' "
                    . "GROUP BY phishing_ip ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // echo $sql;
        //print_r($rows);
        return $rows;
    }

    public function get_top5_port($range, $rangeType, $dataSource, $data_type) {
        $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND  top_ports != 'N/A' "
                    . "GROUP BY top_ports ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // echo $sql;
        //print_r($rows);
        return $rows;
    }

    public function get_top5_network_attacks($range, $rangeType, $dataSource, $data_type) {
         $where = '';
        if ($range == 2) {
            //  $where=" WHERE date_stamp <= $this->rangehigh AND date_stamp >= $this->rangelow  ";
        }
        $sql = "SELECT * FROM  `data_enterprise`  $where ORDER BY app_name DESC LIMIT 0,10 ";
        if ($dataSource == 2) {

            if ($rangeType == 'sms') {
                $where .= " AND " . $data_type . "_score <= " . $this->getRangeType('sms_range_end') . " AND " . $data_type . "_score >= " . $this->getRangeType('sms_range_start') . " ";
            }
            if ($rangeType == 'date') {
                $where .= " AND  `date` <= '" . strtotime($this->daterangehigh) . "' AND `date` >= '" . strtotime($this->daterangelow) . "' ";
            }

            $sql = "SELECT *, " . $data_type . "_score as score, COUNT(*) as counted FROM  `network_data` WHERE  " . $data_type . "_malware ='YES' $where AND  local_ip != 'N/A' "
                    . "GROUP BY local_ip  ORDER BY counted ";
        }
        $query = DB::prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();
        // echo $sql;
        //print_r($rows);
        return $rows;
    }

    public function getRiskColor($score) {
        //  echo"chattiness is: $score";
        $color = "red";
        if ($score > $this->rangehigh) {
            $color = "red";
        }
        if ($score < $this->rangelow) {
            $color = 'green';
        }
        if ($score >= $this->rangelow && $score < $this->rangehigh) {
            $color = 'yellow';
        }
        return $color;
    }

    public function getChartData($data_type, $range = 1, $rangeType, $dataSource, $type) {
        $data = $this->getSMSRep($range, $rangeType, $dataSource, $data_type);
        $result['chatty_apps'] = array();
        $result['chatty_apps']['items'] = array();
        $result['chatty_apps']['title'] = 'Top 5 Chatty Apps';
        $result['high_rep_imei'] = array();
        $result['high_rep_imei']['items'] = array();
        $result['high_rep_imei']['title'] = 'Top 5 High Rep IMEI';
        $result['spam'] = array();
        $result['spam']['items'] = array();
        $result['spam']['title'] = 'Top 5 Spam Sending Phone #';
        $result['malware_apps'] = array();
        $result['malware_apps']['items'] = array();
        $result['malware_apps']['title'] = 'Top 5 Malware Apps';
        $result['new_apps'] = array();
        $result['new_apps']['items'] = array();
        $result['new_apps']['title'] = 'Top 5 New Apps';

        $result['local_attacks'] = array();
        $result['local_attacks']['items'] = array();
        $result['local_attacks']['title'] = 'Top 5 Local Attacks';

        if ($data_type == 'sms') {
            $result['top5_phishing'] = array();
            $result['top5_phishing']['items'] = array();
            $result['top5_phishing']['title'] = 'Top 5 Phishing URLs';

            $result['top5_isp'] = array();
            $result['top5_isp']['items'] = array();
            $result['top5_isp']['title'] = 'Top 5 SMS offending ISP';
        }
        if ($data_type == 'mms') {
            $result['top5_domain'] = array();
            $result['top5_domain']['items'] = array();
            $result['top5_domain']['title'] = 'Top 5 Offending MMS Domain';

            $result['top5_ip'] = array();
            $result['top5_ip']['items'] = array();
            $result['top5_ip']['title'] = 'Top 5 MMS IP';
        }
        if ($data_type == 'web') {
            $result['top5_ip'] = array();
            $result['top5_ip']['items'] = array();
            $result['top5_ip']['title'] = 'Top 5 Web Phishing IP';

            $result['top5_port'] = array();
            $result['top5_port']['items'] = array();
            $result['top5_port']['title'] = 'Top 5 Offending Ports';
        }


        // print_r($data);
        $scoreKey = 'count';
        foreach ($data as $entry) {
            $scoreVal = $entry['score'];
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($entry['score']);
            }
            $result['chatty_apps']['items'][] = array('name' => $entry['app_name'], $scoreKey => $scoreVal);
        }
        foreach ($this->getPhoneRep($range, $rangeType, $dataSource, $data_type) as $item) {
            $scoreVal = $item['score'];
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($item['score']);
            }
            $result['high_rep_imei']['items'][] = array('name' => $item['phone_number'], $scoreKey => $scoreVal);
        }
        foreach ($this->getPhoneSpam($range, $rangeType, $dataSource, $data_type) as $item) {
            $scoreVal = 100;
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($item['score']);
            }
            $result['spam']['items'][] = array('name' => $item['app_name'], $scoreKey => $scoreVal);
        }
        foreach ($this->get_malware_apps($range, $rangeType, $dataSource, $data_type) as $item) {
            $scoreVal = 100;
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($item['score']);
            }
            $result['malware_apps']['items'][] = array('name' => $item['app_name'], $scoreKey => $scoreVal);
        }
        foreach ($this->get_top5_apps($range, $rangeType, $dataSource, $data_type) as $item) {
           // print_r($item);
            $scoreVal = $item['counted'];
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($item['score']);
            }
            $result['new_apps']['items'][] = array('name' => $item['app_name'], $scoreKey => $scoreVal);
        }
        foreach ($this->get_top5_network_attacks($range, $rangeType, $dataSource, $data_type) as $item) {
            $scoreVal = $item['counted'];
            if ($type == 'risk') {
                $scoreKey = 'color';
                $scoreVal = $this->getRiskColor($item['score']);
            }
            $result['local_attacks']['items'][] = array('name' => $item['app_name'], $scoreKey => $scoreVal);
        }
///////////////////////////
        if ($data_type == 'sms' && $dataSource == '2') {
            foreach ($this->get_top5_phishing($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal = $item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                $result['top5_phishing']['items'][] = array('name' => $item['phishing_url'], $scoreKey => $scoreVal);
            }
            foreach ($this->get_top5_isp($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal = $item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                $result['top5_isp']['items'][] = array('name' => $item['app_name'], $scoreKey => $scoreVal);
            }
        }
////////////////////////////
        if ($data_type == 'mms' && $dataSource == '2') {
            foreach ($this->get_top5_domain($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal = $item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                $result['top5_domain']['items'][] = array('name' => $item['domain'], $scoreKey => $scoreVal);
            }
            foreach ($this->get_top5_ip($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal =$item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                // print_r($item); //''
                $result['top5_ip']['items'][] = array('name' => $item['top_ip'], $scoreKey => $scoreVal);
            }
        }
/////////////////////////
        if ($data_type == 'web' && $dataSource == '2') {

            foreach ($this->get_top5_phiship($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal = $item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                //  print_r($item); //''
                $result['top5_ip']['items'][] = array('name' => $item['top_ip'], $scoreKey => $scoreVal);
            }
            foreach ($this->get_top5_port($range, $rangeType, $dataSource, $data_type) as $item) {
                $scoreVal = $item['counted'];
                if ($type == 'risk') {
                    $scoreKey = 'color';
                    $scoreVal = $this->getRiskColor($item['score']);
                }
                $result['top5_port']['items'][] = array('name' => "Port #" . $item['top_ports'], $scoreKey => $scoreVal);
            }
        }

        return $result;
    }

}
