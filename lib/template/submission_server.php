<?php

    if (!defined("load") || !isUserLogin()) {
        header("Location:/403");
        exit;
    }

    /**
     * 解析页面 ID
     */

    $pregResult = preg_match_all("/\/page\/(\d+)/", $_SERVER['REQUEST_URI'], $pageArray);

    if(!$pregResult){
        $page = 1;
    }else{
        $page = $pageArray[1][0];
    }
    
    /**
     * 分页设置
     */

    $pageSize = 10;

    /**
     * 获取搜索信息
     */

    $search = db::escape($_GET["s"]);

    $sql = "SELECT * FROM `judger_info`";
    $searchRedirect = "";

    if (isset($_GET["s"])) {
        $sql = $sql." where (`judger_name` like '%$search%')";
        $searchRedirect = $searchRedirect."?s=".urlencode($search);
        $first = true;     
    } else {
        $search = "";
    }

    /**
     * 获取数据总数
     */

    $totalQuery = db::query("oj", $sql);
    $total = mysqli_num_rows($totalQuery);

    /**
     * 计算页面总数
     */

    $totalPage = floor($total / $pageSize);

    if($total % $pageSize != 0){
        $totalPage = $totalPage + 1;
    }

    $totalPage = max($totalPage, 1);

    /**
     * 非法页面ID跳转
     */

    if($page != 1 && $page > $totalPage){
        header("Location:/submission/server/page/{$totalPage}".$searchRedirect);
        exit;
    }
    if($page < 1){
        header("Location:/submission/server/page/1".$searchRedirect);
        exit;
    }
    
    /**
     * 查询数据
     */
    
    $startID = ($page - 1) * $pageSize;
    $dataQuery = db::query("oj", $sql."LIMIT $startID, $pageSize"); 
?>

<?= html::header(); ?>

<div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-edit" class="form-horizontal" method="post">
                <div id="div-token">
                    <input type="hidden" id="input-token" value="<?= frame::clientKey() ?>">
                </div>
                <div id="div-oldjudgername">
                    <input type="hidden" id="input-oldjudgername" value="">
                </div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">编辑</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="div-judgername" class="form-group mb-1">
                        <label for="input-judgername" class="col-form-label">评测机名:</label>
                        <input type="text" class="form-control" id="input-judgername" maxlength="50">
                        <span class="help-block" id="help-judgername"></span>
                    </div>
                    <div id="div-password" class="form-group mb-1">
                        <label for="input-password" class="col-form-label">密码:</label>
                        <input type="text" class="form-control" id="input-password">
                        <span class="help-block" id="help-password"></span>
                    </div>
                    <div id="div-ip" class="form-group mb-1">
                        <label for="input-ip" class="col-form-label">固定IP:</label>
                        <input type="text" class="form-control" id="input-ip">
                        <span class="help-block" id="help-ip"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="button-submit">提交</button>
                </div>
                </form>
            </div>
            <script type="text/javascript">
                function validateEditPost() {
                    var ok = true;
                    ok &= getFormErrorAndShowHelp('judgername', validateJudgername);
                    ok &= getFormErrorAndShowHelp('password', validatePassword);
                    ok &= getFormErrorAndShowHelp('ip', validateIP);
                    return ok;
                }

                function submitEditPost() {
                    if (!validateEditPost()) {
                        return false;
                    }
                    
                    $.post('/submission/server/edit', {
                        judger_name : $('#input-oldjudgername').val(),
                        new_judger_name : $('#input-judgername').val(),
                        password : $('#input-password').val(),
                        ip : $('#input-ip').val(),
                        token : $('#input-token').val(),
                        action : "edit",
                    }, function(msg) {
                        if (msg == 'ok') {
                            $('#editModel').modal('hide');
                            location.reload();
                        } else if (msg == 'expired') {
                            $('#div-judgername').addClass('has-error');
                            $('#help-judgername').html('页面会话已过期。');
                        } else if (msg == 'judgername') {
                            $('#div-judgername').addClass('has-error');
                            $('#help-judgername').html('评测机已存在。');
                        } else {
                            $('#div-judgername').addClass('has-error');
                            $('#help-judgername').html('未知错误。');
                        }
                    });

                    return true;
                }

                $(document).ready(function() {
                    $('#form-edit').submit(function(e) {
                        e.preventDefault();
                        submitEditPost();
                    });
                });
        </script>
        </div>
    </div>
