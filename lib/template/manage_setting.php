<?php

    if(!defined("load") || !isUserLogin()){
        header("Location:/403");
        exit;
    }


    if($appendFlag == 1){
        array_push($additional_header, "<style>.row-centered {text-align:center;}.col-centered {display:inline-block;float:none;text-align:left;margin-right:-4px;}</style>");
        return true;
    }
    
    
?>
        <div class="row row-centered">
            <div class="col-md-5 col-centered">
                <div>
                    <?php
                        if(frame::issetSession("status")){
                            $status = frame::readSession("status");
                            

                            echo "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
                            
                            echo "<strong>修改失败: </strong>";

                            if($status == "empty")
                                echo "表单不能为空";
                            else if($status == "logintime")
                                echo "有效登录时间必须为数字";
                            else if($status == "short") {
                                echo "有效登录时间必须大于 3600 秒";
                            }
                            echo "</div>";
                            
                            

                            frame::deleteSession("status");
                        }
                    ?>
                </div>

                <div style="padding:10px 0px 30px 0px">
                    <h3>
                        <strong>
                            <p class="text-center">系统设置</p>
                        </strong>
                    </h3>
                </div>

                <form class="form-horizontal" action="/manage/setting/submit" method="POST">
                    <div class="form-group">
                        <label for="inputName" class="col-sm-4 control-label">站点名称</label>
                        <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputText" name="siteName" type="text" value="<?php echo __siteName; ?>" placeholder="Site Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputUrl" class="col-sm-4 control-label">站点URL</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputText" name="siteURL" value="<?php echo __siteUrl; ?>" placeholder="Site URL">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputShortname" class="col-sm-4 control-label">站点简称</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputText" name="siteShortName" value="<?php echo __siteShortName ?>" placeholder="Site Short Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputTime" class="col-sm-4 control-label">登录有效时间</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputText" name="loginTime" value="<?php echo __loginTime ?>" placeholder="Login Effective time">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-default">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>