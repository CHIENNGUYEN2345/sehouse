<span id="test_mail" class="btn btn-danger"
style="cursor: pointer">{{trans('admin.try_send_mail')}}</span>
<script>
    $(document).ready(function(){
        $("#test_mail").click(function(){
            $.ajax({
                url : "/test-mail",
                type : "post",
                data : {},
                success : function (result){
                  toastr.success(result.msg)
                }
            });
        });
    });
</script>
