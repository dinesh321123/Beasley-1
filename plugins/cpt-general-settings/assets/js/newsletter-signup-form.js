(function ($) {
	var $nsf_document = $(document);
	$nsf_document.ready(function () {

        function nsf_disable_button(){
            var email = $(".nsf-email").val();
            var pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;

            if ($('.nsf-first-name').val() == "" || email == "" || !$("#nsf-checkbox-content").is(":checked") ) {
                $("#nsf-form .nsf-form-submit").prop("disabled", true).css('opacity', 0.5).css('cursor', 'not-allowed');
                $("#nsf-form .nsf-form-submit").off("click");
            }else{
                if (!pattern.test(email)) {
                    $("#nsf-form .nsf-form-submit").prop("disabled", true).css('opacity', 0.5).css('cursor', 'not-allowed');
                    $("#nsf-form .nsf-form-submit").off("click");
                } else {
                    $("#nsf-form .nsf-form-submit").prop("disabled", false).css('opacity', 1).css('cursor', 'pointer');
                }
            }
        }

        nsf_disable_button();

        $nsf_document.on('change', '.nsf-first-name', function(event) {
            nsf_disable_button();
            if ($(this).val() == "") {
                $('.nsf-fname-error-msg').text('First name required.');
                $(this).focus();
            } else {
                $('.nsf-fname-error-msg').html('');
            }
        });

        $nsf_document.on('change', '.nsf-email', function(event) {
            var pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            nsf_disable_button();
            if ($(this).val() == "") {
                $('.nsf-email-error-msg').text('Email required.');
                $(this).focus();
            } else {
                if (!pattern.test($(this).val())) {
                    $('.nsf-email-error-msg').text('invalid email');
                    $(this).focus();
                } else {
                    $('.nsf-email-error-msg').html('');
                }
            }
        });

        $nsf_document.on('change', '#nsf-checkbox-content', function(event) {
            nsf_disable_button();
        });

        $nsf_document.on('click', '.nsf-container #nsf-form .nsf-form-submit', function(event) {

            event.preventDefault();

            $('.nsf-email-error-msg').html('');
            $('.nsf-fname-error-msg').html('');

            var name 				= $(".nsf-first-name").val();
            var email 				= $(".nsf-email").val();
            var nsf_last_name 		= $("#nsf-last-name").val();
            var nsf_subscription_attributes = $("#nsf_subscription_attributes").val();
            var nsf_subscription_ID = $("#nsf_subscription_ID").val();

            if (name == "") {
                $(".nsf-first-name").focus();
                $('.nsf-fname-error-msg').text('First name required.');
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
                $('.nsf-email-error-msg').text('Email required.');
                return false;
            }

            if (!$("#nsf-checkbox-content").is(":checked")) {
                $("#nsf-checkbox-content").focus();
                return false;
            }

            $(this).prop('disabled', true).css('opacity', 0.5).css('cursor', 'not-allowed');

            $.ajax({
				type : 'POST',
                dataType: 'json',
				url : nsf_ajax_object.url,
				data : {
					action: 'newsletter_signup_form_data_submit',
                    nonce: nsf_ajax_object.nonce,
                    name : name,
                    email : email,
                    nsf_last_name : nsf_last_name,
                    nsf_page_path : nsf_ajax_object.page_path,
                    nsf_subscription_attributes : nsf_subscription_attributes,
                    nsf_subscription_ID : nsf_subscription_ID,
				},
                beforeSend: function() {
                    $('.nsf-container #nsf-form .nsf-form-submit').prop('disabled', true).css('opacity', 0.5).css('cursor', 'not-allowed');
                    $('.nsf-spinner').css('display','block');
                    $('.response-error-container').html('');
                },
				success : function( response ) {
					var res = jQuery.parseJSON(response.body);
                    if(res.code == '200'){
                        $('.response-error-container').html('Success! You\'re now subscribed.').fadeIn();
                        setTimeout(function() {
                            $('.response-error-container').fadeOut();
                          }, 20000);

						const userAttributeArray = [
							{'attributeName': nsf_subscription_attributes , 'attributeValue': true},
							{'attributeName': 'email_subscribe' , 'attributeValue': 'opted_in'},
							{'attributeName': '$firstname' , 'attributeValue': name},
							{'attributeName': '$lastname' , 'attributeValue': nsf_last_name},
						];
						elementsWithEmailClass = document.getElementsByClassName('nsf-email');
						const emailIsMaskedMParticleVal =  elementsWithEmailClass && elementsWithEmailClass.length > 0 ? !!elementsWithEmailClass[0].disabled : false;
						window.beasleyanalytics?.setMParticleUserAttributeArray(email, emailIsMaskedMParticleVal, userAttributeArray);
					}else{
                        $('.response-error-container').html('Error Code '+res.code+' : '+res.message).css('color','red');
                    }
				},
				error : function( error ) {
					console.log(error);
					$('.response-error-container').html('Something went wrong with API endpoint.').css('color','red');
				},
                complete: function() {
                    $('.nsf-container #nsf-form .nsf-form-submit').prop('disabled', false).css('opacity', 1).css('cursor', 'pointer');
                    $(".nsf-first-name").val('');
                    $(".nsf-email").val('');
                    $("#nsf-last-name").val('');
                    $("#nsf-checkbox-content").prop("checked", false);
                    $("#nsf-form .nsf-form-submit").prop("disabled", true).css('opacity', 0.5).css('cursor', 'not-allowed');
                    $('.nsf-spinner').css('display','none');
                    $('.nsf-email-error-msg').html('');
                    $('.nsf-fname-error-msg').html('');
                }
			});

        });

    });
})(jQuery);
