<?php

    if(!defined("load")){
        header("Location:/403");
        exit;
    }
?>
<?= html::header(); ?>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-edit" class="form-horizontal" method="post">
                <div id="div-edit-token">
                    <input type="hidden" id="input-edit-token" value="<?= frame::clientKey() ?>">
                </div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">增删试题</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="div-edit-id" class="form-group mb-1">
                        <label for="input-edit-id" class="col-form-label">试题 ID:</label>
                        <input type="text" class="form-control" id="input-edit-id">
                        <span class="help-block" id="help-edit-id"></span>
                    </div>                    
                    <div id="div-edit-opinion" class="from-group mb-1">
                        <label for="input-edit-opinion" class="col-form-label">操作:</label>
                        <select class="custom-select" id="input-edit-opinion">
                            <option id="option-add" value="add">新增</option>
                            <option id="option-delete" value="delete">删除</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="button-edit-submit">提交</button>
                </div>
                </form>
            </div>
            <script type="text/javascript">
                function validateEditPost() {
                    var ok = true;
                    ok &= getFormErrorAndShowHelp('edit-id', validateProblemID);
                    return ok;
                }

                function submitEditPost() {
                    if (!validateEditPost()) {
                        return false;
                    }
                    
                    $.post('/tools/cli/submit', {
                        opinion : $('#input-edit-opinion').val(),
                        id : $('#input-edit-id').val(),
                        token : $('#input-edit-token').val(),
                        action : "edit",
                    }, function(msg) {
                        if (msg == 'ok') {
                            $('#editModel').modal('hide');
                            window.location = "<?php echo __siteUrl; ?>service/log";
                        } else if (msg == 'id') {
                            $('#div-edit-id').addClass('has-error');
                            $('#help-edit-id').html('试题ID不存在。');
                        } else if (msg == 'expired') {
                            $('#div-edit-id').addClass('has-error');
                            $('#help-edit-id').html('页面会话已过期。');
                        } else {
                            $('#div-edit-id').addClass('has-error');
                            $('#help-edit-id').html('未知错误。');
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

<div class="modal fade" id="swapModal" tabindex="-1" role="dialog" aria-labelledby="swapModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-swap" class="form-horizontal" method="post">
                <div id="div-swap-token">
                    <input type="hidden" id="input-swap-token" value="<?= frame::clientKey() ?>">
                </div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">交换试题</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="div-swap-id" class="form-group mb-1">
                        <label for="input-swap-id" class="col-form-label">试题一 ID:</label>
                        <input type="text" class="form-control" id="input-swap-id">
                        <span class="help-block" id="help-swap-id"></span>
                    </div>                    
                    <div id="div-swap-id2" class="form-group mb-1">
                        <label for="input-swap-id2" class="col-form-label">试题二 ID:</label>
                        <input type="text" class="form-control" id="input-swap-id2">
                        <span class="help-block" id="help-swap-id2"></span>
                    </div>                    

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="button-swap-submit">提交</button>
                </div>
                </form>
            </div>
            <script type="text/javascript">
                function validateSwapPost() {
                    var ok = true;
                    ok &= getFormErrorAndShowHelp('swap-id', validateProblemID);
                    ok &= getFormErrorAndShowHelp('swap-id2', validateProblemID);
                    return ok;
                }

                function submitSwapPost() {
                    if (!validateSwapPost()) {
                        return false;
                    }
                    
                    $.post('/tools/cli/submit', {
                        id : $('#input-swap-id').val(),
                        id2 : $('#input-swap-id2').val(),
                        token : $('#input-swap-token').val(),
                        action : "swap",
                    }, function(msg) {
                        if (msg == 'ok') {
                            $('#editModel').modal('hide');
                            window.location = "<?php echo __siteUrl; ?>service/log";
                        } else if (msg == 'id') {
                            $('#div-swap-id').addClass('has-error');
                            $('#help-swap-id').html('试题一ID不存在。');
                        } else if (msg == 'id2') {
                            $('#div-swap-id2').addClass('has-error');
                            $('#help-swap-id2').html('试题二ID不存在。');
                        } else if (msg == 'expired') {
                            $('#div-swap-id').addClass('has-error');
                            $('#help-swap-id').html('页面会话已过期。');
                        } else {
                            $('#div-swap-id').addClass('has-error');
                            $('#help-swap-id').html('未知错误。');
                        }
                    });

                    return true;
                }

                $(document).ready(function() {
                    $('#form-swap').submit(function(e) {
                        e.preventDefault();
                        submitSwapPost();
                    });
                });
        </script>
    </div>
</div>

    <nav class="mb-3">
        <button type="button" class="btn btn-default btn-primary" data-toggle="modal" data-target="#editModal" >增删试题</button>
        <button type="button" class="btn btn-default btn-primary" data-toggle="modal" data-target="#swapModal" >交换试题</button>
    </nav>

<script>
    $('#editModel').on('hidden.bs.modal', function (event) {
        showErrorHelp('edit-id');
    })

    $('#swapModel').on('hidden.bs.modal', function (event) {
        showErrorHelp('swap-id');
        showErrorHelp('swap-id2');
    })
</script>
<?= html::footer(); ?>