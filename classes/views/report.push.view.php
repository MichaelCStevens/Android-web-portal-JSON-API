<?php
$i = 0;
$tableRows = array();
$c = count($this->getDeviceSettings());
$score = 0;
foreach ($this->getDeviceSettings() as $setting) {
    $push = $this->getPushSettings($setting['id']);
   // print_r($push);
    $varSet = $setting['setting'];

    if (isset($push[0]['setting_value_id'])) {
      //  echo $setting['title'] . " pushed value";
        $goodsetting[0]['value'] = $push[0]['setting_value_id'];
        $tableRows[$i]['title'] = $setting['title'];
        $tableRows[$i]['js'] = $setting['js_intent'];
        $tableRows[$i]['value_title']=$push[0]['value_title'];
        if ($tableRows[$i]['js'] == '') {
            $tableRows[$i]['js'] = 'android.settings.SETTINGS';
        }
   
    }

    $i++;
}
?> 
<link rel="stylesheet" type="text/css" href="<?php echo $this->uriRoot ?>assets/css/bootstrap.css">
<div style="padding:20px">
    <h2>Admin requested the following changes to your Device:</h2> 
Click the items below to open the settings and take action.<br/><br/>
<table  border="1" CELLPADDING="10" class='table table-striped table-bordered'> 

    <?php foreach ($tableRows as $row) { ?>
        <tr>
            <td style="text-align: center;">
                <a class="btn btn-primary" style='width:96%;padding:2%;' href="javascript:SYMANTEC.launchIntent('<?php echo $row['js'] ?>')">
                   Turn <?php echo $row['title'] ?> <?php echo $row['value_title'];?>
                </a>
            </td>
        </tr>
    <?php } ?>

</table>
</div>


