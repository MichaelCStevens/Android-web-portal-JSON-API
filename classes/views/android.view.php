<script>
    function loadSnap(id, did) {
        jQuery.getJSON("api.php?portalRequest=loadSnapshot&sid=" + id+"&did="+did, function(json) {
            jQuery('#view-snapshot .modal-body table').empty();
            jQuery.each(json[0], function(key, val) {
                if (isNaN(key) == true) {
                    if (key == 'data') {
                        var thehtml = "<table><tr><td>" + key + ' :</td><td>';
                        var jsonval = jQuery.parseJSON(val);
                        jQuery.each(jsonval, function(key2, val2) {
                            if (key2 == 'process_list') {
                                thehtml += "<table><tr><td>" + key2 + ' :</td><td>';

                                jQuery.each(val2, function(key3, val3) {
                                    console.log(val3);
                                    thehtml += val3.process_name + ' - PID:' + val3.process_pid + "<br/>";
                                });
                                thehtml += '</td></tr></table>';
                            } else {
                                thehtml += "<tr><td>" + key2 + ' :</td><td> ' + val2 + "</td></tr> ";
                            }
                        });
                        thehtml += '</td></tr></table>';
                        jQuery('#view-snapshot .modal-body table').append("<tr><td colspan=2>" + thehtml + "</td></tr>");
                    } else {
                        jQuery('#view-snapshot .modal-body table').append("<tr><td>" + key + ' :</td><td> ' + val + "</td></tr> ");
                    }

                }
            });
        });
        jQuery('#view-snapshot').modal('show');

    }
    function viewScripts() {
        jQuery('#view-snapshot').modal('show');
    }
    function loadReport() {
        var report = jQuery('.select-report :selected').attr('data-option');
        var device = jQuery('.select-device :selected').val();
        var value = jQuery('.select-report :selected').val();
        if (value == 0) {
            alert("Please select a " + report + " item");
            return false;
        }
        jQuery('.clear-item').remove();
        jQuery.getJSON("api.php?portalRequest=loadReport&report=" + report + "&device=" + device + "&value=" + value, function(json) {
            jQuery.each(json, function(key, val) {

                var thehtml2 = '';
                if (report == 'blacklist') {
                    jQuery.getJSON("api.php?portalRequest=getBLMatches&type=" + value + "&device=" + device + '&settingVal=' + val.value, function(json2) {
                        var c = 0;
                        jQuery.each(json2, function(key, val2) {
                            console.log(val2);
                            c++;
                            if (val2.value_source == '' || val2.value_source == undefined) {
                                val.value_source = 'None'
                            }
                            if (val2.snapshot_id == '' || val2.snapshot_id == undefined || val2.snapshot_id == 0) {
                                val2.snapshot_id = '<strong>Snapshot ID:</strong>None';
                            } else {
                                val2.snapshot_id = "<a href='javascript:loadSnap(" + val2.snapshot_id + ", \""+val2.device_identifier+"\")'><strong>Snapshot ID:</strong>" + val2.snapshot_id + '</a>';
                            }
                            var logvalue;
                            if (val2.value == '' || val2.value == undefined) {
                                logvalue = 'None';
                            } else {
                                logvalue = "<strong>Log Value:</strong>" + val2.value;
                            }
                            var source = "<strong>Source:</strong>" + val2.value_source + "";
                            if (value == 'sms') {
                                var date = new Date(val2.datetimestamp_first);
                                source = "<strong>Flagged Message:</strong>" + val2.value_source + "<br/><strong>Date:</strong> " + date + "<br/>";
                                if (val2.direction == 0) {
                                    logvalue = "<strong>Incoming text from:</strong> " + val2.value;
                                } else {
                                    logvalue = "<strong> Outgoing text to:</strong> " + val2.value;
                                }

                            }
                            thehtml2 += "<span class='item'><strong>IMEI:</strong>" + val2.imei + "<br/>(" + val2.email + ")<br/>" + logvalue + "<br/>" + source + "<br/>" + val2.snapshot_id + "</span> ";
                        });
                        if (c == 0) {
                            thehtml2 += "<span class='item'>None</span><br/>";
                        }

                        if (val.note == '' || val.note == undefined) {
                            val.note = 'None'
                        }
                        var thehtml = "<tr class='clear-item'>" +
                                "<td width='33%' style='word-wrap: break-word'>" + val.value + "</td>" +
                                "<td width='33%'>" + thehtml2 + "</td>" +
                                "<td width='33%'>" + val.note + "</td>" +
                                "</tr>";
                        jQuery('.table-results-list').append(thehtml);
                    });
                }
                if (report == 'setting') {
                    val.value = 'Setting: ' + val.value_title;
                    jQuery.getJSON("api.php?portalRequest=getSettingsMatches&setting=" + val.setting + "&device=" + device + '&settingVal=' + val.deviceValue, function(json2) {
                        jQuery.each(json2, function(key, val2) {
                            //  console.log('looping');
                            //  console.log(val2);
                            thehtml2 += "<span class='item'>" + val2.imei + "<br clear='both'/>(" + val2.email + ")</span>";
                        });

                        if (val.note == '' || val.note == undefined) {
                            val.note = 'None'
                        }
                        var thehtml = "<tr class='clear-item'>" +
                                "<td width='33%'>" + val.value + "</td>" +
                                "<td width='33%'>" + thehtml2 + "</td>" +
                                "<td width='33%'>" + val.note + "</td>" +
                                "</tr>";
                        jQuery('.table-results-list').append(thehtml);
                    });

                }


            });
        });
    }
