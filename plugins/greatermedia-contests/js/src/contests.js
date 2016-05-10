/* globals is_gigya_user_logged_in:false, get_gigya_user_field:false */
/* globals get_gigya_profile_fields:false, gigya_profile_path:false */
/* globals _:false */
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
				$icon = $this.find('i.gmr-icon'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'gmr-icon icon-spin icon-spin');

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
				$icon = $this.find('i.gmr-icon'),
				classes = $icon.attr('class');

			if (!sync_vote) {
				sync_vote = true;
				$icon.attr('class', 'gmr-icon icon-spin icon-spin');

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
				form.find('i.gmr-icon').show();

				iframe_onload = function() {
					var iframe_document = iframe.contentDocument || iframe.contentWindow.document,
						iframe_body = iframe_document.getElementsByTagName('body')[0],
						scroll_to = container.offset().top - $('#wpadminbar').height() - 10;

					iframe_body = $.trim(iframe_body.innerHTML);
					if (iframe_body.length > 0) {
						container.html(iframe_body);
					} else {
						alert('Your submission failed. Please, enter required fields and try again.');
						form.find('input, textarea, select, button').removeAttr('readonly');
						form.find('i.gmr-icon').hide();
					}

					$('html, body').animate({scrollTop: scroll_to}, 200);
				};

				iframe = document.getElementById('theiframe');
				if (iframe.addEventListener) {
					iframe.addEventListener('load', iframe_onload, false);
					iframe.addEventListener('load', enableSubmitButton, false);
				} else if (iframe.attachEvent) {
					iframe.attachEvent('onload', iframe_onload);
					iframe.attachEvent('onload', enableSubmitButton);
				}

				busySubmitButton();

				return true;
			}

			return false;
		});

		var busySubmitButton = function() {
			var submitButton = $('#contest-form form button[type="submit"]');
			submitButton.attr('disabled', 'disabled');
			submitButton.addClass('disabled');
			submitButton.empty();
			$('<i>', {
			    class: 'gmr-icon icon-spinner icon-spin',
			    style: 'display: inline-block',
			}).appendTo(submitButton);
			submitButton.append(' Submitting...');
		};

		var enableSubmitButton = function() {
			var submitButton = $('#contest-form form button[type="submit"]');
			submitButton.removeAttr('disabled');
			submitButton.removeClass('disabled');
			submitButton.text('Submit');
		};

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

					$('#contest-form form').parsley();
					$('.type-contest.collapsed').removeClass('collapsed');
				} else {
					restriction = response.data.restriction;
				}

				showRestriction(restriction);

				if (response.data && response.data.contest_id) {
					loadUserContestMeta(response.data.contest_id);
				}
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
