(function($, location, document) {
	var $document = $(document),
		classes = {},
		last_url = null,
		current_url = location.href,
		normalize_url,
		siteWrap = $('#site-wrap');

	normalize_url = function(url) {
		return url.replace(/[\?\#].*$/g, '');
	};

	$document.bind('pjax:popstate', function() {
		last_url = normalize_url(current_url);
	});

	$document.bind('pjax:beforeSend', function() {
		last_url = normalize_url(location.href);
	});

	$document.bind('pjax:end', function(e, xhr, options) {
		var $body = $('body'),
			body_classes = false,
			pattern = new RegExp('\<body.*?class=\"(.*?)\"', 'im');

		classes[last_url] = $body.attr('class');

		if (xhr) {
			body_classes = pattern.exec(xhr.responseText);
			if (body_classes && body_classes.length >= 2) {
				$body.attr('class', body_classes[1]);
			}
		} else {
			$body.attr('class', classes[normalize_url(options.url)]);
		}

		current_url = location.href;
	});

	/**
	 * Add "is-busy" class to the body when a Pjax request starts.
	 */
	$document.bind( 'pjax:start', function () {
		$( 'body').addClass( 'is-busy' );
	} );

	$document.bind( 'pjax:click', function () {
		$( 'body').addClass( 'pjax-start' );
	} );

	/**
	 * Remove the "is-busy" class from the body when a Pjax request ends.
	 */
	$document.bind( 'pjax:end', function () {
		$( 'body').removeClass( 'is-busy' );
	} );

	/**
	 * Adds `pjax--active` class to the `#site-wrap` element when a Pjax request starts. This class can be used for
	 * visual display when Pjax is active.
	 */
	$document.bind('pjax:start', function() {
		siteWrap.addClass('pjax--active');
	});
})(jQuery, location, document);