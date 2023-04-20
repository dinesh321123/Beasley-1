var daysData = '';
function changeStream($) {
	if($('select[name="ad_second_stream_enabled"]').val() == 'off'){
		$('.ss_days_class').hide();
	}else{
		$('.ss_days_class').show();
	}
}

function checkFluency($) {
	jsonObj = {};
	var days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday","sunday"]

	for(var k = 0; k < days.length; k++){
		if($( ".ss_tr_"+days[k]+" input[name="+days[k]+"]").length){
			let startTime = $( ".ss_tr_"+days[k]+" input[name=starttime]");
			let endTime = $( ".ss_tr_"+days[k]+" input[name=endtime]");
			var temp;
			var timeTo = new Date();
			var timeFrom = new Date();
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

			if($.trim(startTime.val()) !== '' && $.trim(endTime.val()) !== ''){
				if (timeTo < timeFrom){
					endTime.val('');
					startTime.val('');
					alert('start time should be smaller than end time!');
				}
			}

			if($( ".ss_tr_"+days[k]+" input[name="+days[k]+"]").is(':checked')){
				item =  {};
				item['day'] = days[k];
				item['startTime'] = startTime.val();
				item['endTime'] =endTime.val()
				jsonObj[days[k]] = item;
			}
		}

	}

	if(jsonObj){
		daysData = JSON.stringify(jsonObj);
		$("input[name=ss_enabled_days]").val(daysData);
	}
}
function validateHhMm(inputField) {
	var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField);
	if (isValid) {
		return true;
	} else {
		return false;
	}

}
(function ($) {
	var $document = $(document);
	$document.ready(function () {
		$document.on('submit', '#station-setting-form', function (e) {
			checkFluency($);
		});

		$document.on('change', ' input[name="ad_second_stream_enabled"]', function (e) {
			alert('in');


		});
	});
})(jQuery);
