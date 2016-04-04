(function ($) {
	$(document).ready( function () {
    var clockHolder = $( '.homepage_countdown_clock_ticker' );
    var targetDate = Number(clockHolder.data( 'countdownTarget' ));
    var countdownLength = Math.floor( ( targetDate - Date.now() ) / 1000 );
    if ( countdownLength < 0 ) {
      countdownLength = 0;
    }
    var clock = window.countdownClock = clockHolder.FlipClock( countdownLength, {
  		clockFace: 'DailyCounter',
  		countdown: true,
      callbacks: {
      	interval: function() {
      		var time = this.factory.getTime().time;

      		if( !time ) {
        		$( '.homepage_countdown_clock_message_counting' ).hide();
            $( '.homepage_countdown_clock_message_reached' ).show();
        	}
      	},
        init: function() {
          var time = this.factory.getTime().time;

      		if( !time ) {
        		$( '.homepage_countdown_clock_message_counting' ).hide();
            $( '.homepage_countdown_clock_message_reached' ).show();
        	}
        }
      }
  	});


	});
})(jQuery);
