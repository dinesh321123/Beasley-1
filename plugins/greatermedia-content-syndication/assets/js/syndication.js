(function ($) {
	$(document).ready(function () {
		var $filter_metaboxes = $( '#filter_metaboxes' );

		function get_subscription_loader(){
			return $( '<img>', { src: './images/spinner.gif', class : 'subscription_loader' } );
		}

		$(".subscription_defaults").select2({
			placeholder: "Select term"
		});

		// perform syndication
		$("#syndicate_now").on('click', function (event) {
			event.preventDefault();
			var post_id = $(this).data('postid');
			$.ajax({
				type: "post",
				url: syndication_ajax.ajaxurl,
				data: {action: "syndicate-now",
					syndication_id: post_id,
					syndication_nonce: syndication_ajax.syndication_nonce
				},
				beforeSend: function () {
					$('#syndication_status').html('Checking...');
				},
				success: function (response) {
					var total = response.total;

					if (Math.floor(total) !== 0) {
						$('#syndication_status').html('Imported ' + total + ' item(s). </br>Debugging ID ' + response.unique_id );
					} else {
						$('#syndication_status').html('No matches found! </br>Debugging ID ' + response.unique_id );
					}
				}
			});
		});

		function init_subscription_terms() {
			$( ".subscription_terms" ).select2( {
				placeholder: "Select term"
			} );
			$( '#filter_metaboxes input[type=radio]' ).off( 'click' ).on( "click", function () {
				$( '#filter_metaboxes input[type=radio]' ).each( function () {
					var el = '#' + $( this ).data( 'enabled' );
					if ( $( this ).prop( 'checked' ) ){
						$( el ).select2( 'enable', true );
						$( '#enabled_filter_taxonomy' ).val( $( this ).data( 'enabled' ) );
					} else {
						$( el ).select2( 'enable', false );
					}
				} );
			} );
		}

		init_subscription_terms();

		$( "select.subscription_source_select2" ).select2( { placeholder: "Select term" } ).on( "change", function ( e ) {
			$filter_metaboxes.html( get_subscription_loader() );
			wp.ajax.post( 'syndication_taxonomy_filters', {
				'security': syndication_ajax.syndication_filter_nonce,
				'site_id': $( '#subscription_source' ).val(),
				'post_id': $( '#post_ID' ).val(),
			} ).done( function ( response ) {
				$filter_metaboxes.html( response.content );
				init_subscription_terms();
			} ).fail( function ( response ) {

			} );
		} );

		var syndicated_posts = $('ul.syndicated_posts li');
		syndicated_posts.hide().slice(0, 2).show();

		$("#show_all_syndicated_posts").click(function (event) {
			event.preventDefault();
			syndicated_posts.show('600');
		});
	});
})(jQuery);

(function($){
	var $resetPostButton = $(document.getElementById( 'js-syndication-reset' ) );

	if ( $resetPostButton.length !== 0 ) {

		$resetPostButton.on( 'click', function( e ) {
			if ( ! window.confirm( "Are you sure you want to reset this post to the source site? You will lose any changes you may have made.") ) {
				return false;
			}
		});
	}
})(jQuery);
