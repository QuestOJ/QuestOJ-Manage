<?php

class wp {
    public static function request ($url, $header, $data, $post = false) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url.($post ? "" : http_build_query($data)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $html = curl_exec($curl);
        curl_close($curl);

        return json_decode($html, true);
    }

    public static function getToken() {
        $url = WP_ADDRESS."/wp-json/jwt-auth/v1/token";

        $data = array(  
            'username' => WP_USERNAME,
            'password' => WP_PASSWORD
        );  

        return wp::request($url, array(), $data, true)["token"];
    } 

    public static function getAuthorization () {
        return "Authorization: Bearer ".wp::getToken();
    }
    
    public static function getBlogInfo ($id) {
        $auth = wp::getAuthorization();
        $url = WP_ADDRESS."/wp-json/wp/v2/users/".$id;
        return wp::request($url, array($auth), array());
    }

    public static function checkUserStatus ($user) {
        return !empty($user["blog_id"]) && wp::getBlogInfo($user["blog_id"])["id"] == $user["blog_id"];
    }

    public static function calcUserRole ($user) {
        switch ($user["usergroup"]) {
            case 'B': return 'subscriber';
            case 'S': return 'administrator';
            default: return 'author';
        }
    }
    public static function createUser ($user, $password) {
        $auth = wp::getAuthorization();
        $url = WP_ADDRESS."/wp-json/wp/v2/users/";

        $data = array(
            'username' => $user["username"],
            'name' => $user["username"],
            "first_name" => $user["realname"],
            "email" => $user["email"],
            "locale" => "zh_CN",
            "slug" => $user["username"],
            "roles" => wp::calcUserRole($user),
            "password" => $password
        );

        $blog_id = wp::request($url, array($auth), $data, true)["id"];
        DB::update("oj", "update user_info set blog_id = '$blog_id' where username = '{$user['username']}'");
    }

    public static function updateUser ($user) {
        $auth = wp::getAuthorization();
        $url = WP_ADDRESS."/wp-json/wp/v2/users/".$user["blog_id"];

        $data = array(
            'id' => $user["blog_id"],
            'username' => $user["username"],
            'name' => $user["username"],
            "first_name" => $user["realname"],
            "email" => $user["email"],
            "locale" => "zh_CN",
            "slug" => $user["username"],
            "roles" => wp::calcUserRole($user)
        );

        wp::request($url, array($auth), $data, true);
    }

    public static function updateUserPassword ($user, $password) {
        $auth = wp::getAuthorization();
        $url = WP_ADDRESS."/wp-json/wp/v2/users/".$user["blog_id"];

        $data = array(
            'id' => $user["blog_id"],
            'password' => $password
        );     
        
        wp::request($url, array($auth), $data, true);
    }

    public static function updatePost ($id, $info) {
        $info_title = $info["title"]["rendered"];
        $info_date = date("Y-m-d H:i:s", strtotime($info["date_gmt"]));
        $info_modified = date("Y-m-d H:i:s", strtotime($info["modified_gmt"]));
        $info_poster = $info["author"];
        $info_status = $info["status"];
        $update_time = date("Y-m-d H:i:s");

        DB::query("oj", "update `blogs` SET title = '$info_title', post_time = '$info_date', modified_time = '$info_modified', poster = '$info_poster', status = '$info_status', update_time = '$update_time' where id = '$id'");
    }

    public static function cachePost ($id, $info) {
        if (DB::num_rows("oj", "select id from blogs where id = '$id'") != 0) {
            wp::updatePost($id, $info);
        }

        $info_title = $info["title"]["rendered"];
        $info_date = date("Y-m-d H:i:s", strtotime($info["date_gmt"]));
        $info_modified = date("Y-m-d H:i:s", strtotime($info["modified_gmt"]));
        $info_poster = $info["author"];
        $info_status = $info["status"];

        DB::query("oj", "insert into blogs (`id`, `title`, `post_time`, `modified_time`, `poster`, `status`) VALUES ('$id', '$info_title', '$info_date', '$info_modified', '$info_poster', '$info_status')");
    }

    public static function checkPostStatus ($id) {
        $auth = wp::getAuthorization();
        $url = WP_ADDRESS."/wp-json/wp/v2/posts/".$id;

        $info = wp::request($url, array($auth), array(), false);
        
        if ($id && $info["id"] == $id) {
            wp::cachePost($id, $info);
            return true;
        }

        return false;
    }
}