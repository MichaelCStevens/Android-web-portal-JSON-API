<?php
$rt = $this->model->getSettings('range_type')->value;

$cRS = $this->model->getSettings('chatty_range_start')->value;
$cRE = $this->model->getSettings('chatty_range_end')->value;
$charts = $this->model->getSettings('charts')->value;
$theme = $this->model->getSettings('theme')->value;
$definitions = $this->model->getDeviceDefinitions();
$pubPages = json_decode($this->model->getSettings('published_pages')->value);
?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.min.css"/>

<script src="assets/js/tinymce/jquery.tinymce.min.js"></script>
<script src="assets/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: ".wysiwyg"
    });
    var blacklists = new Array();
<?php
foreach (router::$blacklistItems as $bi) {
    echo"blacklists.push('$bi');";
}
?>
    //blacklists = ['ip', 'url', 'sms', 'phone', 'app', 'file', 'permissions'];

    //console.log('blacklist is ' + blacklists);
    jQuery(document).ready(function() {
        jQuery(".title-bar").click(function() {
            jQuery(".blacklist-tables").hide();
            jQuery(this).next().show();
        });


<?php
if (isset($_GET['sd'])) {
    echo" jQuery('#push-settings').modal('show');";
}
?>
        for (var i = 0; i < blacklists.length; i++) {
            (function(blacklist) {
                //  console.log('blacklist value is ' + blacklist);
                jQuery("." + blacklist + " .save-edit").click(function() {
                    if (jQuery('.' + blacklist + ' .edit-value').val() != '') {
                        jQuery('.' + blacklist + '-blacklist :selected').val(jQuery('.' + blacklist + ' .edit-value').val() + '::' + jQuery('.' + blacklist + ' .edit-note').val()).text(jQuery('.' + blacklist + ' .edit-value').val() + '(' + jQuery('.' + blacklist + ' .edit-note').val() + ')');
                        jQuery('.' + blacklist + ' .edit-value, .' + blacklist + ' .edit-note').val('');
                    } else {
                        alert('Entry cannot be empty');
                    }
                });
                jQuery("." + blacklist + " .remove").click(function() {
                    jQuery('.' + blacklist + ' .edit-value, .' + blacklist + ' .edit-note').val('');
                    jQuery('.' + blacklist + '-blacklist :selected').remove();
                });
                jQuery("." + blacklist + "-blacklist").click(function() {
                    //console.log('click ' + blacklist);
                    var val = jQuery('.' + blacklist + '-blacklist :selected').val();
                    var valray = val.split('::');
                    jQuery('.' + blacklist + ' .edit-value').val(valray[0]);
                    jQuery('.' + blacklist + ' .edit-note').val(valray[1]);
                });
            })(blacklists[i]);
        }
    });
    function postData() {
        jQuery.post("index.php?view=settings&edv=1", jQuery("#editsForm").serialize()).done(function() {
            alert('Data saved successfully!');
            jQuery('#edit-settings').modal('hide');
            window.location = 'index.php?view=settings&sd=1'
            //jQuery('#push-settings').modal('show');
        });
    }
    function openSettings() {
        jQuery('#push-settings').modal('show');
    }
    function addValue() {
        var thehtml = '<tr  class="removable">' +
                '<td><input type="text" name="title[]" value=""/></td>' +
                '<td><select name="reputation[]"><option value="0">Good</option><option value="1">Bad</option></select></td>' +
                '<td><input type="text" name="value[]" value=""/></td>' +
                '<td><a href="javascript:void(0)" class="removeItem btn btn-danger">Remove</td>' +
                '</tr>';
        jQuery('.editing-settings').append(thehtml);
    }
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key))
                size++;
        }
        return size;
    };

    function submitForm() {
        var selectors = '';
<?php
$c = 0;
$count = count(router::$blacklistItems);
foreach (router::$blacklistItems as $bi) {
    $c++;
    echo"selectors +='." . $bi . "-blacklist option';  ";
    if ($c < $count) {
        echo"selectors +=',';";
    }
}
?>
        jQuery(selectors).attr('selected', 'selected');
        jQuery('#settings-form').submit();

    }
    function addItem(type) {
        var val = jQuery('.' + type + ' .new-value').val();
        var note = jQuery('.' + type + ' .new-note').val();
        if (val == '') {
            alert('Entry cannot be empty');
            return false;
        }
        jQuery('.' + type + ' .new-value, .' + type + ' .new-note').val('');
        jQuery('.' + type + '-blacklist').append('<option value="' + val + '::' + note + '" title="' + note + '">' + val + ' (' + note + ')</option>');
    }
    function editSetting(setting_id, setting_title) {
        jQuery('.removable').remove();
        jQuery('.eds-title').html(setting_title);
        jQuery('#push-settings').modal('hide');
        console.log(" Data: " + setting_id);
        jQuery.getJSON("api.php?portalRequest=editSettingsDefinition&id=" + setting_id, function(json) {
            jQuery.each(json[0], function(key, val) {
                console.log(val);
                console.log('object size is');
                console.log(Object.size(val))
                if (Object.size(val) == 4) {
                    var reputation = val.reputation;
                    var x1, x2;
                    if (reputation == 0) {
                        x1 = ' selected="selected" ';
                    } else {
                        x2 = ' selected="selected" ';
                    }
                    var thehtml = '<tr class="removable">' +
                            '<td><input type="text" name="title[]" value="' + val.title + '"/></td>' +
                            '<td><select name="reputation[]"><option ' + x1 + ' value="0">Good</option><option  value="1" ' + x2 + '>Bad</option></select></td>' +
                            '<td><input type="text" name="value[]" value="' + val.value + '"/></td>' +
                            '<td><a href="javascript:void(0)" class="btn btn-danger removeItem">Remove</td>' +
                            '</tr>';
                    jQuery('.editing-settings').append(thehtml);

                } else {
                    console.log('object size issue');
                }
            });

            jQuery(".removeItem").live("click", function() {
                console.log('itmem clicked remove the parent');
                jQuery(this).parent().parent().remove();
            });
        });
        jQuery('.edit_setting_id').val(setting_id);
        jQuery('#edit-settings').modal('show');
    }
    $(function() {
        $(".datepicker").datepicker();
        $(".datepicker").datepicker("option", "dateFormat", 'yy-mm-dd');
        $(".datepicker").datepicker("setDate", "<?php echo $this->model->getSettings('date_range_start')->value ?>");

        $(".datepicker1").datepicker();
        $(".datepicker1").datepicker("option", "dateFormat", 'yy-mm-dd');
        $(".datepicker1").datepicker("setDate", "<?php echo $this->model->getSettings('date_range_end')->value ?>");

    });
