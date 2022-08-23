jQuery(document).ready(function($) {
	function isGutenberg() {
        return ($('.block-editor-writing-flow').length > 0);
    }

    function checkImageReturnWarningMessageOrEmpty() {
        if (isGutenberg()) {
            var $img = $('.editor-post-featured-image').find('img');
        } else {
            var $img = $('#postimagediv').find('img');
        }
		console.log('Image length - '+$img.length);
		if ($img.length === 0) {
			return passedFromServer.jsWarningHtml;
        }
        return '';
    }

    // Contains three test "failures" at page load
    var isTooSmallTrials = [ true, true, true ];

    function disablePublishAndWarn(message) {
        createMessageAreaIfNeeded();
        $('#nofeature-message').addClass("error")
            .html('<p>'+message+'</p>');
        if (isGutenberg()) {
            $('.editor-post-publish-panel__toggle').attr('disabled', 'disabled');
        } else {
            $('#publish').attr('disabled','disabled');
        }
    }

    function clearWarningAndEnablePublish() {
        $('#nofeature-message').remove();
        if (isGutenberg()) {
            $('.editor-post-publish-panel__toggle').removeAttr('disabled');
        } else {
            $('#publish').removeAttr('disabled');
        }
    }

    function createMessageAreaIfNeeded() {
        if ($('body').find("#nofeature-message").length === 0) {
            if (isGutenberg()) {
                $('.components-notice-list').append('<div id="nofeature-message"></div>');
            } else {
                $('#post').before('<div id="nofeature-message"></div>');
            }
        }
    }

    function detectWarnFeaturedImage() {
        if (checkImageReturnWarningMessageOrEmpty()) {
            disablePublishAndWarn(checkImageReturnWarningMessageOrEmpty());
        } else {
            clearWarningAndEnablePublish();
        }
    }

    detectWarnFeaturedImage();
    setInterval(detectWarnFeaturedImage, 800);

});
