jQuery(document).ready(function($){

	$(document).on('click', '#add_new_script_file', function(){
		var label_html = $('#script_files_container').find('.single_file:first-child').html();
		$('#script_files_container').append('<div class="single_file">' + label_html + '<input type="button" class="button button-default delete_script_file" name="delete_script_file[]" id="" value="Delete" /></div>');
	});

	$(document).on('click', '.delete_script_file', function(){
		$(this).parent('.single_file').remove();
	});

	$('#submit_image_btn').on('click', function(e){
		e.preventDefault();
		$('#upload_script_files_form').submit();
	});

	$('#upload_script_files_form').on('submit', function(e){
		e.preventDefault();
		var formdata = new FormData(this);
		formdata.append('action', 'upload_script_files');

		$.ajax({
		    url: wp_script_loader_script_js.admin_ajax_url,
		    type: 'POST',
		    dataType: 'JSON',
		    data: formdata,
		    processData: false,
		    cache: false,
		    contentType: false,
		    success: function(r){
		    	// alert(r);
		    	window.location.reload(true);
		    },
		    error: function(jqXHR, textStatus, errorThrown){
		    	// alert(errorThrown);
		    }
		});
	});
});



jQuery(document).ready(function($){
	$('.use_in_back').on('change', function(){
		var use_in_back = 0;
		if($(this).is(':checked')){
			use_in_back = 1;
		}
		var data_id = $(this).attr('data-id');
		// alert(data_id);
		$.ajax({
		    url: wp_script_loader_script_js.admin_ajax_url,
		    type: 'POST',
		    dataType: 'JSON',
		    data: {'action': 'use_in_back', 'use_in_back': use_in_back, 'data_id': data_id},
		    success: function(r){
		    	// alert(r);
		    	// window.location.reload(true);
		    },
		    error: function(jqXHR, textStatus, errorThrown){
		    	// alert(errorThrown);
		    }
		});
	});

	$('.use_in_front').on('change', function(){
		var use_in_front = 0;
		if($(this).is(':checked')){
			use_in_front = 1;
		}
		var data_id = $(this).attr('data-id');
		// alert(data_id);
		$.ajax({
		    url: wp_script_loader_script_js.admin_ajax_url,
		    type: 'POST',
		    dataType: 'JSON',
		    data: {'action': 'use_in_front', 'use_in_front': use_in_front, 'data_id': data_id},
		    success: function(r){
		    	// alert(r);
		    	// window.location.reload(true);
		    },
		    error: function(jqXHR, textStatus, errorThrown){
		    	// alert(errorThrown);
		    }
		});
	});

	$('.delete_script_file').on('click', function(){
		var data_id = $(this).attr('data-id');
		// alert(data_id);
		if(confirm('Are you sure want to delete this script file...')){
			$.ajax({
			    url: wp_script_loader_script_js.admin_ajax_url,
			    type: 'POST',
			    dataType: 'JSON',
			    data: {'action': 'delete_script_file', 'data_id': data_id},
			    success: function(r){
			    	// alert(r);
			    	window.location.reload(true);
			    },
			    error: function(jqXHR, textStatus, errorThrown){
			    	// alert(errorThrown);
			    }
			});
		}
	});
});