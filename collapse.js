$(document).ready(function(){
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
});