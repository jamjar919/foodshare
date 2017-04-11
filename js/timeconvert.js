function convertTimes() {
    $('.converttime').each(function(i, obj) {
        $(obj).text(moment($(obj).html()).fromNow())
        $(obj).removeClass("converttime");
    });
};

convertTimes();