(function ($) {
	var $nsf_document = $(document);
	$nsf_document.ready(function () {

        $(".nsf-container #nsf-form").submit(function(event) {

            event.preventDefault();
        
            var name = $(".nsf-first-name").val();
            var email = $(".nsf-email").val();
            
            $('.nsf-name-error').text('');
            $('.nsf-email-error').text('');

            if (name == "") {
                $('.nsf-name-error').text('required');
                return false;
            }

            if (email != "") {
                var pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
                if (!pattern.test(email)) {
                    $('.nsf-email-error').text('invalid email');
                    $(this).val("");
                    $(this).focus();
                    return false;
                }
            }else{
                $('.nsf-email-error').text('required');
                return false;
            }
           
            this.submit();

        });
    
    });
})(jQuery);