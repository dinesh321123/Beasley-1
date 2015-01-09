(function ($) {
	var __ready = function () {
		var container = $('#survey-form');

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
					$('.type-survey.collapsed').removeClass('collapsed');
				} else {
					restriction = response.data.restriction;
				}

				showRestriction(restriction);
			});
		};
		
		container.on('submit', 'form', function() {
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
		
		if (container.length > 0) {
			loadContainerState(container.data('load'));
		}
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery);