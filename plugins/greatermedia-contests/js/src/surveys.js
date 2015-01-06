(function ($, gmr) {
	var __ready = function () {
		$('.' + gmr.form_class).submit(function() {
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
					url: gmr.ajax_url,
					type: 'post',
					data: form_data,
					processData: false, // Don't process the files
					contentType: false, // Set content type to false as jQuery will tell the server its a query string request
					dataType: 'json',
					success: function (data, textStatus, jqXHR) {
						if ('success' === textStatus && data.data.message) {
							var wrapper = document.createElement('p');
							wrapper.class = 'survey_thank_you';
							wrapper.innerText = data.data.message;
							form.replaceWith(wrapper);
						}
					}
				});
			}

			return false;
		});
	};

	$(document).bind('pjax:end', __ready).ready(__ready);
})(jQuery, GreaterMediaSurveys);