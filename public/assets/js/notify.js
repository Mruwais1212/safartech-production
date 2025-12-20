var notify={
    initialization:function(msg,type){
      var notify_count= $("#notify_container .notif").length;
        var notify_type="";
        if(type=='success')notify_type="alert-success";
        else if(type=="failed") notify_type="alert-danger";
        var position_top=52*notify_count;
        var div_id='notify_div_'+notify_count;
      var notify_div='<div class="notif alert '+notify_type+' col-lg-6 col-md-6 col-sm-6" role="alert" style="top:'+position_top+'px;display:none; text-align: center;position: fixed;z-index: 999999999999999999999999;" id="'+div_id+'">'
       +' <div class="pull-right btn-box-tool close_notify" onclick="notify.close_notify('+div_id+')" style="cursor: pointer"><i class="fa fa-times"></i></div>'
        +'<span>'+msg+'</span></div>';
$("#notify_container").append(notify_div);

        $("#"+div_id).show(300);

        setTimeout(function() {
            $("#"+div_id).hide(300,function(){
                $("#"+div_id).remove();
            });

        }, 10000);
    },
    close_notify:function(div_id){
        $("#"+div_id.id).hide(300,function(){
        	$("#"+div_id.id).remove();
        });
          

    }
}
