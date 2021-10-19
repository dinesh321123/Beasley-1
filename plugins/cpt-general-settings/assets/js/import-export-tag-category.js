(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$(".ietc-delete-confirm").on("click", function() {
			var x = confirm("Are you sure you want to delete?");
			if (x)
			return true;
			else
			return false;
		});

		$('.general-settings-select2').select2({
			width: '300px',
		});

		$("#import_file").on("click", function() {
			$( '#tp_spinner' ).addClass( 'is-active' );
			$('.import-result').html('');
			$('.show_import_log').html('');

			var file = $('.file').val();
			var site_id = $('.site_id').val();
			var list_id = $('.list_id').val();
			var taxonomy_type = $('.taxonomy_type').val();

			$.ajax({
				    type: "POST",
				    dataType: 'json',
				    url: my_ajax_object.ajax_url,
				   	data : {
				   		action: "ietc_import_data", 
				   		file: file,	
				   		site_id: site_id,
				   		list_id: list_id,
				   		taxonomy_type: taxonomy_type,				   		 
				   	},
				    success: function(result){
				    	console.log(result);
				    	if(result.success == true){
				    		console.log(result.success);
				    		var data = result.data;
							var log_data = data.log_data;
							
					        $('.import-result').append('<div class="notice notice-success is-dismissible"><p>'+data.message+'</p></div></br>');
					        $.each(log_data, function (i) {
					        	var html = '';
							    current_row = log_data[i];
							    html = i +'. Type: '+current_row.type+' Name: '+current_row.name+' Status: '+current_row.message+'</br>';
							    $('.show_import_log').append(html);
							});
					   		$( '#tp_spinner' ).removeClass( 'is-active' ); 	
				    	}
				    	else{
				    		$('.import-result').append('<div class="error notice-error is-dismissible"><p>'+result.data+'<p></div></br>');
						}
					}
				});
			});
		$("#export_file").on("click", function() {
			$('.export-result').html('');
					        
			var site_id = $('.site_id').val();
			var list_id = $('.list_id').val();

			$.ajax({
			    type: "POST",
			    dataType: 'json',
			    url: my_ajax_object.ajax_url,
			   	data : {
			   		action: "ietc_export_data", 
			   		site_id: site_id,	
			   		list_id: list_id,		   					   		 
			   	},
			    success: function(result){
			    	console.log(result);
			        	if(result.success == true){
				    		console.log(result.success);
				    		var data = result.data;
				    		var log_data = data.log_data;
				        
					        $('.export-result').append('<div class="notice notice-success is-dismissible"><p>'+data.message+'<p></div></br>');
					        window.location.href = data.file_path;
				    	}

			    }
			});
		});
		
	$("#import_tag_category").on("click", function() {
		$('#import_msg').html('');
		var network_source 	= $('#network_source').val();
		var network_type	= $('#network_type').val();
		var network_name 	= $("#network_source  option:selected").text();
		// File upload code
		var fileName	= "";
		const fdata		= new FormData();
		
		/* File upload code*/
		let fileInputElement = document.getElementById('csv_file');
		fileName = fileInputElement.files.length ? fileInputElement.files[0].name : '' ;
		
		if(!network_source){
			alert('Select network source');
			return;
		}
		if(!network_type){
			alert('Select type');
			return;
		}

		if( fileName == "" ) {
			alert( 'Please select CSV file' );
			return;
		}

		$( '#import_tag_category_spinner' ).addClass( 'is-active' );
		fdata.append( 'action', 'ietc_import_tag_category' );
		fdata.append( 'network_source', network_source );
		fdata.append( 'network_type', network_type );
		fdata.append( 'network_name', network_name );

		fdata.append( 'csv_file', fileInputElement.files[0], fileInputElement.files[0].name );
		
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: my_ajax_object.ajax_url,
			data : fdata,
			contentType: false,
			processData: false,
			success: function(result){
				// console.log(result);
					if(result.success == true){
						console.log(result.success);
						var data = result.data;
						$('#import_msg').append('<div class="notice notice-success is-dismissible"><p>'+data.message+'</p></div> </br> <span id="timestamp" class="timestamp"><a href ="' + data.log_file_path + '" download> Download log </a> </span>');
						$( '#import_tag_category_spinner' ).removeClass( 'is-active' );
					} else {
						$('#import_msg').prev().append('<div id="errormsg"><p class="error">Error in result. Please reload the page.</p></div>');
						$( '#import_tag_category_spinner' ).removeClass( 'is-active' );
					}
			},
			error : function(r) {
				$('#import_msg').prev().append('<div id="errormsg"><p class="error">There was an error. Please reload the page.</p></div>');
				$('#import_tag_category_spinner').removeClass('is-active');
				// removeErrorAfterSomeTime();
			}
		});

		
	});
	$("#export_tag_category").on("click", function() {
		$('#export_msg').html('');
		$( '#export_tag_category_spinner' ).addClass( 'is-active' );
		var network_source 	= $('#network_source').val();
		var network_type	= $('#network_type').val();
		var network_name 	= $("#network_source  option:selected").text();

		// alert(network_name);
		
		if(!network_source){
			alert('Select network source');
			$( '#export_tag_category_spinner' ).removeClass( 'is-active' );
			return;
		}
		if(!network_type){
			alert('Select type');
			$( '#export_tag_category_spinner' ).removeClass( 'is-active' );
			return;
		}

		$.ajax({
			type: "POST",
			dataType: 'json',
			url: my_ajax_object.ajax_url,
			data : {
				action: "ietc_export_tag_category", 
				network_source: network_source,
				network_type: network_type,
				network_name: network_name,
			},
			success: function(result){
				console.log(result);
					if(result.success == true){
						console.log(result.success);
						var data = result.data;
						$('#export_msg').append('<div class="notice notice-success is-dismissible"><p>'+data.message+'</p></div> </br> <span id="timestamp" class="timestamp"><a href ="' + data.file_path + '" download> Download file manually </a> </span>');
						$( '#export_tag_category_spinner' ).removeClass( 'is-active' );
						window.location.href = data.file_path;
					} else {
						$('#export_msg').prev().append('<div id="errormsg"><p class="error">Error in result. Please reload the page.</p></div>');
						$( '#export_tag_category_spinner' ).removeClass( 'is-active' );
					}
			},
			error : function(r) {
				$('#export_msg').prev().append('<div id="errormsg"><p class="error">There was an error. Please reload the page.</p></div>');
				$('#export_tag_category_spinner').removeClass('is-active');
				// removeErrorAfterSomeTime();
			}
		});
	});
	
  	$('.userfiltercls').change(function(){
    	var getUserid=$(this).val();
    	var getTypeid=$('.typefiltercls').val();
    	var getNetWorkid=$('.networksourcecls').val();

	    window.location.replace($('.wpBaseurl').val()+"/wp-admin/network/admin.php?page=ietc_logs&filtuserid="+getUserid+"&filttypeid="+getTypeid+"&filtnetworkid="+getNetWorkid);
	});

    $('.typefiltercls').change(function(){
    	var getUserid=$('.userfiltercls').val();
    	var getTypeid=$(this).val();
    	var getNetWorkid=$('.networksourcecls').val();
	
		window.location.replace($('.wpBaseurl').val()+"/wp-admin/network/admin.php?page=ietc_logs&filtuserid="+getUserid+"&filttypeid="+getTypeid+"&filtnetworkid="+getNetWorkid);
		
    });

    $('.networksourcecls').change(function(){
    	var getUserid=$('.userfiltercls').val();
    	var getTypeid=$('.typefiltercls').val();
    	var getNetWorkid=$(this).val();
	
		window.location.replace($('.wpBaseurl').val()+"/wp-admin/network/admin.php?page=ietc_logs&filtuserid="+getUserid+"&filttypeid="+getTypeid+"&filtnetworkid="+getNetWorkid);
		
    });

	});
	function removeErrorAfterSomeTime(){
		// alert("Remove function called");
		setTimeout(function(){
			if ($('#errormsg').length > 0) {
			  $('#errormsg').remove();
			}
		  }, 10000)
	}
})(jQuery);
