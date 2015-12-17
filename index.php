<?php
 error_reporting(E_ALL);
ini_set('memory_limit', '-1');
//date_default_timezone_set('America/New_York'); 
require_once('classes/db.class.php');
require_once('classes/router.class.php'); 
$view = new router();
?>
<html>
    <head>
        <title><?php echo $view->pageTitle; ?>   </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5 maximum-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/css/layout.css"/>
        <?php if ($view->theme != '0') { ?>
            <link rel="stylesheet" type="text/css" href="assets/css/layout-<?php echo $view->theme ?>.css"/>
        <?php } ?> 
        <script type="text/javascript"> 
        </script>
    </head>
    <body>
        <div id="wrapper">
            <header>
                <div class="logo">
                    <a href="index.php"><img src="assets/img/dashboard-logo.png" alt="Mobile Analytics Dashboard" width="" height="" /></a>
                </div>
                <nav class="nav">
                    <?php echo $view->navMenu; ?>
                </nav>
            </header>
            <div class=" content row-fluid">
                <div class="span12"> 

                    <article>
                        <?php if (isset($view->noticeMsg)) { ?>
                            <div class="alert alert-warning">
                                <?php echo $view->noticeMsg ?>
                            </div>
                        <?php } ?>
                        <?php echo $view->pageContent; ?>
                    </article>
                    <footer>
                        <div class="row-fluid">
                            <div class="span6">Copyright @2013 Symantec Corporation.  All Rights Reserved.</div>
                            <div class="span6 footer-logo"> </div>
                        </div> 
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>