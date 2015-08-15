jQuery(document).ready(function($) {

	var gselector = '#genesis_'+$('.loop_selector').val()+'_loop_settings';

	$('#genesis_standard_loop_settings').hide();
	$('#genesis_grid_loop_settings').hide();
	$(gselector).show();
    
    $('.loop_selector').change(function() {
        $('#genesis_standard_loop_settings').hide();
        $('#genesis_grid_loop_settings').hide();

        var gselector = '#genesis_'+$('.loop_selector').val()+'_loop_settings';
        $(gselector).slideDown();

    });
	
	//Hide div w/id extra
   if ($(".post_info_selector").is(":checked")){ $(".post_info").css("display","none"); }
   if ($(".post_meta_selector").is(":checked")){ $(".post_meta").css("display","none"); }
   if ($(".genesis_featured_image_selector").is(":checked")){ } else { $("#genesis_image_size").css("display","none"); }

	// Add onclick handler to checkbox w/id checkme
   $(".post_info_selector, .post_meta_selector, .genesis_featured_image_selector").click(function(){

		// If checked
		if ($(".post_info_selector").is(":checked")){
			//hide it
			$(".post_info").hide("fast");
		}
		else{
			//otherwise, show it
			$(".post_info").show("fast");
		}
		
		// If checked
		if ($(".post_meta_selector").is(":checked")){
			//hide it
			$(".post_meta").hide("fast");
		}
		else{
			//otherwise, show it
			$(".post_meta").show("fast");
		}
		
		if ($(".genesis_featured_image_selector").is(":checked")){
			$("#genesis_image_size").show("fast");
		}
		else{
			$("#genesis_image_size").hide("fast");
		}
	});
})

