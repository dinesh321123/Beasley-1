/* globals GMR_Gallery:false, is_gigya_user_logged_in:false, get_gigya_user_field:false */
(function($, gmr) {
	var __ready, gridPreviewLoaded, gridLoadMoreUrl, gridUpdateRating, container, gridContainer, fillForm;

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
			$previewInner.find('.cycle-slideshow').cycle();
		}

		// bind vote click event
		$previewInner.find('.contest__submission--vote').click(function() {
			var $this = $(this),
				$icon = $this.find('i.fa'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'fa fa-spinner fa-spin');

				$.post(container.data('vote'), {ugc: $this.data('id')}, function(response) {
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

				$.post(container.data('unvote'), {ugc: $this.data('id')}, function(response) {
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

		$(document).trigger('contest:preview-loaded');
	};

	gridLoadMoreUrl = function(page) {
		return container.data('infinite') + (page + 1) + '/';
	};

	fillForm = function() {
		if ($.isFunction(is_gigya_user_logged_in) && $.isFunction(get_gigya_user_field) && is_gigya_user_logged_in()) {
			container.find(gmr.selectors.form).each(function() {
				var $form = $(this),
					firstName = get_gigya_user_field('firstName'),
					lastName = get_gigya_user_field('lastName');

				$form.find('input[type="text"]:first').val(firstName + ' ' + lastName);
			});
		}
	};

	__ready = function() {
		container = $(gmr.selectors.container);
		gridContainer = $(gmr.selectors.grid);

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
					url: container.data('submit'),
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
			container.load(container.data('confirm-age'), fillForm);
			return false;
		});
		
		container.on('click', gmr.selectors.no_age, function() {
			container.load(container.data('reject-age'));
			return false;
		});

		if (container.length > 0) {
			container.load(container.data('load'), fillForm);
		}

		if (gridContainer.length > 0) {
			gridContainer.grid({
				loadMore: gmr.selectors.grid_more,
				previewLoaded: gridPreviewLoaded,
				loadMoreUrl: gridLoadMoreUrl
			});
		}
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery, GreaterMediaContests);
