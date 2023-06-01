jQuery(document).ready(function($) {
    jQuery(window).scroll(function() {
        var scroll = jQuery(window).scrollTop();
        if (scroll >= 200) {
            jQuery("div#side-sortables").addClass("sticky_publish");
        }
        else{
            jQuery("div#side-sortables").removeClass("sticky_publish");
        }
    }); //missing );
});