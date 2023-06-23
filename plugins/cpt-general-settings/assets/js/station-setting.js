/**
 * This file contains JavaScript functions used to manage and validate
 * different settings related to streaming and scheduling in a WordPress
 * based radio station website.
 *
 * Functions:
 * 1. changeStream($) - Toggles display of streaming days UI based on stream setting.
 * 2. checkFluency($) - Validates and prepares streaming schedule data before submitting the form.
 * 3. validateHhMm(inputField) - Validates time format.
 */

// Toggles display of streaming days UI based on stream setting
function changeStream($) {
	if($('select[name="ad_second_stream_enabled"]').val() == 'off'){
		$('.ss_days_class').hide();
	}else{
		$('.ss_days_class').show();
	}
}

// Validates and prepares streaming schedule data before submitting the form
function checkFluency($) {
	const jsonObj = {};
	const days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday","sunday"]

	// this loop validates the time format and checks if start time is smaller than end time
	for(let k = 0; k < days.length; k++){
		let dayCheckboxSelector = ".ss_tr_"+days[k]+" input[name="+days[k]+"]";

		if($( dayCheckboxSelector).length){
			let startTime = $( ".ss_tr_"+days[k]+" input[name=starttime]");
			let endTime = $( ".ss_tr_"+days[k]+" input[name=endtime]");
			let temp;
			const timeTo = new Date();
			const timeFrom = new Date();

			// check if start time and end time are not empty
			if($.trim(startTime.val()) !== '') {
				if(!validateHhMm(startTime.val())) {
					startTime.val('');
					alert('Enter Valid start Time');
				} else {
					temp = startTime.val().split(":");
					timeFrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
					timeFrom.setMinutes(parseInt(temp[1]));
				}
			}
			if($.trim(endTime.val()) !== '') {
				if(!validateHhMm(endTime.val())) {
					endTime.val('');
					alert('Enter Valid end Time');
				} else {
					temp = endTime.val().split(":");
					timeTo.setHours((parseInt(temp[0]) - 1 + 24) % 24);
					timeTo.setMinutes(parseInt(temp[1]));
				}
			}

			// check if start time is smaller than end time
			if($.trim(startTime.val()) !== '' && $.trim(endTime.val()) !== ''){
				if (timeTo < timeFrom){
					endTime.val('');
					startTime.val('');
					alert('start time should be smaller than end time!');
				}
			}

			// checks in the day is checked and then add the data to json object
			if($( dayCheckboxSelector).is(':checked')){
				item =  {};
				item['day'] = days[k];
				item['startTime'] = startTime.val();
				item['endTime'] =endTime.val()
				jsonObj[days[k]] = item;
			}
		}

	}

	let daysData;
	if (jsonObj) {
		daysData = JSON.stringify(jsonObj);
		$("input[name=ss_enabled_days]").val(daysData);
	}
}

// Validates time format
function validateHhMm(inputField) {
	// validates that a time is in the correct format of HH:MM between 00:00 and 24:00
	return /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField);
}

// Initialize event listeners for form submission and streaming setting change
(function ($) {
	const $document = $(document);
	$document.ready(function () {
		$document.on('submit', '#station-setting-form', function (e) {
			checkFluency($);
		});

		// check if second stream enabled status is changed
		$document.on('change', ' input[name="ad_second_stream_enabled"]', function (e) {
			alert('in');
		});
	});
})(jQuery);
