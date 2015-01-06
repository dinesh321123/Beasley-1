/* globals GMR_Gallery:false */
(function($, gmr) {
	var __ready, gridPreviewLoaded, gridLoadMoreUrl, gridUpdateRating;

	gridUpdateRating = function($item, delta) {
		var rating = parseInt($item.text().replace(/\D+/g, ''));

		if (isNaN(rating)) {
			rating = 0;
		}

		rating += delta;
		rating = rating.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(/\..*/g, '');

		$item.text(rating);
	};

	gridPreviewLoaded = function(submission) {
		var $previewInner = submission.$previewInner,
			$item = submission.$item,
			$rating = $item.find('.contest__submission--rating b'),
			sync_vote = false;

		// init new gallery
		if ($.fn.cycle) {
			$previewInner.find('.cycle-slideshow').cycle({
				next: '.gallery__next--btn'
			});
		}

		// bind gallery events
		if (GMR_Gallery) {
			GMR_Gallery.bindEvents();
		}

		// bind vote click event
		$previewInner.find('.contest__submission--vote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.fa'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'fa fa-spinner fa-spin');

				$.post(gmr.endpoints.vote, {ugc: $this.data('id')}, function(response) {
					sync_vote = false;
					$icon.attr('class', classes);

					if (response.success) {
						$item.addClass('voted');
						gridUpdateRating($rating, 1);
					}
				});
			}

			return false;
		});

		// bind unvote click event
		$previewInner.find('.contest__submission--unvote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.fa'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'fa fa-spinner fa-spin');

				$.post(gmr.endpoints.unvote, {ugc: $this.data('id')}, function(response) {
					sync_vote = false;
					$icon.attr('class', classes);
					
					if (response.success) {
						$item.removeClass('voted');
						gridUpdateRating($rating, -1);
					}
				});
			}

			return false;
		});
	};

	gridLoadMoreUrl = function(page) {
		return gmr.endpoints.infinite + (page + 1) + '/';
	};

	__ready = function() {
		var container = $(gmr.selectors.container);

		container.on('submit', gmr.selectors.form, function() {
			var form = $(this);

			if (!form.parsley || form.parsley().isValid()) {
				var form_data = new FormData();
				
				form.find('input').each(function() {
					var input = this;

					if ('file' === input.type) {
						$(this.files).each(function(key, value) {
							form_data.append(input.name, value);
						});
					} else if ('radio' === input.type || 'checkbox' === input.type) {
						if (input.checked) {
							form_data.append(input.name, input.value);
						}
					} else {
						form_data.append(input.name, input.value);
					}
				});

				form.find('textarea, select').each(function() {
					form_data.append(this.name, this.value);
				});

				form.find('input, textarea, select, button').attr('disabled', 'disabled');
				form.find('i.fa').show();

				$.ajax({
					url: gmr.endpoints.submit,
					type: 'post',
					data: form_data,
					processData: false, // Don't process the files
					contentType: false, // Set content type to false as jQuery will tell the server its a query string request
					success: function(data) {
						container.html(data);
					}
				});
			}

			return false;
		});

		container.on('click', gmr.selectors.yes_age, function() {
			container.load(gmr.endpoints.confirm_age);
			return false;
		});
		
		container.on('click', gmr.selectors.no_age, function() {
			container.load(gmr.endpoints.reject_age);
			return false;
		});

		container.load(gmr.endpoints.load);

		$('.contest__submissions--list').grid({
			loadMore: '.contest__submissions--load-more',
			previewLoaded: gridPreviewLoaded,
			loadMoreUrl: gridLoadMoreUrl
		});
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery, GreaterMediaContests);