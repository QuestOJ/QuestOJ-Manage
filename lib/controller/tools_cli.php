<?php

    if (!defined("load") || !isUserLogin()) {
        header("Location:/403");
        exit;
    }

    if (!auth::checkToken()) {
        exit("expired");
    }

    $action = db::escape($_POST["action"]);

    if ($action == "edit") {
        $opinion = db::escape($_POST["opinion"]);
        $id = db::escape($_POST["id"]);

        if (!getProblemInfo($id)) {
            exit("id");
        }

        switch ($opinion) {
            case "add": 
                log::writeLog(2, 3, 315, "新增试题 {$id}");
                if (!is_numeric(client::send(1, "tools_insert", array($id)))) {
                    exit("unknown");
                } 

                break;
            case "delete": 
                log::writeLog(2, 3, 316, "删除试题 {$id}");
                if (!is_numeric(client::send(1, "tools_delete", array($id)))) {
                    exit("unknown");
                }

                break;
            default: 
                exit("invaild");
        }
    } else if ($action == "swap") {
        $id = db::escape($_POST["id"]);
        $id2 = db::escape($_POST["id2"]);

        if (!getProblemInfo($id)) {
            exit("id");
        }
        
        if (!getProblemInfo($id2)) {
            exit("id2");
        }

        log::writeLog(2, 3, 317, "交换试题 {$id} {$id2}");
        if (!is_numeric(client::send(1, "tools_swap", array($id, $id2)))) {
            exit("unknown");
        }

    } else {
        exit("");
    }

    exit("ok");
?>