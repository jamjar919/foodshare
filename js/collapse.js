function bindCollapse() {
    //hide all div containers
    $('#collapsible-panels div').hide();
    //apend click event to the a element
    $('#collapsible-panels a').click(function(e){
            //slide down the corresponding div if hidden, or slide up if shown
            $(this).parent().next('#collapsible-panels div').slideToggle('slow');
            //set the current item as active
            $(this).parent().toggleClass('active');
            e.preventDefault();
    });
    var width = $(window).width(); 
    if (width < 660) {
        $('.collapsible-panels-mob .panel-content').hide();
        //apend click event to the a element
        $('.collapsible-panels-mob .panel-control').click(function(e){
                // Flip icons
                $(this).find('.glyphicon-collapse-down').toggleClass("flipx");
                //slide down the corresponding div if hidden, or slide up if shown
                $(this).parent().children(".panel-content").slideToggle('slow');
                //set the current item as active
                $(this).parent().toggleClass('active');
        }); 
    }
}
$(document).ready(function(){
    bindCollapse();
});