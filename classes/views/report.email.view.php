<?php
$i = 0;
$tableRows = array();
$c = count($this->getDeviceSettings());
$score = 0;
foreach ($this->getDeviceSettings() as $setting) {
    $push = $this->getPushSettings($setting['id']);
    $varSet = $setting['setting'];
    $goodsetting = $this->getSettingsGoodPossibilities($setting['id']);
    if ($push[0]['setting_value_id'] > 0) {
        echo $setting['title'] . " pushed value";
        $goodsetting[0]['value'] = $push[0]['setting_value_id'];
    }
    $tableRows[$i]['title'] = $setting['title'];
    $tableRows[$i]['js'] = $setting['js_intent'];
    if ($tableRows[$i]['js'] == '') {
        $tableRows[$i]['js'] = 'android.settings.SETTINGS';
    }
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

<h2>Device Report</h2>
<table class="table"  border="1" CELLPADDING="10" class='table table-striped table-bordered'>
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
<br/><br/><br/>

<table  border="1" CELLPADDING="10" class='table table-striped table-bordered'> 
    <tr>
        <th></th>
        <th class="score-holder"  colspan="2" style="text-aling:left">   
            <strong>Good or Bad</strong> 
        </th>

    </tr>
    <tr>
        <th  class="heading"  colspan="2">
            <strong> General Settings</strong> 
        </th>   
    </tr>
    <?php
    $i = 0;
    while ($i <= 5) {
        ?>
        <tr>
            <td><a href="javascript:SYMANTEC.launchIntent('<?php echo $tableRows[$i]['js'] ?>')"><strong> <?php echo $tableRows[$i]['title'] ?></strong> </a></td>
            <td class="score-holder">

                <?php
                if ($tableRows[$i]['score'] == 1) {
                    echo"GOOD";
                } else {
                    echo"BAD";
                }
                ?>


            </td>

        </tr>
        <?php
        $i++;
    }
    ?>
    <tr>
        <th class="heading" colspan="2"><strong> Security Settings</strong> </th>
    </tr>
    <?php
    $i = 6;
    while ($i <= 10) {
        ?>
        <tr>
            <td>
                <a href="javascript:SYMANTEC.launchIntent('<?php echo $tableRows[$i]['js'] ?>')">
                    <strong> <?php echo $tableRows[$i]['title'] ?></strong> 
                </a>
            </td>
            <td>
                <?php
                if ($tableRows[$i]['score'] == 1) {
                    echo"GOOD";
                } else {
                    echo"BAD";
                }
                ?>
            </td>

        </tr>
        <?php
        $i++;
    }
    ?>
    <tr>
        <th  class="heading"  colspan="2">
            <strong> Bad Apps</strong>
        </th>   
    </tr>
    <tr  class="device-perm-blacklist">

        <td colspan="2">      <?php
            // print_r($this->getBadUserApps());
            if (is_array($this->getBadUserApps())) {
                echo"<table>";
                $c = 0;
                foreach ($this->getBadUserApps() as $bad) {
                    $c++;
                    ?> 
            <tr>
                <td>
                    #<?php echo $c . ')'; ?>

                </td>
                <td>
                    <?php echo $bad['name'] ;//. "<br/>Version:" . $bad[10]; ?> <br/> 
                        <?php //echo wordwrap($bad['android_name'], 18, "<br/>", true); ?>
                </td>
            </tr> 

            <?php
        }
        echo"  </table>";
    }else{ 
                        echo"<tr><td>Nothing found!</td></tr>";
                    }
    ?>

</td> 
</tr>
<tr>
    <th  class="heading"  colspan="2">
        <strong>  Apps Requesting Blacklisted Permissions </strong>
    </th>   
</tr>
<tr  class="device-perm-blacklist"> 
    <td colspan="2">  
        <table>
            <?php
            if (is_array($this->getBadUserAppPerms())) {
                //   print_r($this->getBadUserAppPerms());
                foreach ($this->getBadUserAppPerms() as $bad) {
                    $c++;
                    ?> 
                    <tr>
                        <td>
                            #<?php echo $c . ')'; ?>

                        </td>
                        <td>
                            <?php echo $bad['name'];// . "<br/>Version: " . $bad[10]; ?>
                             <?php //echo "<br/>Permission Requested:".wordwrap($bad['value'], 18, "<br/>", true); ?>
                        </td>
                    </tr> 
                    <?php
                }
            }else{ 
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
                    <?php  $c = 0;
                    if (is_array($this->getBadUserAppActs())) {
                        foreach ($this->getBadUserAppActs() as $bad) {
                            $c++;
                            ?>
                            <tr> 
                                <td>
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['name'];// . "<br/>Version: " . $bad[10]; ?>  
                                        <?php // echo "<br/>Activity Requested: ".wordwrap($bad['value'], 18, "<br/>", true); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }else{ 
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
                    <?php  $c = 0;
                    if (is_array($this->getBadUserAppRecs())) {
                        foreach ($this->getBadUserAppRecs() as $bad) {
                            $c++;
                            ?>
                            <tr> 
                                <td>
                                    #<?php echo $c . ')'; ?>
                                </td>
                                <td>
                                    <?php echo $bad['name'];// . "<br/>Version: " . $bad[10]; ?>  <?php //echo "<br/>Receiver Requested: ".wordwrap($bad['value'], 18, "<br/>", true); ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }else{ 
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
                                <td>
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
    <td><strong> Your Device Score</strong> </td>
    <td><?php echo $finalScore; ?>% </td> 
</tr>

<tr class="device-score">
    <td><strong> Average Device Score</strong> </td>
    <td><?php echo $this->computeAvgScore(); ?>%</td>

</tr>

</table>


