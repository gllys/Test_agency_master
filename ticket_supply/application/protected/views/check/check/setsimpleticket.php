<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">设置小票打印</h4>
        </div>
        <form class="form-horizontal form-bordered" method="post" action="#" id="form">
            <div class="modal-body">

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="rdio rdio-default inline-block">
                            <input type="radio" checked="checked" value="1"   name="print_type"  <?php if ($simpleType == 1) {
    echo "checked=checked";
} ?>  id="radioDefault3">
                            <label for="radioDefault3">正联+副联(2张小票)</label>
                        </div>
                        <div class="rdio rdio-default inline-block">
                            <input type="radio"  value="2"   name="print_type"  <?php if ($simpleType == 2) {
    echo "checked=checked";
} ?>  id="radioDefault4">
                            <label for="radioDefault4">只打印正联(1张小票)</label>
                        </div>
                    </div>
                </div><!-- form-group -->

            </div>
            <div class="modal-footer">
                <button type="submit" id="form-button" class="btn btn-success">保存</button>
                <button  class="btn btn-default" aria-hidden="true" data-dismiss="modal" type="button">取消</button>
            </div>
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        //提交表单
        $('#form-button').click(function() {
            $.post('/check/check/setsimpleticket/',$('#form').serialize(), function(data) {
                if (data.error) {
                    alert(data.msg);
                } else {
                    alert('设置小票打印成功', function() {
                        location.partReload();
                    });
                }
            }, 'json');
            return false;
        });
    });
</script>
