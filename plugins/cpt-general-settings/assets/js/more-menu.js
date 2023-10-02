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

					$(".sub_menu").html(itemsToCopy);
					$(".sub_menu .cnavigation-more").remove();
					$navigation.append(moreButtonContent);

					var desktop_sumWidth = 0;
					var ul_width = $('.top_header.desktop .cnavigation li:not(.sub_menu li)');
					ul_width.each(function () {
						desktop_sumWidth += $(this).width() + 10;
					});
					
					ul_width.parents('.cnavigation').find('.sub_menu').width(desktop_sumWidth - 10);
					mobile_ul_width.parents('.cnavigation').find('.sub_menu').width(sumWidth);
					
					$(".desktop .cnavigation li").css('display','block');
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
			// adjustMenuItems();
			$(".desktop .cnavigation").append(moreButtonContent);
			$(".desktop .cnavigation").data("items-to-copy", adjustMenuItems());
			// $(".desktop .cnavigation").data("items-to-copy", 3);
			// Start hiding items from the 4th item onwards
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
		
		function adjustMenuItems() {
			document.querySelector('.desktop .cnavigation').style.display = 'none';
			var main_containerWidth = document.querySelector('.desktop #slimmer-mobile-navigation').offsetWidth;
			var navigation_logo_width = document.querySelector('.desktop #slimmer-mobile-navigation .mobile-navigation-logo').offsetWidth;
			var title_width = document.querySelector('.desktop .slimmer-navigation-desktop-container .title_description').offsetWidth;
			var containerWidth = main_containerWidth - navigation_logo_width;
			var ul_width = containerWidth - title_width;

			const menu = document.querySelector('.desktop .slimmer-navigation-desktop-container');
			document.querySelector('.desktop .cnavigation').style.display = 'flex';
			document.querySelector('.desktop .cnavigation').style.width = ul_width+'px';
			const items_count = menu.querySelectorAll('li:not(.cnavigation-more)').length;
			const items = menu.querySelectorAll('li');
			const moreItem = menu.querySelector('.cnavigation-more');


			let totalWidth = 0;
			let itemsToDisplay = -1;
		
			// Calculate the total width of visible items
			items.forEach((item) => {
				totalWidth += item.offsetWidth;

			  	if (totalWidth <= ul_width) {
					itemsToDisplay++;
			  	}
			});

			if(items_count > 3){
				itemsToDisplay--;
			}

			for (let i = itemsToDisplay; i < items.length; i++) {
				items[i].style.display = 'none';
			}

			if(items_count > 3){
				if (items.length > itemsToDisplay) {
					moreItem.style.display = 'inline';
				} 
			}
			
			return itemsToDisplay;

		}

	});

})(jQuery);
  