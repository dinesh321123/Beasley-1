(function ($) {
	$(document).ready(function () {
		$('[class*="_shows-"]').addClass("custom-margin");
		width = $(window).width();

		if ($(window).width() <= 768 && $(window).width() > 575) {
			$(".top_mobile_header").empty();			
			licount = $(".cnavigation").find("li").length;
			if (licount > 4) {
				$(".cnavigation")
					.after()
					.click(function () {
						if ($(this).attr("data-click-state") == 1) {
							$(this).attr("data-click-state", 0);
							if ($(".cnavigation").find(".sub_menu").length == 0) {
								$(".cnavigation ").append(
									"<span class='bg_overlay'></span><ul class='sub_menu'></ul>"
								);
								var sourceItems = $(".cnavigation li");
								var itemsToCopy = sourceItems.slice(4);
								$(".sub_menu").html(itemsToCopy);
							} else {
								$(".sub_menu,.bg_overlay").show();
							}
						} else {
							$(this).attr("data-click-state", 1);
							$(".sub_menu,.bg_overlay").hide();
						}
					});
			} else {
				var targetElement = $(".cnavigation");
				targetElement.toggleClass("no-pseudo");
			}
		}
		if ($(window).width() <= 575) {
			$(".article-inner-container .cnavigation").empty();
			$(".show .cnavigation").empty();
			licount = $(".cnavigation").find("li").length;
			if (licount > 3) {
				$(".cnavigation")
					.after()
					.click(function () {
						if ($(this).attr("data-click-state") == 1) {
							$(this).attr("data-click-state", 0);
							if ($(".cnavigation").find(".sub_menu_2").length == 0) {
								$(".cnavigation ").append(
									"<span class='bg_overlay_2'></span><ul class='sub_menu_2'></ul>"
								);
								var sourceItems = $(".cnavigation li");
								var itemsToCopy = sourceItems.slice(3);
								$(".sub_menu_2").html(itemsToCopy);
								$(".sub_menu_2,.bg_overlay_2").show();
							} else {
								$(".sub_menu_2,.bg_overlay_2").show();
							}
						} else {
							$(this).attr("data-click-state", 1);
							$(".sub_menu_2,.bg_overlay_2").hide();
						}
					});
			} else {
				var targetElement = $(".cnavigation");
				targetElement.toggleClass("no-pseudo");
			}
		}
	});
})(jQuery);
