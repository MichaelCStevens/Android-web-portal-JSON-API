<?php

class enterdataModel {

    function __construct() {
        // session_start();
    }

    public function updateNote() {
        $content = filter_input(INPUT_POST, 'editarea', FILTER_UNSAFE_RAW);
        $query = DB::prepare("UPDATE portal_settings SET value= :value WHERE `key`='enterdata_note'");
        $query->bindParam(':value', $content);
        $query->execute();
        return true;
    }

    public function getNote() {
        $content = filter_input(INPUT_POST, 'editarea', FILTER_UNSAFE_RAW);
        $query = DB::prepare("SELECT value FROM portal_settings WHERE `key`='enterdata_note'");
        $query->bindParam(':value', $content);
        $query->execute();
        $rows = $query->rowCount();
        if (isset($rows)) {
            if ($rows > 0) {
                return $query->fetchObject();
            }
        }
    }

    public function updateData() {
        $result = array(
            'success' => FALSE,
            'error_message' => 'There was a problem with processing your request'
        );

        $action = filter_input(INPUT_POST, 'action', FILTER_UNSAFE_RAW);
        $data_type = filter_input(INPUT_POST, 'data_type', FILTER_UNSAFE_RAW);

        $allowed_exts = array('csv');
        $allowed_mime_types = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        $upload_name = 'analyze';
        $data_type_tables = array(
            1 => 'data_phone_reputation',
            2 => 'data_enterprise',
            3 => 'data_3rd_party_rbl',
            4 => 'data_sms_multimedia',
            5 => 'network_data'
        );

        if (!isset($data_type_tables[$data_type])) {
            $result['error_message'] = 'Please select the data type.';
        } elseif (empty($_FILES[$upload_name]) && !isset($_SESSION['csv'])) {
            $result['error_message'] = 'Please upload a csv file.';
        } else {
            $file_extension = pathinfo($_FILES[$upload_name]["name"], PATHINFO_EXTENSION);

            if (in_array($_FILES[$upload_name]['type'], $allowed_mime_types) AND
                    in_array($file_extension, $allowed_exts) AND
                    !$_FILES[$upload_name]["error"]) {
                $theFile = file_get_contents($_FILES[$upload_name]['tmp_name']);

                if (!isset($_POST['previewarea'])) {
                    //uploaded, so lets return data for preview box
                    $result['upload'] = 1;
                    // $_SESSION['csv'] = $csv_data;
                    $_SESSION['csv'] = $theFile;
                    $_SESSION['dataType'] = $data_type;
                    return true;
                } else {
                    $_SESSION['csv'] = filter_input(INPUT_POST, 'previewarea', FILTER_UNSAFE_RAW);
                }

                $result['success'] = TRUE;
            } else {
                $result['error_message'] = 'The uploaded file is not valid.';
            }
        }
        if (isset($_SESSION['csv'])) {
            $prev = filter_input(INPUT_POST, 'previewarea', FILTER_UNSAFE_RAW);
            if (isset($prev)) {
                $_SESSION['csv'] = $prev;
            }
            $data_type = $_SESSION['dataType'];
            unset($_SESSION['dataType']);
            $csv_data = $this->parse_csv($_SESSION['csv']);
            unset($_SESSION['csv']);
            if ($action == 'Replace') {
                //replace data
                DB::query("DELETE FROM " . $data_type_tables[$data_type]);
            }

            switch ($data_type) {
                case 1:
                    foreach ($csv_data as $row) {
                        if (count($row) != 4)
                            continue;

                        $query = DB::prepare("INSERT INTO " . $data_type_tables[$data_type] . " SET phone_number = :phone_number, score = :score, reason = :reason, date_stamp= :date");
                        $query->execute(array(
                            'phone_number' => $row[0],
                            'score' => $row[1],
                            'reason' => $row[2],
                            'date' => $row[3]
                        ));
                    }
                    break;
                case 2:
                    foreach ($csv_data as $row) {
                        if (count($row) != 4)
                            continue;

                        $query = DB::prepare("INSERT INTO " . $data_type_tables[$data_type] . " SET phone_number = :phone_number, type = :type, app_name = :app_name, date_stamp= :date");
                        $query->execute(array(
                            'phone_number' => $row[0],
                            'type' => $row[1],
                            'app_name' => $row[2],
                            'date' => $row[3]
                        ));
                    }
                    break;
                case 3:
                    foreach ($csv_data as $row) {
                        if (count($row) != 4)
                            continue;

                        $query = DB::prepare("INSERT INTO " . $data_type_tables[$data_type] . " SET  phone_number = :phone_number, type = :type, app_name = :app_name, date_stamp= :date");
                        $query->execute(array(
                            'phone_number' => $row[0],
                            'type' => $row[1],
                            'app_name' => $row[2],
                            'date' => $row[3]
                        ));
                    }
                    break;
                case 4:
                    foreach ($csv_data as $row) {
                        if (count($row) != 5)
                            continue;


                        $query = DB::prepare("INSERT INTO " . $data_type_tables[$data_type] . " SET app_name = :app_name, type = :type, chattiness = :chattiness, col_4 = :col_4, date_stamp= :date");
                        $query->execute(array(
                            'app_name' => $row[0],
                            'type' => $row[1],
                            'chattiness' => $row[2],
                            'col_4' => $row[3],
                            'date' => $row[3]
                        ));
                    }
                    break;
                case 5:
                    foreach ($csv_data as $row) {
                        $c = 0;
                        $c2 = 0;
                        foreach ($row as $r) {
                            if ($r == '') {
                                $row[$c] = 'N/A';
                                $c2++;
                            }
                            $row[$c] = strtoupper($row[$c]);
                            $c++;
                        }
                        if (count($row) === $c2) {
                            continue;
                        }
                        $sql = "INSERT INTO " . $data_type_tables[$data_type] . " SET imei = :imei, app_name = :app_name, "
                                . "sms_score = :smsscore, mms_score = :mmsscore, web_score= :webscore, date= :date, sms_malware= :smsmal, "
                                . "reputation_reason= :reprsn, phone_number= :pn, mms_malware =:mmsmal, web_malware= :webmal, phishing_url= :purl, "
                                . "domain= :domain, phishing_ip= :pip, top_ports = :tp, top_isp=:tisp, top_ip= :tip, local_ip= :local_ip, app_local_ip= :app_local_ip  ";
                        $date = strtotime($row[5]);
                        $parts = array(
                            'imei' => $row[0],
                            'app_name' => $row[1],
                            'smsscore' => $row[2],
                            'mmsscore' => $row[3],
                            'webscore' => $row[4],
                            'date' => $date,
                            'smsmal' => $row[6],
                            'reprsn' => $row[7],
                            'pn' => $row[8],
                            'mmsmal' => $row[9],
                            'webmal' => $row[10],
                            'purl' => $row[11],
                            'domain' => $row[12],
                            'pip' => $row[13],
                            'tp' => $row[14],
                            'tisp' => $row[15],
                            'tip' => $row[16],
                            'local_ip' => $row[17],
                            'app_local_ip' => $row[18]
                        );
                        //echo $sql;
                        //   print_r($parts);
                        $query = DB::prepare($sql);
                        $query->execute($parts);
                    }
                    break;
            }
            $result['success'] = TRUE;
        }

        return $result;
    }

    private function parse_csv($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true) {
        $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
        $enc = preg_replace_callback(
                '/"(.*?)"/s', function ($field) {
            return urlencode(utf8_encode($field[1]));
        }, $enc
        );
        $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
        return array_map(
                function ($line) use ($delimiter, $trim_fields) {
            $fields = $trim_fields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
            return array_map(
                    function ($field) {
                return str_replace('!!Q!!', '"', utf8_decode(urldecode($field)));
            }, $fields
            );
        }, $lines
        );
    }

}
