    $(document).ready(function(){
        $('.form-time').daterangepicker({
            format:'YYYY-MM-DD'
        });
    });
    
    //alert poi info
    function modal_jump_poi(id)
    {
        $('#verify-modal-poi').html();
        $.get('index.php?c=poi&a=getModalJumpPoi&id='+id,function(data){
            $('#verify-modal-poi').html(data);
        });
    }