( function( $ ) {
	requestFunction = (action, data) => new Promise((resolve, reject) => {
		const { wp } = window;
		wp.ajax.send(action, {
			type: 'GET',
			data,
			success: resolve,
			error: reject,
		});
	});
	$( document ).ready( function() {
		$('.clearCloudFlareCache').on('click', function(el){
			requestFunction('clear_cloudflare_cache',{ postID: $(this).attr('data-id') }).then((result) => {
					console.log(result)
				})
			.catch((error) => {
				console.log('Errors'+error);
			});

		});
	});

} )( jQuery );

