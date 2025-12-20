/**
 * Created by ABDOOOOO on 29/5/2016.
 */

var menu = {
    Route: "",
    data: null,
    currentOp: "",
    method:"Post",

    connect: function (op) {
        switch (op) {
            case "saveMenu":
                menu.method = "post";
                var _token =  $('[name=_token]').val();
                var menuName=$("input[name='menu_name']").val();
                var menuId=$("input[name='menu_id']").val();
                var menuItems=$('.dd').nestable('serialize');
                menuItems=JSON.stringify(menuItems);
               menu.data = "_token=" + _token+"&menu_name=" + menuName + "&items=" + menuItems+"&menu_id="+menuId ;
                menu.Route = "/admin/addAllItems";
                menu.AjaxCall(op);
                break;
            case "deleteItem":
                menu.method="POST";
                menu.Route="/admin/deleteMenuItem";
                menu.AjaxCall(op);
                break;
        }
    },
    AjaxCall: function (op) {
        $.ajax({
            url: menu.Route,
            data: menu.data,
            type: 'POST',
            dataType: 'JSON',
            headers:{'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},


            // async: false,
            // processData:false,

            success: function (result) {
                switch (op) {
                    case "saveMenu":
                        console.log(result);
                        if (result == "1") {
                            notify.initialization("تم التعديل على القائمة بنجاح  .", "success");
                        }
                        break;
                    case"deleteItem":

                        $("#nestable3").html(result.html);
                        var options="<option value=''>اختر القائمة الاب</option>";
                        for(var i=0;i<result.items.length;i++){
                    options+="<option value='"+result.items[i].id+"'>"+result.items[i].name+"<option>";
                        }
$("#parent_item").html(options);
                        notify.initialization("تم الحذف بنجاح", "success");

                        break;

                }
            },
            error: function (data) {
                var errors=data.responseJSON;
                switch (op){
                    case "saveMenu":
                        $.each(errors, function(k, v) {
                            switch (k) {
                                case "menu_name":
                                    notify.initialization(v[0], "failed");
                                    break;
                            }
                        })
                        break;
                }

            }

        });
    }


}
$(document).ready(function(){
   /* $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });*/
    $(document).on("click",".delete-item",function(e){

       // $(".delete-item").click(function(){
        var itemId= $(this).attr("item_id");
        menu.data="item_id="+itemId;

        menu.connect("deleteItem");

    })

    $("#menu_form").submit(function(e){
        e.preventDefault();
        menu.connect("saveMenu");



    })


})


