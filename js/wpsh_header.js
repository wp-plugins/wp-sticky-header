jQuery(document).ready(function($){
	$(".wpsh_fixed").detach().appendTo("body");
	$(".wpsh_close").click(function(){
		$(".wpsh_fixed").fadeOut("slow");
	});
});