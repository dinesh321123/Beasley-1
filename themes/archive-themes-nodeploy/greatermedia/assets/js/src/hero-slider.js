(function ($) {
	var __ready = function() {
		$('.home__featured_hero_slider .slideshow').each(function() {
			var $this = $(this);

			$('.featured__article--image', $this).width($this.width());
			$('.feature-post-slide', $this).show();

			$this.cycle({
				timeout: 5000,
				prev: '.slick-prev',
				next: '.slick-next',
				slides: '> .feature-post-slide',
				autoHeight: 'container',
				pager: '.slick-dots'
			});
		});
	};

	$(document).bind('pjax:end', __ready).ready(__ready);

	$(window).resize(_.debounce(function() {
		$('.home__featured_hero_slider .slideshow').each(function() {
			var $this = $(this);
			$('.featured__article--image', $this).width($this.width());
		});
	}, 200));
})(jQuery);