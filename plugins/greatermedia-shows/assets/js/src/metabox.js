(function ($) {
	$(document).ready(function () {
		$('.mis-pub-radio').each(function() {
			var $this = $(this),
				$switchSelect = $this.find('.radio-select'),
				$editLink = $this.find('.edit-radio'),
				origin_value = $switchSelect.find('input:radio:checked').val();

			$editLink.click(function() {
				if ($switchSelect.is(':hidden')) {
					$switchSelect.slideDown('fast').find('input[type="radio"]').first().focus();
					$(this).hide();
				}
				return false;
			});

			$switchSelect.find('.cancel-radio').click(function() {
				$switchSelect.slideUp('fast', function() {
					$editLink.show().focus();

					$switchSelect.find('input:radio').each(function() {
						$(this).prop('checked', $(this).val() === origin_value);
					});
				});

				return false;
			});

			$switchSelect.find('.save-radio').click(function() {
				var selected = $switchSelect.find('input:radio:checked');

				$switchSelect.slideUp('fast', function() {
					$editLink.show();

					origin_value = selected.val();
					$this.find('.radio-value').text(selected.parent().text());
				});

				return false;
			});
		});
	});
})(jQuery);