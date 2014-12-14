(function ($) {
	$(document).ready(function () {
		var hovered_show, original_color,
			popup_tmpl = $('#schedule-remove-popup').html();
			
		$('#schedule-table td > div').hover(function() {
			var $this = $(this);

			hovered_show = '.' + $this.attr('class');

			original_color = $this.css('background-color');
			$(hovered_show).css('background-color', $this.attr('data-hover-color'));
		}, function() {
			$(hovered_show).css('background-color', original_color);
		});

		$('.remove-show').click(function () {
			var link = $(this).attr('href');
			
			$('body').append(popup_tmpl.replace(/{url}/g, function() {
				return link;
			}));
			
			return false;
		});
		
		$('body').on('click', '.popup-wrapper .button-cancel', function() {
			$(this).parents('.popup-wrapper').remove();
			return false;
		});
	});
})(jQuery);