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

    $pageSize = 15;

    /**
     * 获取搜索信息
     */

    $search_id = DB::escape($_GET["id"]);
    $search_type = DB::escape($_GET["type"]);
    $search_user = DB::escape($_GET["user"]);

    if ($search_type == "G") {
        $search_type_trans = "好评";
    } else if ($search_type == "B") {
        $search_type_trans = "差评";
    }

    $first = false;

    $sql = "SELECT * FROM `click_zans`";
    $searchRedirect = "";

    if (isset($_GET["user"])) {
        if (!$first) {
            $sql = $sql." where username = '$search_user'";
            $searchRedirect = $searchRedirect."?user=".urlencode($search_user);
            $first = true;
        } else {
            $sql = $sql." and username = '$search_user'";
            $searchRedirect = $searchRedirect."&user=".urlencode($search_user);
        }        
    } else {
        $search_user = "";
    }

    if (isset($_GET["id"])) {
        if (!$first) {
            $sql = $sql." where target_id = '$search_id'";
            $searchRedirect = $searchRedirect."?id=".urlencode($search_id);
            $first = true;
        } else {
            $sql = $sql." and target_id = '$search_id'";
            $searchRedirect = $searchRedirect."&id=".urlencode($search_id);
        }        
    } else {
        $search_id = "";
    }

    if ($search_type == "G") {
        if (!$first) {
            $sql = $sql." where val = '1'";
            $searchRedirect = $searchRedirect."?type=".urlencode($search_type);
            $first = true;
        } else {
            $sql = $sql." and val = '1'";
            $searchRedirect = $searchRedirect."?type=".urlencode($search_type);
        } 
    }

    if ($search_type == "B") {
        if (!$first) {
            $sql = $sql." where val = '-1'";
            $searchRedirect = $searchRedirect."?type=".urlencode($search_type);
            $first = true;
        } else {
            $sql = $sql." and val = '-1'";
            $searchRedirect = $searchRedirect."?type=".urlencode($search_type);
        } 
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
        header("Location:/tools/zan/page/{$totalPage}".$searchRedirect);
        exit;
    }
    if($page < 1){
        header("Location:/tools/zan/page/1".$searchRedirect);
        exit;
    }

    /**
     * 查询数据
     */
    
    $startID = ($page - 1) * $pageSize;
    $dataQuery = db::query("oj", $sql."LIMIT $startID, $pageSize"); 
?>

<?= html::header(); ?>
<div class="d-none d-sm-block">
    <nav style="text-align: center; padding:0px 0px 20px 0px;">
        <form id="form-search" class="form-inline" method="get">
                <div id="form-group-user" class="form-group">
                    <label for="input-user" class="control-label">用户</label>
                    <input type="text" class="form-control input-sm" name="user" id="input-user" maxlength="20" style="width:15em" value="<?php echo $search_user; ?>">
                </div>
                <div id="form-group-search" class="form-group">
                    <label for="input-id" class="control-label">编号</label>
                    <input type="text" class="form-control input-sm" name="id" id="input-id" maxlength="5" style="width:7em" value="<?php echo $search_id; ?>">
                </div>
                <div id="form-group-type" class="form-group">
                <label for="input-type" class="control-label">评价</label>
                <select id="input-type" name="type" class="form-control input-sm">
                    <option selected value="<?= $search_type ?>" hidden><?= $search_type_trans; ?></option>
                    <?php if (!empty($search_type)) {echo "<option></option>";} ?>
                    <option value="G">好评</option>
                    <option value="B">差评</option>
                </select>
                </div>
                <div style="padding: 0 0 0 5px;">
                    <button type="submit" id="submit-search" class="btn btn-default btn-secondary">搜索</button>
                </div>
        </form>

        <script type="text/javascript">
        $('#form-search').submit(function(e) {
            e.preventDefault();
            
            url = '/tools/zan';
            qs = [];
            $(['user', 'type', 'id']).each(function () {
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
                <th class="text-center">用户名</th>
                <th class="text-center">类型</th>
                <th class="text-center">编号</th>
                <th class="text-center">评价</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if ($total == 0) {
                echo "<tr class=\"text-center\"><td colspan=\"5\">暂无数据</td></tr>";
            }
            
            $id = $startID;

            while($dataQueryRow = mysqli_fetch_assoc($dataQuery)){
                $id += 1;

                $username = $dataQueryRow["username"];
                $type = $dataQueryRow["type"];
                $target_id = $dataQueryRow["target_id"];
                $val = $dataQueryRow["val"];

                echo "<tr class=\"text-center\">";
                echo "<td>#{$id}</td>";
                echo "<td><a href=\"/user/list?s=".$username."\" target=\"_blank\">{$username}</a></td>";

                if ($type == 'P') {
                    echo "<td>试题</td>";
                    echo "<td><a href=\"".OJ_URL."/problem/{$target_id}\" target=\"_blank\">#{$target_id}. ".getProblemInfo($target_id)["title"]."</a></td>";
                } else if ($type == 'C') {
                    echo "<td>比赛</td>";
                    echo "<td><a href=\"".OJ_URL."/contest/{$target_id}\" target=\"_blank\">#{$target_id}. ".getContestInfo($target_id)["name"]."</a></td>";
                }

                if ($val == '1') {
                    echo "<td><b><font color='#5FB404'>好评</font></b></td>";
                } else if ($val == "-1") {
                    echo "<td><b><font color='#DF013A'>差评</font></b></td>";
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
                <a class="page-link" href="/tools/zan/page/1<?= $searchRedirect ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php
                $cnt = 0;

                if($page - 2 > 0){
                    ++$cnt;
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/tools/zan/page/".($page-2).$searchRedirect."\">".($page-2)."</a></li>";
                }

                if($page - 1 > 0){
                    ++$cnt;
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/tools/zan/page/".($page-1).$searchRedirect."\">".($page-1)."</a></li>";
                }
            ?>

            <li class="page-item active"><a class="page-link" href="#"><?php echo $page; ?></a></li>
            
            <?php
                $id = 1;

                for($id; $id + $cnt < 5; ++$id){
                    if($id + $page <= $totalPage){
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"/tools/zan/page/".($page+$id).$searchRedirect."\">".($page+$id)."</a></li>";
                    }
                }
            ?>
            
            <li class="page-item">
                <a class="page-link" href="/tools/zan/page/<?php echo $totalPage.$searchRedirect; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
<?= html::footer(); ?>