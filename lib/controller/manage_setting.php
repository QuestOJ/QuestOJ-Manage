<?php

    /**
     * 控制器 : 修改系统设置
     */
    
    if(!defined("load") || !isUserLogin()){
        header("Location:/403");
        exit;
    }

    
    $db = new db();
    $con = $db->connect();

    /**
     * 获取用户输入信息
     */

    $siteName = $_POST["siteName"];
    $siteURL = $_POST["siteURL"];
    $siteShortName = $_POST["siteShortName"];
    $loginTime = $_POST["loginTime"];

    $siteName = mysqli_real_escape_string($con, $siteName);
    $siteURL = mysqli_real_escape_string($con, $siteURL);
    $siteShortName = mysqli_real_escape_string($con, $siteShortName);
    $loginTime = mysqli_real_escape_string($con, $loginTime);

    if (empty($siteName) || empty($siteURL) || empty($siteShortName) || empty($loginTime)) {
        frame::createSession("status", "empty");
        header("Location:/manage/setting");
        exit;
    }

    if (!is_numeric($loginTime)) {
        frame::createSession("status", "logintime");
        header("Location:/manage/setting");
        exit;
    }

    if ($loginTime < 3600) {
        frame::createSession("status", "short");
        header("Location:/manage/setting");
        exit;
    }

    if (substr($siteURL, -1) != "/") {
        $siteURL = $siteURL."/";
    }

    updateSystemSetting("__siteName", $siteName);
    updateSystemSetting("__siteURL", $siteURL);
    updateSystemSetting("__siteShortName", $siteShortName);
    updateSystemSetting("__loginTime", $loginTime);
    header("Location:/manage/setting");
?>