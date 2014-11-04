/**
 * Created by eduardm on 04.11.2014.
 */

jQuery(function($) {
	var dialog, form,
	age = $( "#age" ),
	allFields = $( [] ).add( 'age' );

	function checkAge() {
		var data = {
			'action': 'check_age',
			'user_age': age.val()
		};

		jQuery.post(ajaxData.ajax_url, data, function(response) {

		});

		return true;
	}

	dialog = $( "#dialog-form" ).dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			"Enter": checkAge,
			Cancel: function() {
				dialog.dialog( "close" );
			}
		},
		close: function() {
			allFields.removeClass( "ui-state-error" );
		}
	});

	form = dialog.find( "form" ).on( "submit", function( event ) {
		event.preventDefault();
		addUser();
	});

	dialog.dialog( "open" );

});