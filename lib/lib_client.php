<?php

    class client {
        private static function request($url, $method, $requestData=array()){
            $curl = curl_init();
    
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
           
            if ($method == "POST") {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
            }

            $res = curl_exec($curl);

            if (curl_errno($curl)) {
                log::writeLog(2, 1, 204, "连接远程客户端失败", json_encode(curl_getinfo($curl)));
            }

            curl_close($curl);
            return $res;
        }

        public static function send($id, $target, $arr = array()) {
            $client = getClientInfo($id);

            $task = json_encode(
                array(
                    'taskname' => $target,
                    'args' => $arr
                )
            );

            $requestData = array(
                'id' => $client["clientID"],
                'token' => md5($client["clientID"].$client["clientSecret"]),
                'task' => $task
            );

            $taskID = self::request($client["url"]."/submit", "POST", $requestData);

            if (empty($taskID) || !is_numeric($taskID)) {
                log::writeLog(2, 2, 205, "创建任务请求失败", $taskID);
                return;
            }
            
            return $taskID;
        }
    }
?>