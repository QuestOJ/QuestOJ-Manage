<?php

    if (!defined("load") || !isUserLogin()) {
        header("Location:/403");
        exit;
    }

    if (!auth::checkToken()) {
        exit("expired");
    }

    $id = $_POST["id"];
    $action = db::escape($_POST["action"]);

    if (!is_numeric($id)) {
        exit();
    }

    if (!wp::checkPostStatus($id)) {
        exit();
    }

    $post = getPostInfo($id);

    if ($action == "delete") {
        $comments = db::escape($_POST["comments"]);

        if (empty($comments)) {
            exit();
        }

        log::writelog(2, 3, 306, "删除博客 {$post["title"]}");
    	wp::deletePost($id);
        auth::sendMessage($post["username"], "您发布的博客已被删除", "您于【{$post["post_time"]}】发布的《{$post["title"]}》因【{$comments}】已被管理员删除，如有疑问请于管理员联系！");
    } else {
        exit("");
    }

    exit("ok");
?>