<div class="d-none d-sm-block">
    <nav class="mb-3">
        <form id="form-search" class="form-inline" method="get">
                <div id="form-group-search" class="form-group">
                    <label for="input-s" class="control-label">搜索</label>
                    <input type="text" class="form-control input-sm" name="s" id="input-s" maxlength="50" style="width:25em" value="<?php echo $search; ?>">
                </div>
                <div style="padding: 0 0 0 5px;">
                    <button type="submit" id="submit-search" class="btn btn-default btn-secondary">搜索</button>
                </div>
        </form>
        <script type="text/javascript">
        $('#form-search').submit(function(e) {
            e.preventDefault();
            
            url = '/submission/server';
            qs = [];
            $(['s']).each(function () {
                if ($('#input-' + this).val()) {
                    qs.push(this + '=' + encodeURIComponent($('#input-' + this).val()));
                }
            });
            if (qs.length > 0) {
                url += '?' + qs.join('&');
            }
            location.href = url;
        });
        </script>
    </nav>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">评测机名称</th>
                <th class="text-center">评测机密码</th>
                <th class="text-center">固定IP</th>
                <th class="text-center">最后一次通信时间</th>
                <th class="text-center">状态</th>
                <th class="text-center">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($total == 0) {
                echo "<tr class=\"text-center\"><td colspan=\"7\">暂无数据</td></tr>";
            }

            $id = $startID;

            while($dataQueryRow = mysqli_fetch_assoc($dataQuery)){
                $id += 1;

                $judger_name = $dataQueryRow['judger_name'];
                $password = $dataQueryRow['password'];
                $ip = $dataQueryRow['ip'];
                $last_time = $dataQueryRow['lastTime'];
                $start = $dataQueryRow['start'];

                echo "<tr class=\"text-center\">";
                echo "<td>{$id}</td>";
                echo "<td>{$judger_name}</td>";
                echo "<td>{$password}</td>";
                echo "<td>{$ip}</td>";
                echo "<td>{$last_time}</td>";

                if($start == "1") {
                    echo "<td><b><font color='#5FB404'>正常</font></b></td>";
                    echo '<td><a href = "#" data-toggle="modal" data-target="#editModel" data-judgername="'.$judger_name.'" data-password="'.$password.'" data-ip="'.$ip.'"><span class="glyphicon glyphicon-pencil"></span>编辑</a> &nbsp; <a href="javascript:void(0);" onclick="Ban(\''.$judger_name.'\',\''.frame::clientKey().'\')"><span class="glyphicon glyphicon-remove">禁用</a></td>';
                } else {
                    echo "<td><b><font color='#DF013A'>已禁用</font></b></td>";
                    echo '<td><a href = "#" data-toggle="modal" data-target="#editModel" data-judgername="'.$judger_name.'" data-password="'.$password.'" data-ip="'.$ip.'"><span class="glyphicon glyphicon-pencil"></span>编辑</a> &nbsp; <a href="javascript:void(0);" onclick="Active(\''.$judger_name.'\',\''.frame::clientKey().'\')"><span class="glyphicon glyphicon-ok">启用</a></td>';
                }

                echo "</tr>";
            }
        ?>
        </tbody>
    </table>
</div>

<nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="/submission/server/page/1<?= $searchRedirect ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php
                $cnt = 0;

                if($page - 2 > 0){
                    ++$cnt;
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/submission/server/page/".($page-2).$searchRedirect."\">".($page-2)."</a></li>";
                }

                if($page - 1 > 0){
                    ++$cnt;
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/submission/server/page/".($page-1).$searchRedirect."\">".($page-1)."</a></li>";
                }
            ?>

            <li class="page-item active"><a class="page-link" href="#"><?php echo $page; ?></a></li>
            
            <?php
                $id = 1;

                for($id; $id + $cnt < 5; ++$id){
                    if($id + $page <= $totalPage){
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/submission/server/page/".($page+$id).$searchRedirect."\">".($page+$id)."</a></li>";
                    }
                }
            ?>
            
            <li class="page-item">
                <a class="page-link" href="/submission/server/page/<?php echo $totalPage.$searchRedirect; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <script>
        function Active(Judgername, token){
            $.post('/submission/server/edit', {
                token : token,
                judger_name : Judgername,
                action : "active",
            }, function(msg) {
                if (msg == 'ok') {
                    location.reload();
                }
            });
        }

        function Ban(Judgername, token){
            $.post('/submission/server/edit', {
                token : token,
                judger_name : Judgername,
                action : "ban",
            }, function(msg) {
                if (msg == 'ok') {
                    location.reload();
                }
            });
        }

        $('#editModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var judgername = button.data('judgername')
            var password = button.data('password')
            var ip = button.data('ip')

            var modal = $(this)
            modal.find('.modal-title').text('修改 ' + judgername + ' 评测机信息')
            modal.find('#div-oldjudgername input').val(judgername)
            modal.find('#div-judgername input').val(judgername)
            modal.find('#div-password input').val(password)
            modal.find('#div-ip input').val(ip)
        })

        $('#editModel').on('hidden.bs.modal', function (event) {
            showErrorHelp('judgername');
            showErrorHelp('password');
            showErrorHelp('ip');
        })
    </script>
<?= html::footer(); ?>