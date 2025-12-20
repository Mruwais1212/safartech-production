$(document).on("click",".cancel-add",function(e){
    $(this).parent().remove();
    var attr=$(this).attr("itemid");
    // alert(attr);

});
$(document).on("change",".feature_item",function(e) {
    var max_price = $('option:selected', this).attr('max_price');
    var min_price = $('option:selected', this).attr('min_price');

    $(this).closest('.new_feature').find('.price_input').attr({
        "max" : max_price,        // substitute your own
        "min" : min_price          // values (or variables) here
    });
    if($(this).val() !=""){
        $(this).closest('.new_feature').find('.price_note').text(' السعر لابد ان يكون بين ('+min_price+'-'+max_price+')');
    }
    else {
        $(this).closest('.new_feature').find('.price_note').text('قم باختيار الاضافة اولا');

    }
});

