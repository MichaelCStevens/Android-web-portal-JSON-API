<script>
    var device = 1;
    var sorter = {
        lists: {
            general: {
                comps: new Array(),
                fileStruc: new Array(),
                fileStrucMaster: new Array(),
                masterList: new Array(),
                returnList: new Array(),
                //remove list is list of items that are not similar to other devices comparing
                removeList: new Array(),
                compsSame: new Array(),
                sortBy: 0
            }
        },
        fn: {
            updateLists: function() {
            },
            checkLists: function(curItem, itemsArray) {
                //checks app array if any matches in master list 
            },
            sortLists: function() {
            }
        }
    };
    function addCompare() {
        var userApps = new Array();
        var carrierApps = new Array();
        var oemApps = new Array();
        var androidApps = new Array();
        if (device > 5) {
            device = 1;
        }
        var deviceslottemp = device;
        jQuery(".device-container .span2").each(function(index) {
            var txt = jQuery(this).text();
            if (txt === 'Select a Device') {
                var slotis = jQuery(this).attr('slot');
                if (slotis < deviceslottemp) {
                    deviceslottemp = slotis;
                    // console.log('slot is:' + slotis)
                } else {
                }
            }
        });
        device = deviceslottemp;
        var title = jQuery('.select-device option:selected').attr('device-title');
        var user = jQuery('.select-device option:selected').attr('device-user');
        var deviceid = jQuery('.select-device option:selected').val();
        var compare = jQuery('.data-soruce option:selected').val();
        var sort = jQuery('.data-sort option:selected').val();
        var deviceimg = jQuery('.select-device option:selected').attr('device-img');
        var imei = jQuery('.select-device option:selected').attr('device-imei');
        var rooted = jQuery('.select-device option:selected').attr('device-root');
        var androidVersion = jQuery('.select-device option:selected').attr('device-android-version');
        var device_identifier = jQuery('.select-device option:selected').attr('device-id');
        sorter.lists.general.fileStruc[device] = new Array;

        //    console.log(sorter.lists.general.fileStruc);
        //
        if (rooted == 0) {
            rooted = 'No'
        } else {
            rooted = 'Yes'
        }
        var controller;
        if (device == 1) {
            controller = 'controller';
        } else {
            controller = '';
        }
        var thehtml = '<strong class="title-line" title="' + title + '">' + title + '</strong><span class="user-line" title="' + user + '">' + user +
                '</span><a href="javascript:void(0);" onClick="removeSlot(' + device + ');">Remove</a><div class="device-img"><img src="assets/img/devices/'
                + deviceimg + '"></div>';

        jQuery.getJSON("api.php?portalRequest=getDeviceApps&did=" + deviceid + '&sort=' + sort, function(json) {
            jQuery(".settings-value-select").empty();
            jQuery.each(json, function(key, val) {
                var androidName = val.android_name;
                var name = val.name;
                var data_location = val.data_location;
                var install_source = val.install_source;
                var md5 = val.md5;
                var appinfo = new Array();
                appinfo.push(name);
                appinfo.push(androidName);
                // appinfo.push(data_location);
                appinfo.push(md5);
                //push value if unique
                if (jQuery.inArray(androidName, sorter.lists.general.masterList) == -1) {
                    sorter.lists.general.masterList.push(appinfo);
                }
                if (install_source == 0) {
                    userApps.push(appinfo);
                }
                if (install_source == 1) {
                    carrierApps.push(appinfo);
                }
                if (install_source == 2) {
                    androidApps.push(appinfo);
                }
                if (install_source == 3) {
                    oemApps.push(appinfo);
                }
            });
            sorter.lists.general.comps[device] = new Array;
            sorter.lists.general.comps[device][0] = userApps;
            thehtml += '<h4>Android OS</h4>' + androidVersion + ' <br/>';
            thehtml += '<h4>Rooted:</h4>' + rooted + ' <br/>';
            thehtml += '<h4>IMEI:</h4><span class="app">' + imei + '</span><br/>';
            thehtml += '<div class="app-list"><h4>User Apps:</h4><div class="scroll-1 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][0], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"   title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            sorter.lists.general.comps[device][1] = carrierApps;
            thehtml += '<h4>Carrier Apps:</h4><div class="scroll-2 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][1], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"   title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            sorter.lists.general.comps[device][2] = oemApps;
            thehtml += '<h4>OEM Apps:</h4><div class="scroll-3 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][2], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"  title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            sorter.lists.general.comps[device][3] = androidApps;
            thehtml += '<h4>Base Apps:</h4><div class="scroll-4 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][3], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '" title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';

            jQuery.getJSON("api.php?portalRequest=getDeviceFS&did=" + device_identifier, function(json) {
                var tempAr = new Array();
                jQuery.each(json, function(key, val) {
                    tempAr.push(val.value);
                });
                sorter.lists.general.fileStruc[device] = tempAr;
                //  console.log(sorter.lists.general.fileStruc[device]);
                thehtml += '<h4>File System:</h4><div class="scroll-5 scroll ' + controller + '">';
                jQuery.each(sorter.lists.general.fileStruc[device], function(key, val) {
                    if (jQuery.inArray(val, sorter.lists.general.fileStrucMaster) == -1) {
                        sorter.lists.general.fileStrucMaster.push(val);
                        console.log("pushing value to masterStruc : " + val);
                    }

                    if (val != '') {
                        thehtml += '<span class="fs" data-val=' + val + '>' + val + '</span>';
                    }
                });
                thehtml += '</div>';
                thehtml += '</div>';
                //console.log(sorter.lists.general.masterList);
                if (jQuery('.select-device option:selected').attr('active-slot') > 0) {
                } else {
                    var selectedOptions = jQuery('.select-device option:selected');
                    var last = jQuery('.select-device').children("option").not(":selected").last();
                    jQuery(selectedOptions).insertAfter(last);
                    jQuery('.select-device option:selected').attr('active-slot', device).attr('disabled', 'disabled');
                    jQuery('.device-slot-' + device).html(thehtml);
                    jQuery('.select-device option:first-child').attr("selected", "selected");
                    device++;
                    setupScrolling();
                }
            });
        });
    }
    function resetHTML() {
        for (var device = 1; device < sorter.lists.general.comps.length; device++) {
            var thehtml = '';
            var deviceslottemp = device;
            jQuery(".device-container .span2").each(function(index) {
                var txt = jQuery(this).text();
                if (txt === 'Select a Device') {
                    var slotis = jQuery(this).attr('slot');
                    if (slotis < deviceslottemp) {
                        deviceslottemp = slotis;
                    } else {
                    }
                }
            });
            device = deviceslottemp;
            var controller;
            if (device == 1) {
                controller = 'controller';
            } else {
                controller = '';
            }
            thehtml += '<h4>User Apps:</h4><div class="scroll-1 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][0], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"  title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            thehtml += '<h4>Carrier Apps:</h4><div class="scroll-2 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][1], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"  title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            thehtml += '<h4>OEM Apps:</h4><div class="scroll-3 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][2], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"  title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            thehtml += '<h4>Base Apps:</h4><div class="scroll-4 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.comps[device][3], function(key, val) {
                if (val != '') {
                    thehtml += '<span class="app" data-com="' + val[1] + '"  title="' + val[1] + ' - HASH: ' + val[2] + '">' + val[0] + '</span>';
                }
            });
            thehtml += '</div>';
            thehtml += '<h4>File System:</h4><div class="scroll-5 scroll ' + controller + '">';
            jQuery.each(sorter.lists.general.fileStruc[device], function(key, val) {
                //  console.log(val);
                if (val != '') {
                    thehtml += '<span class="fs" data-val=' + val + '>' + val + '</span>';
                }
            });
            thehtml += '</div>';
            jQuery('.device-slot-' + device + ' .app-list ').html(thehtml);
        }
        setupScrolling();
    }
    function removeSlot(device) {

        jQuery(".device-slot-" + device).html('Select a Device');
        if (device > 1) {
            var selectedOptions = jQuery(".select-device option[active-slot='" + device + "']");
            var first = jQuery('.select-device option:selected').first();
            jQuery(selectedOptions).insertBefore(first);
        }
        jQuery(".select-device option[active-slot='" + device + "']").removeAttr('disabled').removeAttr("active-slot");
        jQuery('.select-device option:first-child').attr("selected", "selected");
        setupScrolling();
    }
    function pushDevice() {
        var title = jQuery('.select-device option:selected').attr('device-title');
        var deviceid = jQuery('.select-device option:selected').val();
        jQuery('span.device-user').html(title);
        jQuery('.push_device_id').val(deviceid);
        jQuery.getJSON("api.php?portalRequest=getAllPush&did=" + deviceid, function(json) {
            jQuery(".selected-device").empty();
            jQuery.each(json, function(key, val) {
                jQuery(".selected-device").append('<tr><td><span class="push-' + val.spid + '">' + val.title + ' Set to ' + val.value_title + '</td><td><a class="btn btn-danger" onclick="removePush(' + val.spid + ')">Remove</a></span></td> </tr>');
            });
        });
        jQuery('#push-settings').modal('show');

    }
    function removePush(x) {
        jQuery.get("api.php?portalRequest=removePush&id=" + x, function() {
            jQuery('.push-' + x).parent().remove();
        });
    }
    function deviceReportReview() {
        var email = jQuery('.select-device option:selected').attr('device-user');
        var deviceid = jQuery('.select-device option:selected').attr('device-id');
        var encoded = '{"email":"' + email + '","device_id":"' + deviceid + '","portal":"1"}';
        var url = 'api.php?reportRequest=report&data=';
        jQuery('#device-report-review .modal-body').html('')
        jQuery.post(url, {data: encoded})
                .done(function(data) {
                    jQuery('#device-report-review .modal-body').html(data);
                    jQuery('#device-report-review .device-user').html(email);

                    jQuery('#device-report-review').modal('show');
                    jQuery('.modal-body').delay(1000).scrollTop(0);
                    // console.log('scrolltop');
                });

    }
    function applySelected() {
        resetHTML();
        var ds = jQuery('.data-sort option:selected').val();
        var json = JSON.stringify(sorter.lists.general.masterList);
        var url = 'api.php?reportRequest=sort&sendMasterList=1';
        jQuery.post(url, {'data': json})
                .done(function(data) {
                });
        if (ds == 2) {
            //only show apps if both have it, so use remove list
            showSimilarities();
        }
        if (ds == 1) {
            //only show apps if both dont have it, so use remove list
            showDifference();
        }
        if (ds == 0) {
            //show all apps
            jQuery(".app").show();
        }
    }
    function setupScrolling() {
        jQuery(".scroll-1.controller").on('scroll', function() {
            var amt = jQuery(this).scrollTop();
            jQuery(".scroll-1:not(.controller)").each(function() {
                jQuery(this).scrollTop(amt);
            });
        });
        jQuery(".scroll-2.controller").on('scroll', function() {
            var amt = jQuery(this).scrollTop();
            jQuery(".scroll-2:not(.controller)").each(function() {
                jQuery(this).scrollTop(amt);
            });
        });
        jQuery(".scroll-3.controller").on('scroll', function() {
            var amt = jQuery(this).scrollTop();
            jQuery(".scroll-3:not(.controller)").each(function() {
                jQuery(this).scrollTop(amt);
            });
        });
        jQuery(".scroll-4.controller").on('scroll', function() {
            var amt = jQuery(this).scrollTop();
            jQuery(".scroll-4:not(.controller)").each(function() {
                jQuery(this).scrollTop(amt);
            });
        });
        jQuery(".scroll-5.controller").on('scroll', function() {
            var amt = jQuery(this).scrollTop();
            jQuery(".scroll-5:not(.controller)").each(function() {
                jQuery(this).scrollTop(amt);
            });
        });
    }
    function  showSimilarities() {
        var compareby = jQuery('.datasource option:selected').val();
        var sort = jQuery('.data-sort option:selected').val();
        var json = JSON.stringify(sorter.lists.general.comps);
        var url = 'api.php?reportRequest=sort&compareBy=' + compareby + '&sort=' + sort;
        jQuery.post(url, {data: json})
                .done(function(data) {
                    var obj = jQuery.parseJSON(data);
                    var list = obj.returnList;
                    jQuery(".app").each(function(index) {
                        var val = jQuery(this).attr('data-com');
                        var isit = jQuery.inArray(val, list);
                        //  console.log("isit val="+isit+" is "+val+" in :::::::::::::::"+list);
                        if (isit > -1) {
                            jQuery(this).show();
                            //  console.log('showing ' + val + ' because its similar pos:' + isit + ' test: ' + list[isit]);
                        } else {
                            jQuery(this).hide();
                            //   console.log('hiding ' + val + ' because its not similar');
                        }
                    });
                });
        jQuery.each(sorter.lists.general.fileStrucMaster, function(key, val) {
            //loop through master list            
            var x = 0
            //the value we are seeking from each device
            var theval = val;
            //// console.log(val);
            jQuery.each(sorter.lists.general.fileStruc, function(key, val) {
                //loop through each device to check if value exists 
                //search device
                console.log("searching for " + theval + " in array with key: " + key + " ");
                console.log(sorter.lists.general.fileStruc[key]);
                if (key > 0) {
                    if (jQuery.inArray(theval, sorter.lists.general.fileStruc[key]) == -1) {
                        //do something if not in device
                        console.log("hiding " + theval);
                        jQuery("[data-val='" + theval + "']").hide();
                    }
                }

            });

        });

    }
    function showDifference() {
        var compareby = jQuery('.datasource option:selected').val();
        var sort = jQuery('.data-sort option:selected').val();
        var json = JSON.stringify(sorter.lists.general.comps);
        var url = 'api.php?reportRequest=sort&compareBy=' + compareby + '&sort=' + sort;
        jQuery.post(url, {data: json})
                .done(function(data) {
                    //  console.log(data);
                    var obj = jQuery.parseJSON(data);
                    var list = obj.returnList;
                    jQuery(".app").each(function(index) {
                        var val = jQuery(this).attr('data-com');
                        var isit = jQuery.inArray(val, list);
                        if (isit > -1) {
                            jQuery(this).show();
                            //   console.log('showing ' + val + ' because its different');
                        } else {
                            jQuery(this).hide();
                            //  console.log('hiding ' + val + ' because its not different');
                        }
                    });
                });
        jQuery.each(sorter.lists.general.fileStrucMaster, function(key, val) {
            //loop through master list            
            var x = 0
            //the value we are seeking from each device
            var theval = val;
            //// console.log(val);
            jQuery.each(sorter.lists.general.fileStruc, function(key, val) {
                //loop through each device to check if value exists 
                //search device
                console.log("searching for " + theval + " in array with key: " + key + " ");
                console.log(sorter.lists.general.fileStruc[key]);
                if (key > 0) {
                    if (jQuery.inArray(theval, sorter.lists.general.fileStruc[key]) != -1) {
                        //do something if not in device
                       
                       x++;
                    }
                }

            });
            var len=sorter.lists.general.fileStruc.length - 1
            if (x == len) {
                 console.log("hiding " + theval);
                jQuery("[data-val='" + theval + "']").hide();
            }

        });
    }
    jQuery(document).ready(function() {
        jQuery(".settings-select").change(function() {
            var settingid = jQuery(this).val();
            if (settingid != 0) {
                jQuery.getJSON("api.php?portalRequest=getSettings&id=" + settingid, function(json) {
                    jQuery(".settings-value-select").empty();
                    jQuery.each(json, function(key, val) {
                        jQuery(".settings-value-select").append('<option value="' + val.value + '">' + val.value_title + '</option>');
                    });
                });
            } else {
                jQuery(".settings-value-select").empty();
                jQuery(".settings-value-select").append('<option value="0">Select a setting above first</option>');
            }
        });
    });

    Array.prototype.remove = function(from, to) {
        var rest = this.slice((to || from) + 1 || this.length);
        this.length = from < 0 ? this.length + from : from;
        return this.push.apply(this, rest);
    };
