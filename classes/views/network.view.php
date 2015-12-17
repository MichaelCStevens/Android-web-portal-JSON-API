<div class="index page">
    <form method="post" action="" enctype="multipart/form-data">
        <div class="row-fluid">
            <div class="span3">
                <label><strong>Select Range:</strong></label>
                <label>
                    <select name="range_type" style="max-width:140px;">
                        <option value='0'>None</option>
                        <option <?php
                        if ($this->controller->rangeType == 'date') {
                            echo "selected='selected'";
                        }
                        ?> value="date">Date</option>
                        <option <?php
                        if ($this->controller->rangeType == 'app') {
                            echo "selected='selected'";
                        }
                        ?>  value="app">App</option>
                        <option  <?php
                        if ($this->controller->rangeType == 'sms') {
                            echo "selected='selected'";
                        }
                        ?> value="sms">Chattines </option>

                    </select>
                </label>

<!--                <label><input type="radio" name="range" <?php if ($this->controller->range == 1 || !isset($this->controller->range)) { ?>checked="checked" <?php } ?>  value="1"/>Total Network</label>
                <label><input type="radio" name="range" <?php if ($this->controller->range == 2) { ?>checked="checked" <?php } ?> value="2"/>Selected Range</label>
                -->
            </div>
            <div class="span3">
                <label><strong>Select Format:</strong></label>
                <label><input type="radio" name="format_type" <?php if ($this->controller->format_type == 1) { ?>checked="checked" <?php } ?> value="1"/><img src="assets/img/risk-icon.png" alt="risk-icon" width="" height="" /> Risk</label>
                <label><input type="radio" name="format_type" <?php if ($this->controller->format_type == 2 || $this->controller->format_type == 0) { ?>checked="checked" <?php } ?> value="2"/><img src="assets/img/chart-icon.png" alt="chart-icon" width="" height="" />Chart</label>

            </div>
            <div class="span3"> 
                <label><strong>Select Data Source:</strong></label>
                <label>
                    <select name="data_source" style="max-width:208px;">
                        <option value="2"   <?php if ($this->controller->dataSource == 2) { ?>selected="selected" <?php } ?> >Network Data Sample 1 </option> 
