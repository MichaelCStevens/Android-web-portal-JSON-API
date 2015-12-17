<?php
//1 is good 0= bad
//print_r($deviceInfo[0] );
$i = 0;

$tableRows = array();
$c = count($this->getDeviceSettings());
$score = 0;
foreach ($this->getDeviceSettings() as $setting) {
    $push = $this->getPushSettings($setting['id']);

    //print_r($setting);
    $varSet = $setting['setting'];
    // echo"var set is $varSet";
    $goodsetting = $this->getSettingsGoodPossibilities($setting['id']);
    if ($push[0]['setting_value_id'] > 0) {
      //  echo $setting['title'] . " pushed value";
        $goodsetting[0]['value'] = $push[0]['setting_value_id'];
    }
    // print_r($goodsetting);
    $tableRows[$i]['title'] = $setting['title'];
    $tableRows[$i]['js'] = $setting['js_intent'];
    if ($tableRows[$i]['js'] == '') {
        $tableRows[$i]['js'] = 'android.settings.SETTINGS';
    }
    //echo $deviceInfo[0][$varSet]." == ".$goodsetting[0]['value'].'<br/><br/>';
    if ($deviceInfo[0][$varSet] == $goodsetting[0]['value']) {
        $tableRows[$i]['score'] = '1';
        $tableRows[$i]['icon'] = 'up';
        $score++;
    } else {
        $tableRows[$i]['score'] = '0';
        $tableRows[$i]['icon'] = 'down';
    }
    $i++;
}
$finalScore = $score / $c;
$finalScore = number_format($finalScore * 100);
$this->updateScore($finalScore);
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->uriRoot ?>assets/css/bootstrap.css">
<style>
    table{width:90%;margin:0 auto;border:solid 1px ; }
    table .heading{font-weight:bold;}
    .device-score td{padding:20px;}
    .score {
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        text-align:center;
    }
    .score-1 {width:40%;float:right;display:block; background:green;padding:10px 5%; content:"test";color:#fff;}
    .score-0 {width:40%;float:left;display:block; background:red;padding:10px 5%; content:"test";color:#fff;text-align: center}
    .score-holder{width:30%}
</style>
<div class="report page">
    <div class="well">
        <h2>Device Report</h2>
        <table class="table">
            <tr>
                <td>Carrier</td>
                <td><?php echo $deviceInfo[0]['device_carrier'] ?></td>
            </tr>
            <tr>
                <td>Manufacturer</td>
                <td><?php echo ucwords($deviceInfo[0]['device_manufacturer']) ?></td>
            </tr>
            <tr>
                <td>Model</td>
                <td><?php echo $deviceInfo[0]['device_model'] ?></td>
            </tr>
            <tr>
                <td>Firmware / Build Version</td>
                <td><?php echo $deviceInfo[0]['firmware'] ?></td>
            </tr>
            <tr>
                <td>Baseband</td>
                <td><?php echo $deviceInfo[0]['baseband_version'] ?></td>
            </tr>
            <tr>
                <td>Android Build</td>
                <td><?php echo $deviceInfo[0]['android_version'] ?></td>
            </tr>
            <tr>
                <td>Android API version</td>
                <td><?php echo $deviceInfo[0]['android_api_version'] ?></td>
            </tr>
        </table>
    </div>
    <table class='table table-striped table-bordered'>
        <tr>
            <th></th>
            <th class="score-holder">
                <span class="score score-0">Bad</span><span class="score score-1">Good</span>
            </th>

        </tr>
        <tr>
            <th  class="heading"  colspan="2">General Settings</th>
        </tr>
        <?php
        $i = 0;
        while ($i <= 5) {
            ?>
            <tr>
                <td><a href="javascript:SYMANTEC.launchIntent('<?php echo $tableRows[$i]['js'] ?>')"><?php echo $tableRows[$i]['title'] ?></a></td>
                <td class="score-holder"><span class="score score-<?php echo $tableRows[$i]['score'] ?>"><i class=" icon-white icon-thumbs-<?php echo $tableRows[$i]['icon'] ?>"></i></span></td>

            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <th class="heading" colspan="2">Security Settings</th>
        </tr>
        <?php
        $i = 6;
        while ($i <= 11) {
            ?>
            <tr>
                <td><a href="javascript:SYMANTEC.launchIntent('<?php echo $tableRows[$i]['js'] ?>')"><?php echo $tableRows[$i]['title'] ?></a></td>
                <td><span class="score score-<?php echo $tableRows[$i]['score'] ?>"><i class=" icon-white icon-thumbs-<?php echo $tableRows[$i]['icon'] ?>"></i></span></td>

            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <th  class="heading"  colspan="2"><strong>Blacklisted Processes</strong></th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
                <table>
                    <?php
                    $c = 0;
                    if (is_array($this->getBadUserProcesses())) {
                        foreach ($this->getBadUserProcesses() as $bad) {
                            $c++;
                            ?>
                            <tr>
                                <td class="num-col">
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['process_name'] ?> <br/>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <th  class="heading"  colspan="2"><strong> Blacklisted Apps</strong> </th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
              <table>      
             <?php
                if (is_array($this->getBadUserApps())) {
                   
                    $c = 0;
                    foreach ($this->getBadUserApps() as $bad) {
                        $c++;
                        ?>
                <tr>
                    <td class="num-col">
                        #<?php echo $c . ')'; ?>
                    </td>
                    <td>
                        <?php echo $bad['name']; // . "<br/>Version:" . $bad[10]; ?> 
                        <?php //echo " <br/>" . $bad['android_name'] ?>
                    </td>
                </tr>

                <?php
            }
           
        } else {
            echo"<tr><td>Nothing found!</td></tr>";
        }
        ?>
 </table>
        </td>
        </tr>
        <tr>
            <th  class="heading"  colspan="2"><strong>Apps Requesting Blacklisted Permissions</strong></th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
                <table>
                    <?php
                    $c = 0;
                    if (is_array($this->getBadUserAppPerms())) {
                        foreach ($this->getBadUserAppPerms() as $bad) {
                            $c++;
                            ?>
                            <tr>
                                <td class="num-col">
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['name']; // . "<br/>Version: " . $bad[10]; ?>
                                    <?php  echo "<br/>Permission Requested: " .wordwrap($bad['value'], 18, "<br/>", true); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <th  class="heading"  colspan="2"><strong>Apps Requesting Blacklisted Activities</strong></th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
                <table>
                    <?php
                    $c = 0;
                    if (is_array($this->getBadUserAppActs())) {
                        foreach ($this->getBadUserAppActs() as $bad) {
                            $c++;
                            ?>
                            <tr>
                                <td class="num-col">
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['name']; // . "<br/>Version: " . $bad[10]; ?> 
                                    <?php  echo "<br/>Activity Requested: ".wordwrap($bad['value'], 18, "<br/>", true); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <th  class="heading"  colspan="2"><strong>Apps Requesting Blacklisted Receivers</strong></th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
                <table>
                    <?php
                    $c = 0;
                    if (is_array($this->getBadUserAppRecs())) {
                        foreach ($this->getBadUserAppRecs() as $bad) {
                            $c++;
                            ?>
                            <tr>
                                <td class="num-col">
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['name']; // . "<br/>Version: " . $bad[10]; ?> 
                                    <?php echo " <br/>Receiver Requested: " . wordwrap($bad['value'], 18, "<br/>", true); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr>
            <th  class="heading"  colspan="2"><strong>Blacklisted Files</strong></th>
        </tr>
        <tr  class="device-perm-blacklist">
            <td colspan="2">
                <table>
                    <?php
                    $c = 0;
                    if (is_array($this->getBadUserFiles())) {
                        foreach ($this->getBadUserFiles() as $bad) {
                            $c++;
                            ?>
                            <tr>
                                <td class="num-col">
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['filename'] ?> <br/>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
                    ?>
                </table>
            </td>
        </tr>
        <tr  class="device-score">
            <td><strong>Your Device Score</strong></td>
            <td><?php echo $finalScore; ?>% </td>
        </tr>
        <tr class="device-score">
            <td><strong>Average Device Score</strong></td>
            <td><?php echo $this->computeAvgScore(); ?>%</td>
        </tr>
        <tr>
            <td colspan="2">DETAILED REPORT WILL BE SENT TO <?php echo $deviceInfo[0]['email'] ?> SHORTLY.</td>
        </tr>
    </table>
</div>
<br clear='both'/>