</script> 
<div class="settings page"> 
    <span class="labels"><strong>Device Settings Definitions:</strong></span><br/>
    <a class="btn btn-warning" href="javascript:void(0)" onClick="openSettings()">Open Device Settings Manager</a>
    <br/><br/>

    <form method="post" action="index.php?view=settings&hc=1" style="margin-top:20px;">
        <span class="labels"><strong>Homepage Copy:</strong></span>
        <textarea class='wysiwyg' name="homecopy">
            <?php
            $content = $this->model->getHomeCopy();
            echo $content[0]['value'];
            ?>
        </textarea>
        <br clear="both"/>
        <input type="submit" value="Save Copy" class="btn btn-success">
    </form> 
    <div class="item">
        <span class="labels"><strong>Clear Database:</strong></span>

        <form class="clear-db-form" method="POST" action="index.php?view=settings&cd=1">

            <label><input name='clearBlacklist' type='checkbox' value='blacklist' /> Blacklist Log</label>
            <label><input name='clearUsers' type='checkbox' value='users' /> Users and Devices</label>
            <label><input name='clearAPI' type='checkbox' value='api' /> API Dev Log</label>
            <label><input name='clearAppDefs' type='checkbox' value='clearAppDefs' /> App Definitions</label>
            <label><input name='clearFileDefs' type='checkbox' value='clearFileDefs' /> File Definitions</label>
            <input type="submit" value='Clear Selected' class="btn btn-danger"/>

        </form>
    </div>  
    <form id='settings-form' method="POST" action="index.php?view=settings&us=1">
        <br/>
        <br/>
        <a value="Save" class="btn btn-success" onclick='submitForm()'>Save the settings Below</a>
        <br/>
        <br/>
        <div style="margin-bottom:10px;">
            <span class="labels"> <strong>Range Options: </strong></span> Analyze a subset of the data by selecting the range. Press "Save", to save your settings.
        </div>
        <div class="row-fluid item">
            <span class="labels"><strong>Date:</strong></span> 
            Range From
            <input class="small datepicker"  style="width:100px" type="text" name="date_range_start" value="<?php echo $this->model->getSettings('date_range_start')->value ?>" /> to
            <input class="small datepicker1"  style="width:100px" type="text" name="date_range_end" value="<?php echo $this->model->getSettings('date_range_end')->value ?>" /> 
        </div> 
        <div class="row-fluid item"> 
            <span class="labels"><strong>App Name: </strong></span> 
            Range From
            <input class="small" type="text" name="app_range_start" value="<?php echo $this->model->getSettings('app_range_start')->value ?>" /> to
            <input class="small" type="text" name="app_range_end" value="<?php echo $this->model->getSettings('app_range_end')->value ?>" /> 
        </div> 
        <div class="row-fluid item">  
            <span class="labels"><strong>SMS Chattines: </strong></span>
            Range From
            <input class="small" type="text" name="sms_range_start" value="<?php echo $this->model->getSettings('sms_range_start')->value ?>" /> to
            <input class="small" type="text" name="sms_range_end" value="<?php echo $this->model->getSettings('sms_range_end')->value ?>" />

        </div> 
        <div class="row-fluid item">
            <div class="range-options">
                <div>
                    <span class="labels"><strong>Score Thresholds:</strong></span> High <input type="text" name="chatty_range_start" value="<?php echo $cRS ?>" /> Low <input type="text" name="chatty_range_end" value="<?php echo $cRE ?>" />
                </div>
            </div>
        </div>

        <!--        <div class="row-fluid item">
                    <span class="labels"><strong>Data Charts:</strong></span>
                    <select name='charts'>
                        <option <?php
        if ($charts == 0) {
            echo"selected='selected'";
        }
        ?> value="0">Chart Type</option>
                        <option <?php
        if ($charts == 1) {
            echo"selected='selected'";
        }
        ?> value="1">Bar Graph</option>
                    </select>
                    <br clear='both'/>
                </div>-->

        <div class="row-fluid item">
            <span class="labels"><strong>Customized Themes:</strong></span>
            <select name='theme'>
                <option <?php
                if ($theme == 0) {
                    echo"selected='selected'";
                }
                ?> value="0">Symantec Theme</option>

                <option <?php
                if ($theme == 'blue') {
                    echo"selected='selected'";
                }
                ?> value="blue">Blue Theme</option>

                <option <?php
                if ($theme == 'yellow') {
                    echo"selected='selected'";
                }
                ?> value="yellow">Yellow Theme</option>

                <option <?php
                if ($theme == 'red') {
                    echo"selected='selected'";
                }
                ?> value="red">Red Theme</option>

                <option <?php
                if ($theme == 'purple') {
                    echo"selected='selected'";
                }
                ?> value="purple">Purple Theme</option>

                <option <?php
                if ($theme == 'green') {
                    echo"selected='selected'";
                }
                ?> value="green">Green Theme</option>
            </select>
            <br clear='both'/>
        </div>

        <div class="item">
            <span class="labels"><strong>Published Pages:</strong></span>
            <span> Uncheck a box to unpublish the link in the navigation and prevent the page from being accessed</span>
            <br clear='both'/><br clear='both'/>
            <label class='pubpage'><input type='checkbox' <?php
                if (in_array('enterdata', $pubPages)) {
                    echo"checked='checked'";
                }
                ?> name='published_pages[]' value='enterdata'/> Enter Data </label>
            <label class='pubpage'><input type='checkbox' <?php
                if (in_array('device', $pubPages)) {
                    echo"checked='checked'";
                }
                ?> name='published_pages[]' value='device'/> Device Dashboard </label>
            <label class='pubpage'><input type='checkbox' <?php
                if (in_array('network', $pubPages)) {
                    echo"checked='checked'";
                }
                ?>  name='published_pages[]' value='network'/> Network Dashboard </label>
            <label class='pubpage'><input type='checkbox' <?php
                if (in_array('android', $pubPages)) {
                    echo"checked='checked'";
                }
                ?>  name='published_pages[]' value='android'/> Android Functions </label>

            <br clear='both'/>
        </div>



        <div class="item">
            <label><strong>BlackList Items:</strong> <span> Be sure to use the green "Save" button when you are done</span></label>

            <!--0=ip, 1=url,2=phone,3=sms 4=app-->
            <?php
            $c = 0;
            foreach (router::$blacklistItems as $bi) {
                $c++;
                ?>
                <div class="row-fluid">
                    <div class="<?php echo $bi; ?> ">
                        <div class='title-bar badge badge-info theme-color-bg'>
                            <span class="labels  "  class='left'> <strong><?php echo ucwords($bi); ?></strong></span>
                        </div>
                        <table border="0" width="100%" class='blacklist-tables' style="<?php
                        if ($c == 1) {
                            echo"display:block;";
                        }
                        ?>" cellpadding="10" cellspacing="5">
                            <tr valign="top">
                                <td>
                                    <label><strong>New:</strong> </label>
                                    <input type='text' class='new-value' placeholder="Enter New <?php echo ucwords($bi); ?>" /> 
                                    <textarea class='new-note' placeholder="Enter a Note..."></textarea>
                                    <a onclick='addItem("<?php echo $bi; ?>");' class='btn btn-primary'>Add</a>
                                </td>
                                <td rowspan="2" align="right">
                                    <span class='right'> Active <?php echo ucwords($bi); ?>:</span><br clear="both">
                                    <select class='<?php echo $bi; ?>-blacklist multi-select' name='<?php echo $bi; ?>-blacklist[]' multiple  >
                                        <?php
                                        $BLitems = $this->model->getBlacklist($bi);
                                        if (is_array($BLitems)) {
                                            foreach ($BLitems as $item) {
                                                echo '<option value="' . $item['value'] . '::' . $item['note'] . '" >' . $item['value'] . ' (' . $item['note'] . ')</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <td class="edit-cell">
                                    <label><strong>Edit:</strong> </label>
                                    <input type='text' name='edit' class='edit-value' value='' placeholder="(Select the active <?php echo $bi; ?> to your right)"> 
                                    <textarea class='edit-note' placeholder="Note..."></textarea>
                                    <div class="group-btn">
                                        <a class='btn save-edit btn-info'>Save Edit</a>
                                        <a class='btn remove btn-danger'>Remove</a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php } ?>
            <a value="Save" class="btn btn-success " onclick='submitForm()'>Save Settings</a>
        </div>
        <br clear="both">
    </form>

    <div id="push-settings" class="modal hide fade" style="width: 700px;margin-left: -350px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Device Settings Definitions</h3>
        </div>
        <div class="modal-body">
            <form method="post" action="index.php?view=settings&definitions=1">
                <table class="table table-striped">
                    <tr>
                        <th>Setting Title</th>
                        <th>Setting Value(s)</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    foreach ($definitions as $def) {
                        $values = $this->model->getDeviceDefinitionsValues($def['id']);
                        $good = array();
                        $bad = array();
                        foreach ($values as $value) {
                            if ($value['reputation'] == 0) {
                                $good[$value['id']][0] = $value['value_title'];
                                $good[$value['id']][1] = $value['value'];
                            } else {
                                $bad[$value['id']][0] = $value['value_title'];
                                $bad[$value['id']][1] = $value['value'];
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $def['title'] ?> </td>
                            <td><table clas="table">
                                    <tr><td>Good(Device Setting)</td><td>Bad(Device Setting)</td></tr>
                                    <tr>
                                        <td><?php
                                            foreach ($good as $g) {
                                                echo$g[0] . " (" . $g[1] . ")";
                                            }
                                            ?></td>
                                        <td><?php
                                            foreach ($bad as $g) {
                                                echo$g[0] . " (" . $g[1] . ")";
                                            }
                                            ?></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <a class="btn btn-warning" onClick="editSetting(<?php echo $def['id']; ?>, '<?php echo $def['title'] ?>')">Edit</a>

                            </td>
                        </tr>
                        <?php
                        // print_r($def);
                    }
                    ?>
                </table>
                <input type="hidden" class="push_device_id" name="device_id" value=""/>
            </form>

        </div>
        <div class="modal-footer">
            <a href="#" class="btn"  data-dismiss="modal" aria-hidden="true">Exit without Changing</a>

        </div>
    </div>


    <div id="edit-settings" class="modal hide fade" style="width: 700px;margin-left: -350px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Edit Device Settings Definitions for <span class="eds-title"></span> <a class="btn btn-primary" onclick="addValue()">Add Value</a></h3>
        </div>
        <div class="modal-body">
            <form id="editsForm" method="post" action="index.php?view=settings&edv=1">
                <table class="editing-settings table table-striped">
                    <tr>
                        <th>Setting Title</th>
                        <th>Setting Value</th>
                        <th>Device Value</th>
                        <th>Action</th>
                    </tr>

                </table>

                <input type="hidden" class="edit_setting_id" name="setting_id" value=""/>
            </form>

        </div>
        <div class="modal-footer">
            <a href="#" onclick="postData();" class="btn btn-success"  data-dismiss="modal" aria-hidden="true">Save</a>

        </div>
    </div>