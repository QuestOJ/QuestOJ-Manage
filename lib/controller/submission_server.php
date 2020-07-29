<?php

    if (!defined("load") || !isUserLogin()) {
        header("Location:/403");
        exit;
    }

    if (!auth::checkToken()) {
        exit("expired");
    }

    $action = $_POST["action"];
    $judgername = $_POST["judger_name"];

    if (!is_string($action) || !is_string($judgername)) {
        exit();
    }

    $action = db::escape($action);
    $judgername = db::escape($judgername);

    if (!$judger = getJudgerInfo($judgername)) {
        exit("judger");
    }

    if ($action == "ban") {
        log::writelog(2, 3, 313, "禁用评测机 {$judgername}");

        if ($judger["start"] == "0") {
            log::writelog(2, 2, 3131, "评测机已禁用,无需操作");
        }

        db::query("oj", "UPDATE `judger_info` SET `start` = '0' where `judger_name` = '{$judgername}'");
    } else if ($action == "active") {
        log::writelog(2, 3, 313, "启用评测机 {$judgername}");

        if ($judger["start"] == "1") {
            log::writelog(2, 2, 3132, "评测机已启用,无需操作");
        }

        db::query("oj", "UPDATE `judger_info` SET `start` = '1' where `judger_name` = '{$judgername}'");
    } else if ($action == "edit") {
        $newjudgername = DB::escape($_POST['new_judger_name']);
        $password = DB::escape($_POST['password']);
        $ip = DB::escape($_POST['ip']);

        if (!validateIP($ip)) {
            exit('');
        }

        if ($judgername != $newjudgername && getJudgerInfo($newjudgername)) {
            exit('judgername');
        }

        log::writeLog(2, 3, 314, "更新评测机 {$judgername} 信息");
        db::query("oj", "UPDATE `judger_info` SET `judger_name` = '$newjudgername', `password` = '$password', `ip` = '$ip' where `judger_name` = '{$judgername}'");
    } else {
        exit("");
    }

    exit("ok");
?>