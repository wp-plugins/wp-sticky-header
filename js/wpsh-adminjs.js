jQuery(document).ready(function($){
	checkStatus();
	$(".display").change(function(){
		checkStatus();
	});
	$("#help").click(function(){
		$(".showhelp").fadeToggle("slow");
	});
});



function checkStatus(){
	var page = jQuery(".display:checked").val();
	if(page == "selected"){
		jQuery(".pageids").fadeIn();
	}
	else{
		jQuery(".pageids").fadeOut();
	}
}