$('.converttime').each(function(i, obj) {
    $(obj).text(moment($(obj).html()).fromNow())
});