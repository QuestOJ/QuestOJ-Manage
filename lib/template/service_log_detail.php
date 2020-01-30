<?php

if (!defined("load") || !isUserLogin()) {
    header("Location:/403");
    exit;
}

if ($appendFlag == 1) {

    return true;
}


$db = new db();

/**
 * 解析页面 ID
 */

$pregResult = preg_match_all("/\d+/", $_SERVER['REQUEST_URI'], $pageArray);

if (!$pregResult) {
    $ID = 0;
} else {
    $ID = $pageArray[0][0];
}

$jobInfo = getJobInfo($ID);

if (!$jobInfo) {
    header("Location:/404");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $pageTitle; ?> - <?php echo __siteName; ?></title>

    <!-- Bootstrap -->
    <link href="<?php echo __siteUrl; ?>static/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?php echo __siteUrl; ?>static/js/jquery.min.js"></script>
    <script src="<?php echo __siteUrl; ?>static/js/bootstrap.min.js"></script>

    
    <link rel="stylesheet" type="text/css" href="<?php echo __siteUrl; ?>static/css/prettify.css">
    <script src="<?php echo __siteUrl; ?>static/js/prettify.js"></script>

    <?php
        foreach($additional_header as $code){
            echo $code;
        }
    ?>

    <!--[if lt IE 9]>
        <script src="<?php echo __siteUrl; ?>static/js/html5shiv.min.js"></script>
        <script src="<?php echo __siteUrl; ?>static/js/respond.min.js"></script>
    <![endif]-->
	
	<style>
		img{
			width: auto;
			height: auto;
			max-width: 100%;
			max-height: 100%; 
		}
	</style>
</head>

<body onload="PR.prettyPrint()">
<div class="container">
<div class="table-responsive">
<h2> Task #<?php echo $jobInfo["taskID"]; ?>-<?php echo $jobInfo["jobID"];?>-<?php echo $jobInfo["jobTimes"];?> </h2>
<hr/>
<h3> 运行信息 </h3>
    <div class="panel panel-default">
        <div class="panel-body" style="white-space:pre-line"><xmp><?php echo base64_decode($jobInfo["system"]); ?></xmp></div>
    </div>
<h3> 标准输出流 </h3>
    <div class="panel panel-default">
        <div class="panel-body" style="white-space:pre-line"><xmp><?php echo base64_decode($jobInfo["stdout"]); ?></xmp></div>
    </div>

    <h3> 标准错误流 </h3>
    <div class="panel panel-default">
        <div class="panel-body" style="white-space:pre-line"><xmp><?php echo base64_decode($jobInfo["stderr"]); ?></xmp></div>
    </div>

</div>
</body>
<?php
    foreach($additional_footer as $code){
        echo $code;
    }
?>
</html>