</script>

<div class="device page">

    <div>
        <span class="labels"><strong>Select a Device:</strong></span>

        <?php // print_r($this->model->getDevices());  ?>
        <select class="select-device" name='device'>
            <?php
            foreach ($this->model->getDevices() as $device) {
                if ($device['img'] == '') {
                    $device['img'] = 'default_device.png';
                }
                if ($device['title'] == '') {
                    $device['title'] = $device['device_model'];
                }
                echo"<option value='" . $device['ud_id'] . "'  device-id='" . $device['device_identifier'] . "'  device-android-version='" . $device['android_version'] . "' device-img='" . $device['img'] . "' device-title=' " . $device['title'] . "' device-user='" . $device['email'] . "'"
                . "  device-root='" . $device['rooted'] . "' device-imei='" . $device['imei'] . "' >Device: " . $device['title'] . " - User: " . $device['email'] . "</option>";
            }
            ?>

        </select>

        <span class="labels wd"><strong>With Device:</strong></span>
        <a class='btn btn-info' onClick="pushDevice()">Push Settings</a>
        <a class='btn btn-inverse' onClick="deviceReportReview()">View Latest Report</a>
        <a class='btn btn-primary' onClick="addCompare()">Add to Compare List</a>
        <br clear='both'/>
    </div>

    <div class="addspace">
        <span class="labels"><strong>Compare By Options:</strong></span>
        <select name='datasource' class='datasource'> 
            <option  value="1">App Name</option> 
            <option  value="2">App Hash</option> 
        </select>

        <select name='data-sort' class='data-sort'>
            <option value='0'>Show All</option>
            <option value='1'>Show Differences</option>
            <option value='2'>Show Similarities</option>
        </select>
        <a class='btn' onclick="applySelected();">Apply Selected Options</a>
    </div>


    <div class="compare-devices-layout row-fluid">
        <div class="span2  ">
            <h3>Device<span>1</span></h3>
        </div>

        <div class="span2">
            <h3>Device<span>2</span></h3>
        </div>
        <div class="span2">
            <h3>Device<span>3</span></h3>
        </div>
        <div class="span2">
            <h3>Device<span>4</span></h3>
        </div>
        <div class="span2">
            <h3>Device<span>5</span></h3>
        </div>
    </div>

    <div class="compare-devices-layout device-container row-fluid">
        <div class="span2 device-slot-1" slot="1">
            Select a Device
        </div>

        <div class="span2 device-slot-2" slot="2">
            Select a Device
        </div>
        <div class="span2 device-slot-3" slot="3">
            Select a Device
        </div>
        <div class="span2 device-slot-4" slot="4">
            Select a Device
        </div>
        <div class="span2 device-slot-5" slot="5">
            Select a Device
        </div>
    </div>

