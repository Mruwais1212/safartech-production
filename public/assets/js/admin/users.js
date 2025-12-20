/**
 * Created by ABDOOOOO on 1/4/2018.
 */

var users = {
    Route: "",
    data: null,
    currentOp: "",
    method:"Post",

    connect: function (op) {
        switch (op) {
            case "openUserDetails":
                users.method="GET";
                users.Route="/admin/user/openUserDetails";
                users.AjaxCall(op);
                break;
        }
    },
    AjaxCall: function (op) {
        $.ajax({
            url: users.Route,
            data: users.data,
            type: users.method,
            dataType: 'JSON',
            headers:{'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},


            // async: false,
            // processData:false,

            success: function (result) {
                switch (op) {
                    case"openUserDetails":

                        $("#user_details").html(result.html);
                        $("#user_details").modal("show");
                }
            },
            error: function (data) {
                var errors=data.responseJSON;
                switch (op){
                    case "openUserDetails":
                        $.each(errors, function(k, v) {
                            switch (k) {
                            }
                        })
                        break;
                }

            }

        });
    },
    openUserDetails:function (user_id) {
        users.data="user_id="+user_id;
        users.connect("openUserDetails");
    }


}
$(document).ready(function(){
    /* $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
     });*/


})