<!--                        <option value="1"  <?php if ($this->controller->dataSource == 1) { ?>selected="selected" <?php } ?> >Symantec (Phone Rep, Enterprise Data, 3rd party RBL,SMS-Multimedia-WebD. Apps )</option>-->

                    </select>
                </label>

                <label><strong>Select Data Type:</strong></label>
                <label><input type="radio" name="data_type" <?php if ($this->controller->dataType == 'sms' || !isset($this->controller->dataType)) { ?>checked="checked" <?php } ?> value="sms"/>SMS Data</label>
                <label><input type="radio" name="data_type" <?php if ($this->controller->dataType == 'mms') { ?>checked="checked" <?php } ?> value="mms"/>Multi-Media Messaging</label>
                <label><input type="radio" name="data_type" <?php if ($this->controller->dataType == 'web') { ?>checked="checked" <?php } ?>  value="web"/>Internet (Web Data)</label>
    <!--            <label><input type="radio" name="data_type" value="4"/>Phone Reputations</label>-->
            </div>
            <div class="span3">
                <input type="submit" class="btn btn-primary" name="action" value="Go!" style="min-width:142px;margin-bottom:10px;margin-top:23px;"/>
                <a class="btn" href="index.php?view=network" style="min-width:117px">Reset</a>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12 addspace">
                <span class=""> Please use the <a href="index.php?view=settings">SETTINGS</a> feature to configure ranges for analysis.</span>
            </div>
        </div>

        <?php
        if (!empty($this->controller->data['risk_data'])) {
            ?>
            <div class="row-fluid">
                <?php
                $c = 0;
                $c1 = 0;
                foreach ($this->controller->data['risk_data'] as $value) {
                    $c++;
                    $c1++;
                    if ($c1 == 1) {
                        echo'<div class="row-fluid risk-spacer" >';
                    }
                    ?> 
                    <div class="span3">
                        <p class="chart-head"><?php echo $value['title'] ?></p> 
                        <ul class='risk-items'>
                            <?php
                            if (isset($value['items'])) {
                                foreach ($value['items'] as $row) {
                                    ?>
                                    <li><img src="assets/img/risk-<?php echo $row['color']; ?>.png" /> <span title='<?php echo $row['name']; ?>'><?php echo $row['name']; ?></span></li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div> 
                    <?php
                    if ($c1 == 4) {
                        $c1 = 0;
                        echo"</div>";
                    }
                    ?>



                <?php } ?>
            </div>
        <?php } ?>

        <?php
        if (!empty($this->controller->data['chart_data'])) {
            $c = 0;
            $c1 = 0;
            foreach ($this->controller->data['chart_data'] as $chart) {
                $c++;
                $c1++;
                if ($c1 == 1) {
                    echo'<div class="row-fluid">';
                }
                ?> 
                <div class="span6"> 
                    <div id="bar_chart_<?php echo $c; ?>" width="430" height="260px;"></div>
                </div>
                <?php
                if ($c1 == 2) {
                    $c1 = 0;
                    echo"</div>";
                }
                ?>



                <?php
            }
        }
        ?>

    </form>
</div>
<?php
///debug code
//foreach ($this->controller->data['chart_data'] as $chart) {
//    //print_r($chart);
//    echo"<br/><br/><br/>";
//}
?>
<?php if ($this->controller->format_type == 2) { ?> 
    <script src="assets/js/highcharts.js"></script>
    <script src="assets/js/modules/exporting.js"></script>
    <script type="text/javascript">
                jQuery(document).ready(function() {
    <?php
    $chartcounter = 0;
    foreach ($this->controller->data['chart_data'] as $chart) {
        $chartcounter++;
        ?>
            var colors = Highcharts.getOptions().colors,
                    categories = [ <?php
        $c = 0;
        foreach ($chart['items'] as $item) {
            $c++;
            echo"'" . $item['name'] . "'";
            if ($c < count($chart['items'])) {
                echo",";
            }
        }
        ?>],
                    name = '<?php echo $chart['title'] ?>',
                    data = [  <?php
        $c = 0;
        foreach ($chart['items'] as $item) {
            $c++;
            ?> {
                        y: <?php echo $item['count'] ?>,
                                color: colors[<?php echo $c ?>],
                                drilldown: {
                                name: '<?php echo $item['name'] ?>',
                                        categories: ['<?php echo $item['name'] ?>'],
                                        data: [<?php echo $item['count'] ?>],
                                        color: colors[<?php echo $c ?>]
                                }
                        }<?php
            if ($c < count($chart['items'])) {
                echo",";
            }
        }
        ?>];
                    function setChart(name, categories, data, color) {
                    chart.xAxis[0].setCategories(categories, false);
                            chart.series[0].remove(false);
                            chart.addSeries({
                            name: name,
                                    data: data,
                                    color: color || 'white'
                            }, false);
                            chart.redraw();
                    }

            var chart = jQuery('#bar_chart_<?php echo $chartcounter ?>').highcharts({
            chart: {
            type: 'column'
            },
                    title: {
                    text: '<?php echo $chart['title'] ?>'
                    },
                    subtitle: {
                    text: 'Click the columns to view versions.'
                    },
                    xAxis: {
                    categories: categories
                    },
                    yAxis: {
                    title: {
                    text: 'Score'
                    }
                    },
                    plotOptions: {
                    column: {
                    cursor: 'pointer',
                            point: {
                            events: {
                            click: function() {
                            var drilldown = this.drilldown;
                                    if (drilldown) { // drill down
                            setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                            } else { // restore
                            setChart(name, categories, data);
                            }
                            }
                            }
                            },
                            dataLabels: {
                            enabled: true,
                                    color: colors[0],
                                    style: {
                                    fontWeight: 'bold'
                                    },
                                    formatter: function() {
                                    return this.y + 'pts';
                                    }
                            }
                    }
                    },
                    tooltip: {
                    formatter: function() {
                    var point = this.point,
                            s = this.x + ':<b>' + this.y + ' pts score</b><br/>';
                            if (point.drilldown) {
                    s += 'Click to view ' + point.category + ' versions';
                    } else {
                    s += 'Click to return to normal view';
                    }
                    return s;
                    }
                    },
                    series: [{
                    name: name,
                            data: data,
                            color: 'white'
                    }],
                    exporting: {
                    enabled: false
                    }
            })
                    .highcharts(); // return chart
    <?php } ?>
        });


    </script>


<?php } ?>