</div>




<div id="push-settings" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Push Settings to <span class="device-user"></span></h3>
    </div>
    <div class="modal-body">
        <form method="post" action="index.php?view=device&push=1">
            <div class="setting">
                <?php // print_r($this->model->getSettings());   ?>
                <select class="settings-select" name="settings-select">
                    <option value='0'> Select a setting</option>
                    <?php
                    foreach ($this->model->getSettings() as $setting) {
                        echo"<option value='" . $setting['id'] . "'> " . ucwords(str_replace('_', ' ', $setting['setting'])) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="setting-value">
                <select class="settings-value-select" name="settings-value-select">
                    <option value='0'> Select a setting from the left first</option>

                </select>
            </div>
            <br clear="both"/>
            <br clear="both"/>
            <input type="hidden" class="push_device_id" name="device_id" value=""/>
            <input type='submit' class="btn btn-primary" value='Push Changes'/> 
        </form>
        <h2>Existing Pushes</h2>
        <table class="selected-device table table-bordered">
        </table>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-inverse"  data-dismiss="modal" aria-hidden="true">Exit without Pushing</a> 
    </div>
</div> 
<!--
<div id="device-report" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Push Settings to <span class="device-user"></span></h3>
    </div>
    <div class="modal-body">
        <form method="post" action="index.php?view=device&push=1">
            <div class="selected-device">
            </div>

            <div class="setting">
<?php // print_r($this->model->getSettings());   ?>
                <select class="settings-select" name="settings-select">
                    <option value='0'> Select a setting</option>
<?php
foreach ($this->model->getSettings() as $setting) {
    echo"<option value='" . $setting['setting'] . "'> " . ucwords(str_replace('_', ' ', $setting['setting'])) . "</option>";
}
?>
                </select>
            </div>
            <div class="setting-value">
                <select class="settings-value-select" name="settings-value-select">
                    <option value='0'> Select a setting above first</option>

                </select>
            </div>


            <input type="hidden" class="push_device_id" name="device_id" value=""/>
        </form>
        <a href="#" class="btn btn-primary">Push Changes</a>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn"  data-dismiss="modal" aria-hidden="true">Exit without Pushing</a>

    </div>
</div>-->


<div id="device-report-review" class="modal hide fade" style="width: 800px;margin-left:-400px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Latest Report for <span class="device-user"></span></h3>
    </div>
    <div class="modal-body">

        <a href="#" class="btn btn-primary">Close Report</a>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn"  data-dismiss="modal" aria-hidden="true">Close Report</a>

    </div>
</div>