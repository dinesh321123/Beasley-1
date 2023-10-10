(function ($) {
	$(document).ready(function () {

		var body = document.body;
		var headerContainer = document.querySelector('.show-header-container');

		if (headerContainer) {
			if (!body.classList.contains('slimmer-menu-react')) {

				$('[class*="_shows-"]').addClass("custom-margin");
				$("#top_header .cnavigation").attr("data-click-state", 1);
				$("#top_mobile_header .cnavigation").attr("data-click-state", 1);

				var width = $(window).width();
				var moreButtonContent = '<li class="cnavigation-more"> More </li>';
				
				if (width > 992) {

					$("#top_header").addClass('desktop');
					$("#top_mobile_header").empty();
					var getMenuItemsCounts = adjustMenuItems();
					$(".desktop .cnavigation").data("items-to-copy", getMenuItemsCounts);
					$(".desktop .cnavigation").append(moreButtonContent);
					$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClickSlimmer);

				}

				if (width <= 992 && width > 768) {
					SlimmerMenuClick(6);
				} else if (width <= 768 && width > 480) {
					SlimmerMenuClick(4);
				} else if (width <= 480 && width > 320) {
					SlimmerMenuClick(3);
				} else if (width <= 320) {
					SlimmerMenuClick(2);
				} else {
					var targetElement = $(".cnavigation");
					targetElement.toggleClass("no-pseudo");
				}

				function SlimmerMenuClick(itemsToCopy) {

					var moreButtonContent = '<li class="cnavigation-more"> More </li>';
					
					$(".article-inner-container .cnavigation").empty();
					$(".show .cnavigation").empty();
					$(".cnavigation").data("items-to-copy", itemsToCopy);
					$(".cnavigation").append(moreButtonContent);
					$(document).on('click', '.cnavigation .cnavigation-more', handleNavigationClickSlimmer);
				
				}
				
				function handleNavigationClickSlimmer() {
					
					var body = document.body;
					var moreButtonContent = '<li class="cnavigation-more"> More </li>';
					
					if (!body.classList.contains('slimmer-menu-react')) {
					
						var $navigation = $(this).parents('.cnavigation');
						var clickState = $navigation.attr("data-click-state");
					
						if (clickState == 1) {
							
							$navigation.attr("data-click-state", 0).addClass("dropdown-active");
							var $subMenu = $("#slimmer-submenu");
							
							if ($subMenu.length === 0) {
								
								$navigation.append("<span class='bg_overlay'></span><ul id='slimmer-submenu' class='sub_menu'></ul>");
								var sourceItems = $(".cnavigation li");
								var itemsToCopy = sourceItems.slice($navigation.data("items-to-copy"));
								
								$("#slimmer-submenu").html(itemsToCopy);
								$("#slimmer-submenu .cnavigation-more").remove();
								$navigation.append(moreButtonContent);
								
								var sumWidth = 0;
								var mobile_ul_width = $('#top_mobile_header .cnavigation li:not(#slimmer-submenu li)');
								
								mobile_ul_width.each(function () {
									sumWidth += $(this).width() + 10;
								});
								mobile_ul_width.parents('.cnavigation').find('#slimmer-submenu').width(sumWidth + 50);

								var desktop_sumWidth = 0;
								var ul_width = $('#top_header.desktop .cnavigation li:not(#slimmer-submenu li)');
								
								ul_width.each(function () {
									desktop_sumWidth += $(this).width() + 10;
								});
								ul_width.parents('.cnavigation').find('#slimmer-submenu').width(desktop_sumWidth - 10);
								
								$(".desktop .cnavigation li").css('display','block');
								$(".desktop .cnavigation .bg_overlay").hide();

							} else {
								$subMenu.show();
								$(".bg_overlay").show();
								$(".desktop .cnavigation .bg_overlay").hide();
							}
						} else {
							$navigation.attr("data-click-state", 1).removeClass("dropdown-active");
							$("#slimmer-submenu, .bg_overlay").hide();
						}
					}
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
					const items = menu.querySelectorAll('li:not(.cnavigation-more)');
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
							if(moreItem){
								moreItem.style.display = 'inline';
							}
						} 
					}

					return itemsToDisplay;

				}

				function handleOutsideClick(event) {
					const navigations = $('.cnavigation');
					const subMenuElement = document.querySelector('#slimmer-submenu');
					const bgOverlayElement = document.querySelector('.bg_overlay');
					if (subMenuElement && event.target.className !== 'cnavigation-more') {
						const computedStyles = window.getComputedStyle(subMenuElement);
						const displayPropertyValue = computedStyles.getPropertyValue('display');
						if (displayPropertyValue === 'block') {
							subMenuElement.style.display = 'none';
							bgOverlayElement.style.display = 'none';
							navigations
								.attr('data-click-state', 1)
								.removeClass('dropdown-active');
						}
					}
				}

				window.addEventListener('click', handleOutsideClick);
				
			}
		}
		
	});
	
})(jQuery);
  