</script>


<div class="index page android">
    <!--    <a class='btn btn-primary' onclick="viewScripts()">Add / Edit Scripts</a>-->

    <div class='row-fluid'>
        <div class='span12'>
            <span class="labels"><strong>Select Script:</strong></span>
            <select class='select-report'>
                <option data-option='blacklist' value='0' >-----BlackLists-----</option>

                <?php
                foreach (router::$blacklistItems as $bi) {
                    echo" <option  data-option='blacklist' value='$bi' >" . ucwords($bi) . "</option>";
                }
                ?>
                <option  data-option='setting' value='0' >-----Settings------</option> 
                <?php
                foreach ($this->model->getDeviceDefinitions() as $item) {
                    echo"<option  data-option='setting' value='" . $item['setting'] . "'>" . $item['title'] . "</option>";
                }
                ?>
            </select>

            <span class="labels"><strong>Select Device (Optional):</strong></span>
            <select class='select-device'>
                <option value='0'>All Devices</option>
                <?php
                foreach ($this->model->getDevices() as $item) {
                    echo"<option value='" . $item['device_identifier'] . "'>" . $item['email'] . " - IMEI: " . $item['imei'] . "</option>";
                }
                ?>
            </select>

            <a class='btn btn-primary' onclick="loadReport()" >Run Report</a>


        </div>
    </div>

    <div class="addspace">
        Choose which script and devices to run. Press "Run Report" to see results.
    </div>

    <div class="item results-header">
        <div class='row-fluid  results-header'>
            <div class='span4'>
                Name
            </div>
            <div class='span4'>
                IMEI Matches
            </div>
            <div class='span4'>
                Note
            </div>
        </div>
        <div class='row-fluid results-list'>
            <div class='span12'>
                <table style="table-layout: fixed; width: 100%" class='table table-striped table-results-list table-bordered'>
                    <tr class='clear-item'>
                        <td colspan="3">Run a report</td>
                    </tr>
                </table>
            </div>

        </div>


    </div>


    <br clear='both'/>


</div>



<div id="view-snapshot" class="modal hide fade" style="width: 800px;margin-left:-400px">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Android Snapshot</h3>
    </div>
    <div class="modal-body">
        <table class='table table-bordered'>

        </table>

    </div>
    <div class="modal-footer">
        <a href="#" class="btn"  data-dismiss="modal" aria-hidden="true">Exit</a>

    </div>
</div>