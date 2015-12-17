<script src="assets/js/tinymce/jquery.tinymce.min.js"></script>
<script src="assets/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: ".edit-textarea"
    });

    function checkReplace() {

    }
    jQuery(document).ready(function() {
        jQuery("#previewform").submit(function(event) {
            
            var val = jQuery("input[type=submit][clicked=true]").val();
            console.log('val is ' + val)
            if (val == 'Replace') {
                var r = confirm("Are you sure you want to replace all the items in the database?");
                if (r != true) {
                    event.preventDefault();
                }
            }
        });
        jQuery("form input[type=submit]").click(function() {
            jQuery("input[type=submit]", jQuery(this).parents("form")).removeAttr("clicked");
            jQuery(this).attr("clicked", "true");
        });
    });
</script>

<div class="enter-data page">

    <form method="post" action="index.php?view=enterdata&ud=1" enctype="multipart/form-data">
        <div class="row-fluid">
            <div class="span4">
                <label><strong>What kind of data?</strong></label> 
                <label><input type="radio" name="data_type" value="5" checked="checked" selected="selected"/>Multiple Data Feed Import</label>
<!--                <label><input type="radio" name="data_type" value="1"/>Phone Reputation</label>
                <label><input type="radio" name="data_type" value="2"/>Enterprise Data</label>
                <label><input type="radio" name="data_type" value="3"/>3<sup>rd</sup> Party RBL</label>
                <label><input type="radio" name="data_type" value="4"/>SMS-Multimedia-WebD. Apps</label>-->
            </div>
            <div class="span8">
                <label><strong>Select file:</strong></label>
                <input type="file" name="analyze"/>
                <input type="submit" class="btn btn-primary" name="action" value="Preview"/> 
                <br clear="both"/>
                <br clear="both"/>
                <span class="">Acceptable data formats include: Delimited, .xls, etc. Please use the <a href="index.php?view=settings">SETTINGS</a> feature to configure ranges for analysis.</span>
            </div>
        </div>
    </form>


    <div class="row-fluid notes">
        <div class="span12">
            <?php if (isset($_SESSION['csv'])) { ?>
               <strong> Preview:</strong><br/>
                <form id="previewform" method="post" action="index.php?view=enterdata&ud=1" enctype="multipart/form-data" >
                    <textarea name="previewarea" class="preview-textarea"><?php echo $_SESSION['csv'] ?></textarea> 
                    <br clear="both">
                    <input type="submit" class="btn btn-success" name="action" value="Append"/>
                    <input type="submit" class="btn btn-danger" name="action" value="Replace"/>
                    <a class="btn" href="index.php?view=enterdata&reset=1">Reset</a>
                </form>
            <?php } ?>
           <br/> <br/> <strong>Notes:</strong>
            <form method="post" action="index.php?view=enterdata&un=1">
                <textarea name="editarea" class="edit-textarea"><?php echo $this->model->getNote()->value; ?></textarea>
             <br/>    <input type="submit" class='btn btn-success' value="Save Note">
            </form>
        </div>
    </div>
 

</div>