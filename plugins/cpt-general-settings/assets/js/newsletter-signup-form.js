(function ($) {
	var $nsf_document = $(document);
	$nsf_document.ready(function () {

        $(".nsf-container #nsf-form").submit(function(event) {

            event.preventDefault();
        
            var name = $(".nsf-first-name").val();
            var email = $(".nsf-email").val();

            if (name == "") {
                $(".nsf-first-name").focus();
                return false;
            }

            if (email != "") {
                var pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
                if (!pattern.test(email)) {
                    $('.nsf-email-error-msg').text('invalid email');
                    $(".nsf-email").focus();
                    $(this).val("");
                    $(this).focus();
                    return false;
                }
            }else{
                $(".nsf-email").focus();
                return false;
            }
           
            this.submit();

        });
    
    });
})(jQuery);