(function ($) {
	$(document).ready(function () {
	  	$('[class*="_shows-"]').addClass("custom-margin");
	  	var width = $(window).width();
	  	var moreButtonContent = '<li class="cnavigation-more"> More </li>';
	  	function handleNavigationClick() {
			
			var $navigation = $(this).parents('.cnavigation');
			var clickState = $navigation.attr("data-click-state");
		
			if (clickState == 1) {
		  		$navigation.attr("data-click-state", 0).addClass("dropdown-active");
		  		var $subMenu = $(".sub_menu");
		  
		  		if ($subMenu.length === 0) {
					$navigation.append("<span class='bg_overlay'></span><ul class='sub_menu'></ul>");
					var sourceItems = $(".cnavigation li");
					var itemsToCopy = sourceItems.slice($navigation.data("items-to-copy"));
					var mobile_ul_width = $('.top_mobile_header .cnavigation').find('li:lt(5)');
					var sumWidth = 0;
					mobile_ul_width.each(function () {
						sumWidth += $(this).width();
					});

					var ul_width = $('.top_header .cnavigation');

					$(".sub_menu").html(itemsToCopy);
					$(".sub_menu .cnavigation-more").remove();
					$navigation.append(moreButtonContent);
					ul_width.find('.sub_menu').width(ul_width.width());
					mobile_ul_width.parents('.cnavigation').find('.sub_menu').width(sumWidth);
					$(".desktop .cnavigation .bg_overlay").hide();
		  		} else {
					$subMenu.show();
					$(".bg_overlay").show();
					$(".desktop .cnavigation .bg_overlay").hide();
		  		}
			} else {
				$navigation.attr("data-click-state", 1).removeClass("dropdown-active");
				$(".sub_menu, .bg_overlay").hide();
			}
	  	}
		
		if (width > 992) {
			$(".top_header").addClass('desktop');
			$(".top_mobile_header").empty();
			$(".desktop .cnavigation").data("items-to-copy", 3);
			$(".desktop .cnavigation").append(moreButtonContent);
			$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClick);
		}

	  	if (width <= 992 && width > 768) {
		
			$(".top_mobile_header").empty();
			$(".cnavigation").data("items-to-copy", 7);
			$(".cnavigation").append(moreButtonContent);
			$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClick);

	  	} else if (width <= 768 && width > 480) {
		
			$(".top_mobile_header").empty();
			$(".cnavigation").data("items-to-copy", 4);
			$(".cnavigation").append(moreButtonContent);
			$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClick);
	  
		} else if (width <= 480) {
	
			$(".article-inner-container .cnavigation").empty();
			$(".show .cnavigation").empty();
			$(".cnavigation").data("items-to-copy", 3);
			$(".cnavigation").append(moreButtonContent);
			$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClick);
	
		} else {
			var targetElement = $(".cnavigation");
			targetElement.toggleClass("no-pseudo");
	  	}
	});

})(jQuery);
  