/* globals is_gigya_user_logged_in:false, get_gigya_user_field:false */
(function($) {
	var $document = $(document), container, gridContainer;

	var gridUpdateRating = function($item, delta) {
		var rating = parseInt($item.text().replace(/\D+/g, ''));

		if (isNaN(rating)) {
			rating = 0;
		}

		rating += delta;
		rating = rating.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').replace(/\..*/g, '');

		$item.text(rating);
	};

	var gridPreviewLoaded = function(submission) {
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

		$document.trigger('contest:preview-loaded');
	};

	var gridLoadMoreUrl = function(page) {
		return container.data('infinite') + (page + 1) + '/';
	};

	var __ready = function() {
		container = $('#contest-form');
		gridContainer = $('.contest__submissions--list');

		$document.on('submit', '#contest-form form', function() {
			var form = $(this),
				iframe, iframe_onload;

			if (!form.parsley || form.parsley().isValid()) {
				form.find('input, textarea, select, button').attr('readonly', 'readonly');
				form.find('i.fa').show();

				iframe_onload = function() {
					var iframe_document = iframe.contentDocument || iframe.contentWindow.document,
						iframe_body = iframe_document.getElementsByTagName('body')[0],
						scroll_to = container.offset().top - $('#wpadminbar').height() - 10;

					container.html(iframe_body.innerHTML);
					$('html, body').animate({scrollTop: scroll_to}, 200);
				};

				iframe = document.getElementById('theiframe');
				if (iframe.addEventListener) {
					iframe.addEventListener('load', iframe_onload, false);
				} else if (iframe.attachEvent) {
					iframe.attachEvent('onload', iframe_onload);
				}

				return true;
			}

			return false;
		});

		var showRestriction = function(restriction) {
			var $restrictions = $('.contest__restrictions');

			$restrictions.attr('class', 'contest__restrictions');
			if (restriction) {
				$restrictions.addClass(restriction);
			}
		};

		var loadContainerState = function(url) {
			$.get(url, function(response) {
				var restriction = null;

				if (response.success) {
					container.html(response.data.html);
					loadUserContestMeta(response.data.contest_id);

					$('#contest-form form').parsley();

					$('.type-contest.collapsed').removeClass('collapsed');
				} else {
					restriction = response.data.restriction;
				}

				showRestriction(restriction);
			});
		};

		var loadUserContestMeta = function(contestID) {
			if (is_gigya_user_logged_in()) {
				get_gigya_profile_fields(['email', 'dateOfBirth'])
					.then(didLoadUserContestMeta);
			} else {
				var $form = $('.contest__form--user-info');
				$form.css('display', 'block');
			}
		};

		var didLoadUserContestMeta = function(response) {
			if (response.success) {
				showUserContestMeta(response.data);
			}
		};

		var showUserContestMeta = function(fields) {
			var userTemplate = '<span class="meta-title">Entry Details</span>' +
				'<a href="<%- editProfileUrl %>">Edit Your Profile</a>' +
				'<p class="meta-subtitle">This information is required for every entry.</p>' +
				'<dl>' +
				'<dt>Name: </dt>' +
				'<dd><%- firstName %> <%- lastName %></dd>' +
				'<dt>Email Address:</dt>' +
				'<dd><%- email %></dd>' +
				'<dt>Date of Birth: </dt>' +
				'<dd><%- dateOfBirth %></dd>' +
				'<dt>Zip: </dt>' +
				'<dd><%- zip %></dd>' +
				'</dl>';

			var data = {
				editProfileUrl : gigya_profile_path('account'),
				loginUrl       : gigya_profile_path('login'),
				firstName      : get_gigya_user_field('firstName'),
				lastName       : get_gigya_user_field('lastName'),
				email          : fields.email || 'N/A',
				age            : get_gigya_user_field('age'),
				dateOfBirth    : fields.dateOfBirth || 'N/A',
				zip            : get_gigya_user_field('zip'),
			};

			var template     = _.template(userTemplate);
			var html         = template(data);
			var $box        = $('.contest__form--user-info .user-info-box');

			$box.html(html);
			$box.css('display', 'block');

			var $userInfo = $('.contest__form--user-info');
			$userInfo.css('display', 'block');
		};

		$('.contest__restriction--min-age-yes').click(function() {
			loadContainerState(container.data('confirm-age'));
			return false;
		});

		$('.contest__restriction--min-age-no').click(function() {
			showRestriction('age-fails');
			return false;
		});

		if (container.length > 0) {
			loadContainerState(container.data('load'));
		}

		if (gridContainer.length > 0) {
			gridContainer.grid({
				loadMore: '.contest__submissions--load-more',
				previewLoaded: gridPreviewLoaded,
				loadMoreUrl: gridLoadMoreUrl
			});
		}
	};

	$document.bind('pjax:end', __ready).ready(__ready);
})(jQuery);
