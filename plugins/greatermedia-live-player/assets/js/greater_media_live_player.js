/*!
  * Bowser - a browser detector
  * https://github.com/ded/bowser
  * MIT License | (c) Dustin Diaz 2014
  */

!function (name, definition) {
  if (typeof module != 'undefined' && module.exports) module.exports['browser'] = definition()
  else if (typeof define == 'function' && define.amd) define(definition)
  else this[name] = definition()
}('bowser', function () {
  /**
    * See useragents.js for examples of navigator.userAgent
    */

  var t = true

  function detect(ua) {

    function getFirstMatch(regex) {
      var match = ua.match(regex);
      return (match && match.length > 1 && match[1]) || '';
    }

    var iosdevice = getFirstMatch(/(ipod|iphone|ipad)/i).toLowerCase()
      , likeAndroid = /like android/i.test(ua)
      , android = !likeAndroid && /android/i.test(ua)
      , versionIdentifier = getFirstMatch(/version\/(\d+(\.\d+)?)/i)
      , tablet = /tablet/i.test(ua)
      , mobile = !tablet && /[^-]mobi/i.test(ua)
      , result

    if (/opera|opr/i.test(ua)) {
      result = {
        name: 'Opera'
      , opera: t
      , version: versionIdentifier || getFirstMatch(/(?:opera|opr)[\s\/](\d+(\.\d+)?)/i)
      }
    }
    else if (/windows phone/i.test(ua)) {
      result = {
        name: 'Windows Phone'
      , windowsphone: t
      , msie: t
      , version: getFirstMatch(/iemobile\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/msie|trident/i.test(ua)) {
      result = {
        name: 'Internet Explorer'
      , msie: t
      , version: getFirstMatch(/(?:msie |rv:)(\d+(\.\d+)?)/i)
      }
    }
    else if (/chrome|crios|crmo/i.test(ua)) {
      result = {
        name: 'Chrome'
      , chrome: t
      , version: getFirstMatch(/(?:chrome|crios|crmo)\/(\d+(\.\d+)?)/i)
      }
    }
    else if (iosdevice) {
      result = {
        name : iosdevice == 'iphone' ? 'iPhone' : iosdevice == 'ipad' ? 'iPad' : 'iPod'
      }
      // WTF: version is not part of user agent in web apps
      if (versionIdentifier) {
        result.version = versionIdentifier
      }
    }
    else if (/sailfish/i.test(ua)) {
      result = {
        name: 'Sailfish'
      , sailfish: t
      , version: getFirstMatch(/sailfish\s?browser\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/seamonkey\//i.test(ua)) {
      result = {
        name: 'SeaMonkey'
      , seamonkey: t
      , version: getFirstMatch(/seamonkey\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/firefox|iceweasel/i.test(ua)) {
      result = {
        name: 'Firefox'
      , firefox: t
      , version: getFirstMatch(/(?:firefox|iceweasel)[ \/](\d+(\.\d+)?)/i)
      }
      if (/\((mobile|tablet);[^\)]*rv:[\d\.]+\)/i.test(ua)) {
        result.firefoxos = t
      }
    }
    else if (/silk/i.test(ua)) {
      result =  {
        name: 'Amazon Silk'
      , silk: t
      , version : getFirstMatch(/silk\/(\d+(\.\d+)?)/i)
      }
    }
    else if (android) {
      result = {
        name: 'Android'
      , version: versionIdentifier
      }
    }
    else if (/phantom/i.test(ua)) {
      result = {
        name: 'PhantomJS'
      , phantom: t
      , version: getFirstMatch(/phantomjs\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/blackberry|\bbb\d+/i.test(ua) || /rim\stablet/i.test(ua)) {
      result = {
        name: 'BlackBerry'
      , blackberry: t
      , version: versionIdentifier || getFirstMatch(/blackberry[\d]+\/(\d+(\.\d+)?)/i)
      }
    }
    else if (/(web|hpw)os/i.test(ua)) {
      result = {
        name: 'WebOS'
      , webos: t
      , version: versionIdentifier || getFirstMatch(/w(?:eb)?osbrowser\/(\d+(\.\d+)?)/i)
      };
      /touchpad\//i.test(ua) && (result.touchpad = t)
    }
    else if (/bada/i.test(ua)) {
      result = {
        name: 'Bada'
      , bada: t
      , version: getFirstMatch(/dolfin\/(\d+(\.\d+)?)/i)
      };
    }
    else if (/tizen/i.test(ua)) {
      result = {
        name: 'Tizen'
      , tizen: t
      , version: getFirstMatch(/(?:tizen\s?)?browser\/(\d+(\.\d+)?)/i) || versionIdentifier
      };
    }
    else if (/safari/i.test(ua)) {
      result = {
        name: 'Safari'
      , safari: t
      , version: versionIdentifier
      }
    }
    else result = {}

    // set webkit or gecko flag for browsers based on these engines
    if (/(apple)?webkit/i.test(ua)) {
      result.name = result.name || "Webkit"
      result.webkit = t
      if (!result.version && versionIdentifier) {
        result.version = versionIdentifier
      }
    } else if (!result.opera && /gecko\//i.test(ua)) {
      result.name = result.name || "Gecko"
      result.gecko = t
      result.version = result.version || getFirstMatch(/gecko\/(\d+(\.\d+)?)/i)
    }

    // set OS flags for platforms that have multiple browsers
    if (android || result.silk) {
      result.android = t
    } else if (iosdevice) {
      result[iosdevice] = t
      result.ios = t
    }

    // OS version extraction
    var osVersion = '';
    if (iosdevice) {
      osVersion = getFirstMatch(/os (\d+([_\s]\d+)*) like mac os x/i);
      osVersion = osVersion.replace(/[_\s]/g, '.');
    } else if (android) {
      osVersion = getFirstMatch(/android[ \/-](\d+(\.\d+)*)/i);
    } else if (result.windowsphone) {
      osVersion = getFirstMatch(/windows phone (?:os)?\s?(\d+(\.\d+)*)/i);
    } else if (result.webos) {
      osVersion = getFirstMatch(/(?:web|hpw)os\/(\d+(\.\d+)*)/i);
    } else if (result.blackberry) {
      osVersion = getFirstMatch(/rim\stablet\sos\s(\d+(\.\d+)*)/i);
    } else if (result.bada) {
      osVersion = getFirstMatch(/bada\/(\d+(\.\d+)*)/i);
    } else if (result.tizen) {
      osVersion = getFirstMatch(/tizen[\/\s](\d+(\.\d+)*)/i);
    }
    if (osVersion) {
      result.osversion = osVersion;
    }

    // device type extraction
    var osMajorVersion = osVersion.split('.')[0];
    if (tablet || iosdevice == 'ipad' || (android && (osMajorVersion == 3 || (osMajorVersion == 4 && !mobile))) || result.silk) {
      result.tablet = t
    } else if (mobile || iosdevice == 'iphone' || iosdevice == 'ipod' || android || result.blackberry || result.webos || result.bada) {
      result.mobile = t
    }

    // Graded Browser Support
    // http://developer.yahoo.com/yui/articles/gbs
    if ((result.msie && result.version >= 10) ||
        (result.chrome && result.version >= 20) ||
        (result.firefox && result.version >= 20.0) ||
        (result.safari && result.version >= 6) ||
        (result.opera && result.version >= 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] >= 6) ||
        (result.blackberry && result.version >= 10.1)
        ) {
      result.a = t;
    }
    else if ((result.msie && result.version < 10) ||
        (result.chrome && result.version < 20) ||
        (result.firefox && result.version < 20.0) ||
        (result.safari && result.version < 6) ||
        (result.opera && result.version < 10.0) ||
        (result.ios && result.osversion && result.osversion.split(".")[0] < 6)
        ) {
      result.c = t
    } else result.x = t

    return result
  }

  var bowser = detect(typeof navigator !== 'undefined' ? navigator.userAgent : '')


  /*
   * Set our detect method to the main bowser object so we can
   * reuse it to test other user agents.
   * This is needed to implement future tests.
   */
  bowser._detect = detect;

  return bowser
});

/*! noUiSlider - 7.0.10 - 2014-12-27 14:50:46 */

/*jslint browser: true */
/*jslint white: true */

(function( $ ){

	'use strict';


	// Removes duplicates from an array.
	function unique(array) {
		return $.grep(array, function(el, index) {
			return index === $.inArray(el, array);
		});
	}

	// Round a value to the closest 'to'.
	function closest ( value, to ) {
		return Math.round(value / to) * to;
	}

	// Checks whether a value is numerical.
	function isNumeric ( a ) {
		return typeof a === 'number' && !isNaN( a ) && isFinite( a );
	}

	// Rounds a number to 7 supported decimals.
	function accurateNumber( number ) {
		var p = Math.pow(10, 7);
		return Number((Math.round(number*p)/p).toFixed(7));
	}

	// Sets a class and removes it after [duration] ms.
	function addClassFor ( element, className, duration ) {
		element.addClass(className);
		setTimeout(function(){
			element.removeClass(className);
		}, duration);
	}

	// Limits a value to 0 - 100
	function limit ( a ) {
		return Math.max(Math.min(a, 100), 0);
	}

	// Wraps a variable as an array, if it isn't one yet.
	function asArray ( a ) {
		return $.isArray(a) ? a : [a];
	}

	// Counts decimals
	function countDecimals ( numStr ) {
		var pieces = numStr.split(".");
		return pieces.length > 1 ? pieces[1].length : 0;
	}


	var
	// Cache the document selector;
	/** @const */
	doc = $(document),
	// Make a backup of the original jQuery/Zepto .val() method.
	/** @const */
	$val = $.fn.val,
	// Namespace for binding and unbinding slider events;
	/** @const */
	namespace = '.nui',
	// Determine the events to bind. IE11 implements pointerEvents without
	// a prefix, which breaks compatibility with the IE10 implementation.
	/** @const */
	actions = window.navigator.pointerEnabled ? {
		start: 'pointerdown',
		move: 'pointermove',
		end: 'pointerup'
	} : window.navigator.msPointerEnabled ? {
		start: 'MSPointerDown',
		move: 'MSPointerMove',
		end: 'MSPointerUp'
	} : {
		start: 'mousedown touchstart',
		move: 'mousemove touchmove',
		end: 'mouseup touchend'
	},
	// Re-usable list of classes;
	/** @const */
	Classes = [
/*  0 */  'noUi-target'
/*  1 */ ,'noUi-base'
/*  2 */ ,'noUi-origin'
/*  3 */ ,'noUi-handle'
/*  4 */ ,'noUi-horizontal'
/*  5 */ ,'noUi-vertical'
/*  6 */ ,'noUi-background'
/*  7 */ ,'noUi-connect'
/*  8 */ ,'noUi-ltr'
/*  9 */ ,'noUi-rtl'
/* 10 */ ,'noUi-dragable'
/* 11 */ ,''
/* 12 */ ,'noUi-state-drag'
/* 13 */ ,''
/* 14 */ ,'noUi-state-tap'
/* 15 */ ,'noUi-active'
/* 16 */ ,''
/* 17 */ ,'noUi-stacking'
	];


// Value calculation

	// Determine the size of a sub-range in relation to a full range.
	function subRangeRatio ( pa, pb ) {
		return (100 / (pb - pa));
	}

	// (percentage) How many percent is this value of this range?
	function fromPercentage ( range, value ) {
		return (value * 100) / ( range[1] - range[0] );
	}

	// (percentage) Where is this value on this range?
	function toPercentage ( range, value ) {
		return fromPercentage( range, range[0] < 0 ?
			value + Math.abs(range[0]) :
				value - range[0] );
	}

	// (value) How much is this percentage on this range?
	function isPercentage ( range, value ) {
		return ((value * ( range[1] - range[0] )) / 100) + range[0];
	}


// Range conversion

	function getJ ( value, arr ) {

		var j = 1;

		while ( value >= arr[j] ){
			j += 1;
		}

		return j;
	}

	// (percentage) Input a value, find where, on a scale of 0-100, it applies.
	function toStepping ( xVal, xPct, value ) {

		if ( value >= xVal.slice(-1)[0] ){
			return 100;
		}

		var j = getJ( value, xVal ), va, vb, pa, pb;

		va = xVal[j-1];
		vb = xVal[j];
		pa = xPct[j-1];
		pb = xPct[j];

		return pa + (toPercentage([va, vb], value) / subRangeRatio (pa, pb));
	}

	// (value) Input a percentage, find where it is on the specified range.
	function fromStepping ( xVal, xPct, value ) {

		// There is no range group that fits 100
		if ( value >= 100 ){
			return xVal.slice(-1)[0];
		}

		var j = getJ( value, xPct ), va, vb, pa, pb;

		va = xVal[j-1];
		vb = xVal[j];
		pa = xPct[j-1];
		pb = xPct[j];

		return isPercentage([va, vb], (value - pa) * subRangeRatio (pa, pb));
	}

	// (percentage) Get the step that applies at a certain value.
	function getStep ( xPct, xSteps, snap, value ) {

		if ( value === 100 ) {
			return value;
		}

		var j = getJ( value, xPct ), a, b;

		// If 'snap' is set, steps are used as fixed points on the slider.
		if ( snap ) {

			a = xPct[j-1];
			b = xPct[j];

			// Find the closest position, a or b.
			if ((value - a) > ((b-a)/2)){
				return b;
			}

			return a;
		}

		if ( !xSteps[j-1] ){
			return value;
		}

		return xPct[j-1] + closest(
			value - xPct[j-1],
			xSteps[j-1]
		);
	}


// Entry parsing

	function handleEntryPoint ( index, value, that ) {

		var percentage;

		// Wrap numerical input in an array.
		if ( typeof value === "number" ) {
			value = [value];
		}

		// Reject any invalid input, by testing whether value is an array.
		if ( Object.prototype.toString.call( value ) !== '[object Array]' ){
			throw new Error("noUiSlider: 'range' contains invalid value.");
		}

		// Covert min/max syntax to 0 and 100.
		if ( index === 'min' ) {
			percentage = 0;
		} else if ( index === 'max' ) {
			percentage = 100;
		} else {
			percentage = parseFloat( index );
		}

		// Check for correct input.
		if ( !isNumeric( percentage ) || !isNumeric( value[0] ) ) {
			throw new Error("noUiSlider: 'range' value isn't numeric.");
		}

		// Store values.
		that.xPct.push( percentage );
		that.xVal.push( value[0] );

		// NaN will evaluate to false too, but to keep
		// logging clear, set step explicitly. Make sure
		// not to override the 'step' setting with false.
		if ( !percentage ) {
			if ( !isNaN( value[1] ) ) {
				that.xSteps[0] = value[1];
			}
		} else {
			that.xSteps.push( isNaN(value[1]) ? false : value[1] );
		}
	}

	function handleStepPoint ( i, n, that ) {

		// Ignore 'false' stepping.
		if ( !n ) {
			return true;
		}

		// Factor to range ratio
		that.xSteps[i] = fromPercentage([
			 that.xVal[i]
			,that.xVal[i+1]
		], n) / subRangeRatio (
			that.xPct[i],
			that.xPct[i+1] );
	}


// Interface

	// The interface to Spectrum handles all direction-based
	// conversions, so the above values are unaware.

	function Spectrum ( entry, snap, direction, singleStep ) {

		this.xPct = [];
		this.xVal = [];
		this.xSteps = [ singleStep || false ];
		this.xNumSteps = [ false ];

		this.snap = snap;
		this.direction = direction;

		var index, ordered = [ /* [0, 'min'], [1, '50%'], [2, 'max'] */ ];

		// Map the object keys to an array.
		for ( index in entry ) {
			if ( entry.hasOwnProperty(index) ) {
				ordered.push([entry[index], index]);
			}
		}

		// Sort all entries by value (numeric sort).
		ordered.sort(function(a, b) { return a[0] - b[0]; });

		// Convert all entries to subranges.
		for ( index = 0; index < ordered.length; index++ ) {
			handleEntryPoint(ordered[index][1], ordered[index][0], this);
		}

		// Store the actual step values.
		// xSteps is sorted in the same order as xPct and xVal.
		this.xNumSteps = this.xSteps.slice(0);

		// Convert all numeric steps to the percentage of the subrange they represent.
		for ( index = 0; index < this.xNumSteps.length; index++ ) {
			handleStepPoint(index, this.xNumSteps[index], this);
		}
	}

	Spectrum.prototype.getMargin = function ( value ) {
		return this.xPct.length === 2 ? fromPercentage(this.xVal, value) : false;
	};

	Spectrum.prototype.toStepping = function ( value ) {

		value = toStepping( this.xVal, this.xPct, value );

		// Invert the value if this is a right-to-left slider.
		if ( this.direction ) {
			value = 100 - value;
		}

		return value;
	};

	Spectrum.prototype.fromStepping = function ( value ) {

		// Invert the value if this is a right-to-left slider.
		if ( this.direction ) {
			value = 100 - value;
		}

		return accurateNumber(fromStepping( this.xVal, this.xPct, value ));
	};

	Spectrum.prototype.getStep = function ( value ) {

		// Find the proper step for rtl sliders by search in inverse direction.
		// Fixes issue #262.
		if ( this.direction ) {
			value = 100 - value;
		}

		value = getStep(this.xPct, this.xSteps, this.snap, value );

		if ( this.direction ) {
			value = 100 - value;
		}

		return value;
	};

	Spectrum.prototype.getApplicableStep = function ( value ) {

		// If the value is 100%, return the negative step twice.
		var j = getJ(value, this.xPct), offset = value === 100 ? 2 : 1;
		return [this.xNumSteps[j-2], this.xVal[j-offset], this.xNumSteps[j-offset]];
	};

	// Outside testing
	Spectrum.prototype.convert = function ( value ) {
		return this.getStep(this.toStepping(value));
	};

/*	Every input option is tested and parsed. This'll prevent
	endless validation in internal methods. These tests are
	structured with an item for every option available. An
	option can be marked as required by setting the 'r' flag.
	The testing function is provided with three arguments:
		- The provided value for the option;
		- A reference to the options object;
		- The name for the option;

	The testing function returns false when an error is detected,
	or true when everything is OK. It can also modify the option
	object, to make sure all values can be correctly looped elsewhere. */

	/** @const */
	var defaultFormatter = { 'to': function( value ){
		return value.toFixed(2);
	}, 'from': Number };

	function testStep ( parsed, entry ) {

		if ( !isNumeric( entry ) ) {
			throw new Error("noUiSlider: 'step' is not numeric.");
		}

		// The step option can still be used to set stepping
		// for linear sliders. Overwritten if set in 'range'.
		parsed.singleStep = entry;
	}

	function testRange ( parsed, entry ) {

		// Filter incorrect input.
		if ( typeof entry !== 'object' || $.isArray(entry) ) {
			throw new Error("noUiSlider: 'range' is not an object.");
		}

		// Catch missing start or end.
		if ( entry.min === undefined || entry.max === undefined ) {
			throw new Error("noUiSlider: Missing 'min' or 'max' in 'range'.");
		}

		parsed.spectrum = new Spectrum(entry, parsed.snap, parsed.dir, parsed.singleStep);
	}

	function testStart ( parsed, entry ) {

		entry = asArray(entry);

		// Validate input. Values aren't tested, as the public .val method
		// will always provide a valid location.
		if ( !$.isArray( entry ) || !entry.length || entry.length > 2 ) {
			throw new Error("noUiSlider: 'start' option is incorrect.");
		}

		// Store the number of handles.
		parsed.handles = entry.length;

		// When the slider is initialized, the .val method will
		// be called with the start options.
		parsed.start = entry;
	}

	function testSnap ( parsed, entry ) {

		// Enforce 100% stepping within subranges.
		parsed.snap = entry;

		if ( typeof entry !== 'boolean' ){
			throw new Error("noUiSlider: 'snap' option must be a boolean.");
		}
	}

	function testAnimate ( parsed, entry ) {

		// Enforce 100% stepping within subranges.
		parsed.animate = entry;

		if ( typeof entry !== 'boolean' ){
			throw new Error("noUiSlider: 'animate' option must be a boolean.");
		}
	}

	function testConnect ( parsed, entry ) {

		if ( entry === 'lower' && parsed.handles === 1 ) {
			parsed.connect = 1;
		} else if ( entry === 'upper' && parsed.handles === 1 ) {
			parsed.connect = 2;
		} else if ( entry === true && parsed.handles === 2 ) {
			parsed.connect = 3;
		} else if ( entry === false ) {
			parsed.connect = 0;
		} else {
			throw new Error("noUiSlider: 'connect' option doesn't match handle count.");
		}
	}

	function testOrientation ( parsed, entry ) {

		// Set orientation to an a numerical value for easy
		// array selection.
		switch ( entry ){
		  case 'horizontal':
			parsed.ort = 0;
			break;
		  case 'vertical':
			parsed.ort = 1;
			break;
		  default:
			throw new Error("noUiSlider: 'orientation' option is invalid.");
		}
	}

	function testMargin ( parsed, entry ) {

		if ( !isNumeric(entry) ){
			throw new Error("noUiSlider: 'margin' option must be numeric.");
		}

		parsed.margin = parsed.spectrum.getMargin(entry);

		if ( !parsed.margin ) {
			throw new Error("noUiSlider: 'margin' option is only supported on linear sliders.");
		}
	}

	function testLimit ( parsed, entry ) {

		if ( !isNumeric(entry) ){
			throw new Error("noUiSlider: 'limit' option must be numeric.");
		}

		parsed.limit = parsed.spectrum.getMargin(entry);

		if ( !parsed.limit ) {
			throw new Error("noUiSlider: 'limit' option is only supported on linear sliders.");
		}
	}

	function testDirection ( parsed, entry ) {

		// Set direction as a numerical value for easy parsing.
		// Invert connection for RTL sliders, so that the proper
		// handles get the connect/background classes.
		switch ( entry ) {
		  case 'ltr':
			parsed.dir = 0;
			break;
		  case 'rtl':
			parsed.dir = 1;
			parsed.connect = [0,2,1,3][parsed.connect];
			break;
		  default:
			throw new Error("noUiSlider: 'direction' option was not recognized.");
		}
	}

	function testBehaviour ( parsed, entry ) {

		// Make sure the input is a string.
		if ( typeof entry !== 'string' ) {
			throw new Error("noUiSlider: 'behaviour' must be a string containing options.");
		}

		// Check if the string contains any keywords.
		// None are required.
		var tap = entry.indexOf('tap') >= 0,
			drag = entry.indexOf('drag') >= 0,
			fixed = entry.indexOf('fixed') >= 0,
			snap = entry.indexOf('snap') >= 0;

		parsed.events = {
			tap: tap || snap,
			drag: drag,
			fixed: fixed,
			snap: snap
		};
	}

	function testFormat ( parsed, entry ) {

		parsed.format = entry;

		// Any object with a to and from method is supported.
		if ( typeof entry.to === 'function' && typeof entry.from === 'function' ) {
			return true;
		}

		throw new Error( "noUiSlider: 'format' requires 'to' and 'from' methods.");
	}

	// Test all developer settings and parse to assumption-safe values.
	function testOptions ( options ) {

		var parsed = {
			margin: 0,
			limit: 0,
			animate: true,
			format: defaultFormatter
		}, tests;

		// Tests are executed in the order they are presented here.
		tests = {
			'step': { r: false, t: testStep },
			'start': { r: true, t: testStart },
			'connect': { r: true, t: testConnect },
			'direction': { r: true, t: testDirection },
			'snap': { r: false, t: testSnap },
			'animate': { r: false, t: testAnimate },
			'range': { r: true, t: testRange },
			'orientation': { r: false, t: testOrientation },
			'margin': { r: false, t: testMargin },
			'limit': { r: false, t: testLimit },
			'behaviour': { r: true, t: testBehaviour },
			'format': { r: false, t: testFormat }
		};

		// Set defaults where applicable.
		options = $.extend({
			'connect': false,
			'direction': 'ltr',
			'behaviour': 'tap',
			'orientation': 'horizontal'
		}, options);

		// Run all options through a testing mechanism to ensure correct
		// input. It should be noted that options might get modified to
		// be handled properly. E.g. wrapping integers in arrays.
		$.each( tests, function( name, test ){

			// If the option isn't set, but it is required, throw an error.
			if ( options[name] === undefined ) {

				if ( test.r ) {
					throw new Error("noUiSlider: '" + name + "' is required.");
				}

				return true;
			}

			test.t( parsed, options[name] );
		});

		// Pre-define the styles.
		parsed.style = parsed.ort ? 'top' : 'left';

		return parsed;
	}

// Class handling

	// Delimit proposed values for handle positions.
	function getPositions ( a, b, delimit ) {

		// Add movement to current position.
		var c = a + b[0], d = a + b[1];

		// Only alter the other position on drag,
		// not on standard sliding.
		if ( delimit ) {
			if ( c < 0 ) {
				d += Math.abs(c);
			}
			if ( d > 100 ) {
				c -= ( d - 100 );
			}

			// Limit values to 0 and 100.
			return [limit(c), limit(d)];
		}

		return [c,d];
	}


// Event handling

	// Provide a clean event with standardized offset values.
	function fixEvent ( e ) {

		// Prevent scrolling and panning on touch events, while
		// attempting to slide. The tap event also depends on this.
		e.preventDefault();

		// Filter the event to register the type, which can be
		// touch, mouse or pointer. Offset changes need to be
		// made on an event specific basis.
		var  touch = e.type.indexOf('touch') === 0
			,mouse = e.type.indexOf('mouse') === 0
			,pointer = e.type.indexOf('pointer') === 0
			,x,y, event = e;

		// IE10 implemented pointer events with a prefix;
		if ( e.type.indexOf('MSPointer') === 0 ) {
			pointer = true;
		}

		// Get the originalEvent, if the event has been wrapped
		// by jQuery. Zepto doesn't wrap the event.
		if ( e.originalEvent ) {
			e = e.originalEvent;
		}

		if ( touch ) {
			// noUiSlider supports one movement at a time,
			// so we can select the first 'changedTouch'.
			x = e.changedTouches[0].pageX;
			y = e.changedTouches[0].pageY;
		}

		if ( mouse || pointer ) {

			// Polyfill the pageXOffset and pageYOffset
			// variables for IE7 and IE8;
			if( !pointer && window.pageXOffset === undefined ){
				window.pageXOffset = document.documentElement.scrollLeft;
				window.pageYOffset = document.documentElement.scrollTop;
			}

			x = e.clientX + window.pageXOffset;
			y = e.clientY + window.pageYOffset;
		}

		event.points = [x, y];
		event.cursor = mouse;

		return event;
	}


// DOM additions

	// Append a handle to the base.
	function addHandle ( direction, index ) {

		var handle = $('<div><div/></div>').addClass( Classes[2] ),
			additions = [ '-lower', '-upper' ];

		if ( direction ) {
			additions.reverse();
		}

		handle.children().addClass(
			Classes[3] + " " + Classes[3]+additions[index]
		);

		return handle;
	}

	// Add the proper connection classes.
	function addConnection ( connect, target, handles ) {

		// Apply the required connection classes to the elements
		// that need them. Some classes are made up for several
		// segments listed in the class list, to allow easy
		// renaming and provide a minor compression benefit.
		switch ( connect ) {
			case 1:	target.addClass( Classes[7] );
					handles[0].addClass( Classes[6] );
					break;
			case 3: handles[1].addClass( Classes[6] );
					/* falls through */
			case 2: handles[0].addClass( Classes[7] );
					/* falls through */
			case 0: target.addClass(Classes[6]);
					break;
		}
	}

	// Add handles to the slider base.
	function addHandles ( nrHandles, direction, base ) {

		var index, handles = [];

		// Append handles.
		for ( index = 0; index < nrHandles; index += 1 ) {

			// Keep a list of all added handles.
			handles.push( addHandle( direction, index ).appendTo(base) );
		}

		return handles;
	}

	// Initialize a single slider.
	function addSlider ( direction, orientation, target ) {

		// Apply classes and data to the target.
		target.addClass([
			Classes[0],
			Classes[8 + direction],
			Classes[4 + orientation]
		].join(' '));

		return $('<div/>').appendTo(target).addClass( Classes[1] );
	}

function closure ( target, options, originalOptions ){

// Internal variables

	// All variables local to 'closure' are marked $.
	var $Target = $(target),
		$Locations = [-1, -1],
		$Base,
		$Handles,
		$Spectrum = options.spectrum,
		$Values = [],
	// libLink. For rtl sliders, 'lower' and 'upper' should not be inverted
	// for one-handle sliders, so trim 'upper' it that case.
		triggerPos = ['lower', 'upper'].slice(0, options.handles);

	// Invert the libLink connection for rtl sliders.
	if ( options.dir ) {
		triggerPos.reverse();
	}

// Helpers

	// Shorthand for base dimensions.
	function baseSize ( ) {
		return $Base[['width', 'height'][options.ort]]();
	}

	// External event handling
	function fireEvents ( events ) {

		// Use the external api to get the values.
		// Wrap the values in an array, as .trigger takes
		// only one additional argument.
		var index, values = [ $Target.val() ];

		for ( index = 0; index < events.length; index += 1 ){
			$Target.trigger(events[index], values);
		}
	}

	// Returns the input array, respecting the slider direction configuration.
	function inSliderOrder ( values ) {

		// If only one handle is used, return a single value.
		if ( values.length === 1 ){
			return values[0];
		}

		if ( options.dir ) {
			return values.reverse();
		}

		return values;
	}

// libLink integration

	// Create a new function which calls .val on input change.
	function createChangeHandler ( trigger ) {
		return function ( ignore, value ){
			// Determine which array position to 'null' based on 'trigger'.
			$Target.val( [ trigger ? null : value, trigger ? value : null ], true );
		};
	}

	// Called by libLink when it wants a set of links updated.
	function linkUpdate ( flag ) {

		var trigger = $.inArray(flag, triggerPos);

		// The API might not have been set yet.
		if ( $Target[0].linkAPI && $Target[0].linkAPI[flag] ) {
			$Target[0].linkAPI[flag].change(
				$Values[trigger],
				$Handles[trigger].children(),
				$Target
			);
		}
	}

	// Called by libLink to append an element to the slider.
	function linkConfirm ( flag, element ) {

		// Find the trigger for the passed flag.
		var trigger = $.inArray(flag, triggerPos);

		// If set, append the element to the handle it belongs to.
		if ( element ) {
			element.appendTo( $Handles[trigger].children() );
		}

		// The public API is reversed for rtl sliders, so the changeHandler
		// should not be aware of the inverted trigger positions.
		// On rtl slider with one handle, 'lower' should be used.
		if ( options.dir && options.handles > 1 ) {
			trigger = trigger === 1 ? 0 : 1;
		}

		return createChangeHandler( trigger );
	}

	// Place elements back on the slider.
	function reAppendLink ( ) {

		var i, flag;

		// The API keeps a list of elements: we can re-append them on rebuild.
		for ( i = 0; i < triggerPos.length; i += 1 ) {
			if ( this.linkAPI && this.linkAPI[(flag = triggerPos[i])] ) {
				this.linkAPI[flag].reconfirm(flag);
			}
		}
	}

	target.LinkUpdate = linkUpdate;
	target.LinkConfirm = linkConfirm;
	target.LinkDefaultFormatter = options.format;
	target.LinkDefaultFlag = 'lower';

	target.reappend = reAppendLink;


	// Handler for attaching events trough a proxy.
	function attach ( events, element, callback, data ) {

		// This function can be used to 'filter' events to the slider.

		// Add the noUiSlider namespace to all events.
		events = events.replace( /\s/g, namespace + ' ' ) + namespace;

		// Bind a closure on the target.
		return element.on( events, function( e ){

			// jQuery and Zepto (1) handle unset attributes differently,
			// but always falsy; #208
			if ( !!$Target.attr('disabled') ) {
				return false;
			}

			// Stop if an active 'tap' transition is taking place.
			if ( $Target.hasClass( Classes[14] ) ) {
				return false;
			}

			e = fixEvent(e);
			e.calcPoint = e.points[ options.ort ];

			// Call the event handler with the event [ and additional data ].
			callback ( e, data );
		});
	}

	// Handle movement on document for handle and range drag.
	function move ( event, data ) {

		var handles = data.handles || $Handles, positions, state = false,
			proposal = ((event.calcPoint - data.start) * 100) / baseSize(),
			h = handles[0][0] !== $Handles[0][0] ? 1 : 0;

		// Calculate relative positions for the handles.
		positions = getPositions( proposal, data.positions, handles.length > 1);

		state = setHandle ( handles[0], positions[h], handles.length === 1 );

		if ( handles.length > 1 ) {
			state = setHandle ( handles[1], positions[h?0:1], false ) || state;
		}

		// Fire the 'slide' event if any handle moved.
		if ( state ) {
			fireEvents(['slide']);
		}
	}

	// Unbind move events on document, call callbacks.
	function end ( event ) {

		// The handle is no longer active, so remove the class.
		$('.' + Classes[15]).removeClass(Classes[15]);

		// Remove cursor styles and text-selection events bound to the body.
		if ( event.cursor ) {
			$('body').css('cursor', '').off( namespace );
		}

		// Unbind the move and end events, which are added on 'start'.
		doc.off( namespace );

		// Remove dragging class.
		$Target.removeClass(Classes[12]);

		// Fire the change and set events.
		fireEvents(['set', 'change']);
	}

	// Bind move events on document.
	function start ( event, data ) {

		// Mark the handle as 'active' so it can be styled.
		if( data.handles.length === 1 ) {
			data.handles[0].children().addClass(Classes[15]);
		}

		// A drag should never propagate up to the 'tap' event.
		event.stopPropagation();

		// Attach the move event.
		attach ( actions.move, doc, move, {
			start: event.calcPoint,
			handles: data.handles,
			positions: [
				$Locations[0],
				$Locations[$Handles.length - 1]
			]
		});

		// Unbind all movement when the drag ends.
		attach ( actions.end, doc, end, null );

		// Text selection isn't an issue on touch devices,
		// so adding cursor styles can be skipped.
		if ( event.cursor ) {

			// Prevent the 'I' cursor and extend the range-drag cursor.
			$('body').css('cursor', $(event.target).css('cursor'));

			// Mark the target with a dragging state.
			if ( $Handles.length > 1 ) {
				$Target.addClass(Classes[12]);
			}

			// Prevent text selection when dragging the handles.
			$('body').on('selectstart' + namespace, false);
		}
	}

	// Move closest handle to tapped location.
	function tap ( event ) {

		var location = event.calcPoint, total = 0, to;

		// The tap event shouldn't propagate up and cause 'edge' to run.
		event.stopPropagation();

		// Add up the handle offsets.
		$.each( $Handles, function(){
			total += this.offset()[ options.style ];
		});

		// Find the handle closest to the tapped position.
		total = ( location < total/2 || $Handles.length === 1 ) ? 0 : 1;

		location -= $Base.offset()[ options.style ];

		// Calculate the new position.
		to = ( location * 100 ) / baseSize();

		if ( !options.events.snap ) {
			// Flag the slider as it is now in a transitional state.
			// Transition takes 300 ms, so re-enable the slider afterwards.
			addClassFor( $Target, Classes[14], 300 );
		}

		// Find the closest handle and calculate the tapped point.
		// The set handle to the new position.
		setHandle( $Handles[total], to );

		fireEvents(['slide', 'set', 'change']);

		if ( options.events.snap ) {
			start(event, { handles: [$Handles[total]] });
		}
	}

	// Attach events to several slider parts.
	function events ( behaviour ) {

		var i, drag;

		// Attach the standard drag event to the handles.
		if ( !behaviour.fixed ) {

			for ( i = 0; i < $Handles.length; i += 1 ) {

				// These events are only bound to the visual handle
				// element, not the 'real' origin element.
				attach ( actions.start, $Handles[i].children(), start, {
					handles: [ $Handles[i] ]
				});
			}
		}

		// Attach the tap event to the slider base.
		if ( behaviour.tap ) {

			attach ( actions.start, $Base, tap, {
				handles: $Handles
			});
		}

		// Make the range dragable.
		if ( behaviour.drag ){

			drag = $Base.find( '.' + Classes[7] ).addClass( Classes[10] );

			// When the range is fixed, the entire range can
			// be dragged by the handles. The handle in the first
			// origin will propagate the start event upward,
			// but it needs to be bound manually on the other.
			if ( behaviour.fixed ) {
				drag = drag.add($Base.children().not( drag ).children());
			}

			attach ( actions.start, drag, start, {
				handles: $Handles
			});
		}
	}


	// Test suggested values and apply margin, step.
	function setHandle ( handle, to, noLimitOption ) {

		var trigger = handle[0] !== $Handles[0][0] ? 1 : 0,
			lowerMargin = $Locations[0] + options.margin,
			upperMargin = $Locations[1] - options.margin,
			lowerLimit = $Locations[0] + options.limit,
			upperLimit = $Locations[1] - options.limit;

		// For sliders with multiple handles,
		// limit movement to the other handle.
		// Apply the margin option by adding it to the handle positions.
		if ( $Handles.length > 1 ) {
			to = trigger ? Math.max( to, lowerMargin ) : Math.min( to, upperMargin );
		}

		// The limit option has the opposite effect, limiting handles to a
		// maximum distance from another. Limit must be > 0, as otherwise
		// handles would be unmoveable. 'noLimitOption' is set to 'false'
		// for the .val() method, except for pass 4/4.
		if ( noLimitOption !== false && options.limit && $Handles.length > 1 ) {
			to = trigger ? Math.min ( to, lowerLimit ) : Math.max( to, upperLimit );
		}

		// Handle the step option.
		to = $Spectrum.getStep( to );

		// Limit to 0/100 for .val input, trim anything beyond 7 digits, as
		// JavaScript has some issues in its floating point implementation.
		to = limit(parseFloat(to.toFixed(7)));

		// Return false if handle can't move.
		if ( to === $Locations[trigger] ) {
			return false;
		}

		// Set the handle to the new position.
		handle.css( options.style, to + '%' );

		// Force proper handle stacking
		if ( handle.is(':first-child') ) {
			handle.toggleClass(Classes[17], to > 50 );
		}

		// Update locations.
		$Locations[trigger] = to;

		// Convert the value to the slider stepping/range.
		$Values[trigger] = $Spectrum.fromStepping( to );

		linkUpdate(triggerPos[trigger]);

		return true;
	}

	// Loop values from value method and apply them.
	function setValues ( count, values ) {

		var i, trigger, to;

		// With the limit option, we'll need another limiting pass.
		if ( options.limit ) {
			count += 1;
		}

		// If there are multiple handles to be set run the setting
		// mechanism twice for the first handle, to make sure it
		// can be bounced of the second one properly.
		for ( i = 0; i < count; i += 1 ) {

			trigger = i%2;

			// Get the current argument from the array.
			to = values[trigger];

			// Setting with null indicates an 'ignore'.
			// Inputting 'false' is invalid.
			if ( to !== null && to !== false ) {

				// If a formatted number was passed, attemt to decode it.
				if ( typeof to === 'number' ) {
					to = String(to);
				}

				to = options.format.from( to );

				// Request an update for all links if the value was invalid.
				// Do so too if setting the handle fails.
				if ( to === false || isNaN(to) || setHandle( $Handles[trigger], $Spectrum.toStepping( to ), i === (3 - options.dir) ) === false ) {

					linkUpdate(triggerPos[trigger]);
				}
			}
		}
	}

	// Set the slider value.
	function valueSet ( input ) {

		// LibLink: don't accept new values when currently emitting changes.
		if ( $Target[0].LinkIsEmitting ) {
			return this;
		}

		var count, values = asArray( input );

		// The RTL settings is implemented by reversing the front-end,
		// internal mechanisms are the same.
		if ( options.dir && options.handles > 1 ) {
			values.reverse();
		}

		// Animation is optional.
		// Make sure the initial values where set before using animated
		// placement. (no report, unit testing);
		if ( options.animate && $Locations[0] !== -1 ) {
			addClassFor( $Target, Classes[14], 300 );
		}

		// Determine how often to set the handles.
		count = $Handles.length > 1 ? 3 : 1;

		if ( values.length === 1 ) {
			count = 1;
		}

		setValues ( count, values );

		// Fire the 'set' event. As of noUiSlider 7,
		// this is no longer optional.
		fireEvents(['set']);

		return this;
	}

	// Get the slider value.
	function valueGet ( ) {

		var i, retour = [];

		// Get the value from all handles.
		for ( i = 0; i < options.handles; i += 1 ){
			retour[i] = options.format.to( $Values[i] );
		}

		return inSliderOrder( retour );
	}

	// Destroy the slider and unbind all events.
	function destroyTarget ( ) {

		// Unbind events on the slider, remove all classes and child elements.
		$(this).off(namespace)
			.removeClass(Classes.join(' '))
			.empty();

		delete this.LinkUpdate;
		delete this.LinkConfirm;
		delete this.LinkDefaultFormatter;
		delete this.LinkDefaultFlag;
		delete this.reappend;
		delete this.vGet;
		delete this.vSet;
		delete this.getCurrentStep;
		delete this.getInfo;
		delete this.destroy;

		// Return the original options from the closure.
		return originalOptions;
	}

	// Get the current step size for the slider.
	function getCurrentStep ( ) {

		// Check all locations, map them to their stepping point.
		// Get the step point, then find it in the input list.
		var retour = $.map($Locations, function( location, index ){

			var step = $Spectrum.getApplicableStep( location ),

				// As per #391, the comparison for the decrement step can have some rounding issues.
				// Round the value to the precision used in the step.
				stepDecimals = countDecimals(String(step[2])),

				// Get the current numeric value
				value = $Values[index],

				// To move the slider 'one step up', the current step value needs to be added.
				// Use null if we are at the maximum slider value.
				increment = location === 100 ? null : step[2],

				// Going 'one step down' might put the slider in a different sub-range, so we
				// need to switch between the current or the previous step.
				prev = Number((value - step[2]).toFixed(stepDecimals)),

				// If the value fits the step, return the current step value. Otherwise, use the
				// previous step. Return null if the slider is at its minimum value.
				decrement = location === 0 ? null : (prev >= step[1]) ? step[2] : (step[0] || false);

			return [[decrement, increment]];
		});

		// Return values in the proper order.
		return inSliderOrder( retour );
	}

	// Get the original set of options.
	function getOriginalOptions ( ) {
		return originalOptions;
	}


// Initialize slider

	// Throw an error if the slider was already initialized.
	if ( $Target.hasClass(Classes[0]) ) {
		throw new Error('Slider was already initialized.');
	}

	// Create the base element, initialise HTML and set classes.
	// Add handles and links.
	$Base = addSlider( options.dir, options.ort, $Target );
	$Handles = addHandles( options.handles, options.dir, $Base );

	// Set the connect classes.
	addConnection ( options.connect, $Target, $Handles );

	// Attach user events.
	events( options.events );

// Methods

	target.vSet = valueSet;
	target.vGet = valueGet;
	target.destroy = destroyTarget;

	target.getCurrentStep = getCurrentStep;
	target.getOriginalOptions = getOriginalOptions;

	target.getInfo = function(){
		return [
			$Spectrum,
			options.style,
			options.ort
		];
	};

	// Use the public value method to set the start values.
	$Target.val( options.start );

}


	// Run the standard initializer
	function initialize ( originalOptions ) {

		// Test the options once, not for every slider.
		var options = testOptions( originalOptions, this );

		// Loop all items, and provide a new closed-scope environment.
		return this.each(function(){
			closure(this, options, originalOptions);
		});
	}

	// Destroy the slider, then re-enter initialization.
	function rebuild ( options ) {

		return this.each(function(){

			// The rebuild flag can be used if the slider wasn't initialized yet.
			if ( !this.destroy ) {
				$(this).noUiSlider( options );
				return;
			}

			// Get the current values from the slider,
			// including the initialization options.
			var values = $(this).val(), originalOptions = this.destroy(),

				// Extend the previous options with the newly provided ones.
				newOptions = $.extend( {}, originalOptions, options );

			// Run the standard initializer.
			$(this).noUiSlider( newOptions );

			// Place Link elements back.
			this.reappend();

			// If the start option hasn't changed,
			// reset the previous values.
			if ( originalOptions.start === newOptions.start ) {
				$(this).val(values);
			}
		});
	}

	// Access the internal getting and setting methods based on argument count.
	function value ( ) {
		return this[0][ !arguments.length ? 'vGet' : 'vSet' ].apply(this[0], arguments);
	}

	// Override the .val() method. Test every element. Is it a slider? Go to
	// the slider value handling. No? Use the standard method.
	// Note how $.fn.val expects 'this' to be an instance of $. For convenience,
	// the above 'value' function does too.
	$.fn.val = function ( arg ) {

		// this === instanceof $

		function valMethod( a ){
			return a.hasClass(Classes[0]) ? value : $val;
		}

		// If no value is passed, this is 'get'.
		if ( !arguments.length ) {
			var first = $(this[0]);
			return valMethod(first).call(first);
		}

		var isFunction = $.isFunction(arg);

		// Return the set so it remains chainable. Make sure not to break
		// jQuery's .val(function( index, value ){}) signature.
		return this.each(function( i ){

			var val = arg, $t = $(this);

			if ( isFunction ) {
				val = arg.call(this, i, $t.val());
			}

			valMethod($t).call($t, val);
		});
	};

// Extend jQuery/Zepto with the noUiSlider method.
	$.fn.noUiSlider = function ( options, rebuildFlag ) {

		switch ( options ) {
			case 'step': return this[0].getCurrentStep();
			case 'options': return this[0].getOriginalOptions();
		}

		return ( rebuildFlag ? rebuild : initialize ).call(this, options);
	};

}( window.jQuery || window.Zepto ));

var $ = jQuery;
(function(gmr) {
	var ggComObj, stream, tracker = null;

	// Nielsen SDK event codes:
	//  5 - play
	//  6 - pause
	//  7 - stop
	//  8 - position change
	//  9 - mute
	// 10 - fullscreen
	// 11 - volume change
	// 15 - load Metadata
	// 49 - set Playhead Position
	// 55 - timed Metadata

	window.bindNielsenSDKEvents = function(beacon, player) {
		var hasAddEventListener = player.addEventListener ? true : false,
			events = {
				'track-cue-point': onTrackCuePoint,
				'ad-break-cue-point': onAdBreakCuePoint,
				'stream-status': onStreamStatus
			};

		stream = gmr.callsign;
		ggComObj = new NielsenSDKggCom(beacon, player);

		// register event listeners
		for (var event in events) {
			if (hasAddEventListener) {
				player.addEventListener(event, events[event]);
			} else {
				player.attachEvent(event, events[event]);
			}
		}

		// listen to stream change event
		if (hasAddEventListener) {
			document.addEventListener('live-player-stream-changed', onStreamChanged);
		} else {
			document.attachEvent('live-player-stream-changed', onStreamChanged);
		}
	};

	function NielsenSDKggCom(beacon, player) {
		var that = this;

		that.gg = beacon;
		that.player = player;
		that.is_playing = false;
	}

	var onStreamChanged = function(e) {
		debug('Stream has been changed to ' + e.detail);
		stream = e.detail;
	};

	var onStreamStatus = function(e) {
		debug('onStreamStatus: ' + e.data.code + ' ' + Date.now());
		if (e.data.code === 'LIVE_PAUSE' || e.data.code === 'LIVE_STOP') {
			onStreamStop();
		}
		if (e.data.code === 'LIVE_PLAYING') {

			if (!ggComObj.is_playing) {

				debug('Send now playing metadata event to Nielsen SDK.');
				ggComObj.gg.ggPM(15, {
					dataSrc: 'cms',
					assetid: stream,
					type: 'radio',
					provider: 'GreaterMedia',
					stationType: 1
				});

				trackPlayheadPosition();

				ggComObj.is_playing = true;

			}
		}
	};

	var trackPlayheadPosition = function() {
		if (!tracker) {
			tracker = setInterval(function() {
				debug('Send playhead position event to Nielsen SDK.');
				ggComObj.gg.ggPM(49, Date.now() / 1000);
			}, 9500);
		}
	};

	var onAdBreakCuePoint = function(e) {
		var data = e.data.adBreakData;

		if (ggComObj.is_playing) {
			onStreamStop();
		}

		debug('Send ad block cue metadata event to Nielsen SDK.');
		ggComObj.gg.ggPM(15, {
			dataSrc: 'cms',
			assetid: stream,
			title: data.cueTitle,
			length: data.duration / 1000, // convert to seconds
			type: 'radio',
			provider: 'GreaterMedia',
			stationType: 1
		});

		trackPlayheadPosition();

		ggComObj.is_playing = true;
	};

	var onTrackCuePoint = function(e) {
		var data = e.data.cuePoint;

		if (ggComObj.is_playing) {
			onStreamStop();
		}

		debug('Send track cue metadata event to Nielsen SDK.');
		ggComObj.gg.ggPM(15, {
			dataSrc: 'cms',
			assetid: stream,
			title: data.cueTitle,
			length: data.cueTimeDuration,
			type: 'radio',
			provider: 'GreaterMedia',
			stationType: 1
		});

		trackPlayheadPosition();

		ggComObj.is_playing = true;
	};

	var onStreamStop = function() {
		if (ggComObj.is_playing) {
			debug('Send stop event to Nielsen SDK.');

			ggComObj.gg.ggPM(7, Date.now() / 1000);
			ggComObj.is_playing = false;

			if (tracker) {
				clearInterval(tracker);
				tracker = null;
			}
		}
	};

	var debug = function(info) {
		if (gmr.debug && console) {
			console.log(info);
		}
	};
})(gmr);

(function($, window, undefined) {
	"use strict";

	// variables
	var document = window.document,
		body = document.querySelectorAll('body'),
		playButton = $('#playButton'),
		pauseButton = $('#pauseButton'),
		resumeButton = $('#resumeButton'),
		listenNow = $('#live-stream__listen-now'),
		listenLogin = $('#live-stream__login'),
		accountLogin = $('.header__account--btn');

	/**
	 *
	 * Pjax is running against the DOM. By default pjax detects a click event, and this case, we are targeting all `a`
	 * links in the `.main` element. This will run pjax against the first link clicked. After the initial link is
	 * clicked, pjax will stop.
	 *
	 * It is important to call pjax against the `.main` element. Initially we used `.page-wrap` but this caused elements
	 * that had click events attached to them to not function.
	 *
	 * To prevent pjax from stopping, we introduce some pjax `options`.
	 * The `fragment` allows for pjax to continue to detect clicks within the same element, in this case `.main`,
	 * that we initially are calling pjax against. This ensures that pjax continues to run.
	 * `maxCacheLength` is the maximum cache size for the previous container contents.
	 * `timeout` is the ajax timeout in milliseconds after which a full refresh is forced.
	 *
	 * If a user is logged into WordPress, pjax will not work. To resolve this, we run a check that is part of the `else
	 * if` statement that runs a localized variable from the PHP Class `GMLP_Player` in the Greater Media Live player
	 * plugin folder>includes>class-gmlp-player.php. This variable is `gmlp.logged_in` and checks if a user is logged
	 * in with WordPress. If a user is logged in with WordPress, we change the element that pjax is targeting to
	 * `.page-wrap`.
	 *
	 * @summary Detects if a user is authenticated with Gigya, then runs pjax against `a` links in `.page-wrap`
	 *
	 * @event click
	 * @fires pjax
	 *
	 * @see https://github.com/defunkt/jquery-pjax
	 */
	function pjaxInit() {
		if (is_gigya_user_logged_in()) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		} else if (gmr.wpLoggedIn) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.page-wrap', {
					'fragment': '.page-wrap',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		} else {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		}
	}

	playButton.on('click', function() {
		pjaxInit();
	});

	resumeButton.on('click', function() {
		pjaxInit();
	});

	playButton.on( 'click', function() {
		if( !is_gigya_user_logged_in() ) {
			Cookies.set('gmlp_play_button_pushed', 1);
			Cookies.set('gmr_play_live_audio', 0);
		}
	});

	listenLogin.on( 'click', function() {
		if( !is_gigya_user_logged_in() ) {
			Cookies.set('gmlp_play_button_pushed', 1);
			Cookies.set('gmr_play_live_audio', 0);
		}
	});

	listenNow.on( 'click', function() {
		if( !is_gigya_user_logged_in() ) {
			Cookies.set('gmlp_play_button_pushed', 1);
			Cookies.set('gmr_play_live_audio', 0);
		}
	});

	accountLogin.on( 'click', function() {
		if( !is_gigya_user_logged_in() ) {
			Cookies.set('gmr_play_live_audio', 0);
		}
	});
})(jQuery, window);
/* global: gigya_profile_path */
(function ($, window, undefined) {
	"use strict";

	var tech = getUrlVars()['tech'] || 'html5_flash';
	var aSyncCuePointFallback = getUrlVars()['aSyncCuePointFallback'] == 'false' ? false : true;

	var player;
	/* TD player instance */

	var playingCustomAudio = false;
	/* This will be true if we're playing a custom audio file vs. live stream */
	var customAudio = false;
	/* Will be an HTML5 Audio object, if we support it */
	var customArtist, customTrack, customHash; // So we can re-add these when resuming via live-player
	var playingLiveAudio = false; // This will be true if we're playing live audio from the live stream

	var adPlaying;
	/* boolean - Ad break currently playing */
	var currentTrackCuePoint;
	/* Current Track */
	var livePlaying;
	/* boolean - Live stream currently playing */
	var song;
	/* Song object that wraps NPE data */
	var companions;
	/* VAST companion banner object */
	var currentStation = '';
	/* String - Current station played */

	var body = document.querySelector('body');
	var tdContainer = document.getElementById('td_container');
	var livePlayer = document.getElementById('live-player');
	var liveStreamPlayer = document.querySelector('.live-stream__player');
	var playBtn = document.getElementById('playButton');
	var pauseBtn = document.getElementById('pauseButton');
	var resumeBtn = document.getElementById('resumeButton');
	var loadingBtn = document.getElementById('loadButton');
	var podcastPlayBtn = document.querySelector('.podcast__btn--play');
	var podcastPauseBtn = document.querySelector('.podcast__btn--pause');
	var podcastPlayer = document.querySelector('.podcast-player');
	var listenNow = document.getElementById('live-stream__listen-now');
	var nowPlaying = document.getElementById('live-stream__now-playing');
	var listenLogin = document.getElementById('live-stream__login');
	var $trackInfo = $(document.getElementById('trackInfo'));
	var gigyaLogin = gigya_profile_path('login');
	var clearDebug = document.getElementById('clearDebug');
	var onAir = document.getElementById('on-air');
	var streamStatus = document.getElementById('live-stream__status');
	var nowPlayingInfo = document.getElementById('nowPlaying');
	var trackInfo = document.getElementById('trackInfo');
	var liveStreamSelector = document.querySelector('.live-player__stream');
	var inlineAudioInterval = null;
	var liveStreamInterval = null;
	var footer = document.querySelector('.footer');
	var lpInit = false;
	var volume_slider = $(document.getElementById('live-player--volume'));
	var global_volume = 1;

	/**
	 * function to detect if the current browser can use `addEventListener`, if not, use `attachEvent`
	 * this is a specific fix for IE8
	 *
	 * @param elem
	 * @param eventType
	 * @param handler
	 */
	function addEventHandler(elem, eventType, handler) {
		if (elem != null) {
			if (elem.addEventListener) {
				elem.addEventListener(eventType, handler, false);
			} else if (elem.attachEvent) {
				elem.attachEvent('on' + eventType, handler);
			}
		}
	}

	/**
	 * Starts an interval timer for when the live stream is playing
	 * Broadcasts an event every `audioIntervalDuration`
	 */
	function startLiveStreamInterval() {
		var interval = gmr.intervals.live_streaming;

		if (interval > 0) {
			debug('Live stream interval set');

			liveStreamInterval = setInterval(function () {
				$(body).trigger('liveStreamPlaying.gmr');
				debug('Live stream interval reached');
			}, interval * 60 * 1000);
		}
	}

	/**
	 * Starts an interval timer for when inline audio is playing
	 * Broadcasts an event every `audioIntervalDuration`
	 */
	function startInlineAudioInterval() {
		var interval = gmr.intervals.inline_audio;

		if (interval > 0) {
			debug('Inline audio interval set');

			inlineAudioInterval = setInterval(function () {
				$(body).trigger('inlineAudioPlaying.gmr');
				debug('Inline audio interval reached');
			}, interval * 60 * 1000);
		}
	}

	/**
	 * Stops the live stream interval timer
	 * Should be called whenever live stream goes from playing to not playing
	 */
	function stopLiveStreamInterval() {
		clearInterval(liveStreamInterval);
		debug('Live stream interval off');
	}

	/**
	 * Stops the inline audio interval timer
	 * Should be called whenever inline audio goes from playing to not playing (including paused)
	 */
	function stopInlineAudioInterval() {
		clearInterval(inlineAudioInterval);
		debug('Inline audio interval off');
	}

	/**
	 * @todo remove the console log before beta
	 */
	window.tdPlayerApiReady = function () {
		debug("--- TD Player API Loaded ---");
		initPlayer();
	};

	function calcTechPriority() {
		if (bowser.firefox) {
			return ['Flash', 'Html5'];
		} else if (bowser.safari) {
			return ['Html5', 'Flash'];
		} else if (bowser.chrome) {
			return ['Html5', 'Flash'];
		} else {
			return ['Html5', 'Flash'];
		}
	}

	function initPlayer() {
		var techPriority = calcTechPriority();
		debug('+++ initPlayer - techPriority = ' + techPriority.join(', '));

		/* TD player configuration object used to create player instance */
		var tdPlayerConfig = {
			coreModules: [
				{
					id: 'MediaPlayer',
					playerId: 'td_container',
					isDebug: false,
					techPriority: techPriority,
					timeShift: { // timeShifting is currently available on Flash only. Leaving for HTML5 future
						active: 0, /* 1 = active, 0 = inactive */
						max_listening_time: 35 /* If max_listening_time is undefined, the default value will be 30 minutes */
					},
					// set geoTargeting to false on devices in order to remove the daily geoTargeting in browser
					geoTargeting: {desktop: {isActive: false}, iOS: {isActive: false}, android: {isActive: false}},
					plugins: [{id: "vastAd"}]
				},
				{id: 'NowPlayingApi'},
				{id: 'Npe'},
				{id: 'PlayerWebAdmin'},
				{id: 'SyncBanners', elements: [{id: 'td_synced_bigbox', width: 300, height: 250}]},
				{id: 'TargetSpot'}
			]
		};

		require(['tdapi/base/util/Companions'], function (Companions) {
				companions = new Companions();
			}
		);

		window.player = player = new TdPlayerApi(tdPlayerConfig);
		if (player.addEventListener) {
			player.addEventListener('player-ready', onPlayerReady);
			player.addEventListener('configuration-error', onConfigurationError);
			player.addEventListener('module-error', onModuleError);
		} else if (player.attachEvent) {
			player.attachEvent('player-ready', onPlayerReady);
			player.attachEvent('configuration-error', onConfigurationError);
			player.attachEvent('module-error', onModuleError);
		}
		player.loadModules();
	}

	/**
	 * DO NOT REMOVE THIS FUNCTION --- REQUIRED FOR TRITON API
	 *
	 * load TD Player API asynchronously
	 */
	function loadIdSync(station) {
		var scriptTag = document.createElement('script');
		scriptTag.setAttribute("type", "text/javascript");
		scriptTag.setAttribute("src", "//playerservices.live.streamtheworld.com/api/idsync.js?station=" + station);
		document.getElementsByTagName('head')[0].appendChild(scriptTag);
	}

	function initControlsUi() {

		if (pauseBtn != null) {
			addEventHandler(pauseBtn, 'click', pauseStream);
		}

		if (resumeBtn != null) {
			if ( is_gigya_user_logged_in() ) {
				addEventHandler(resumeBtn, 'click', resumeLiveStream);
			} else {
				addEventHandler(resumeBtn, 'click', function () {
					window.location.href = gigyaLogin;
				});
			}
		}

		if (clearDebug != null) {
			addEventHandler(clearDebug, 'click', clearDebugInfo);
		}

	}

	function setPlayingStyles() {
		if (null === tdContainer) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		tdContainer.classList.add('stream__active');
		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}
		if (!resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.add('live-player__muted');
		}
		if (resumeBtn.classList.contains('resume__live')) {
			resumeBtn.classList.remove('resume__live');
		}
		if (true === playingCustomAudio) {
			nowPlaying.style.display = 'none';
			listenNow.style.display = 'inline-block';
		} else {
			nowPlaying.style.display = 'inline-block';
			listenNow.style.display = 'none';
		}
		if (false === playingCustomAudio && loadingBtn != null) {
			loadingBtn.classList.add('loading');
		}
		if (true === playingCustomAudio && pauseBtn != null) {
			if (pauseBtn.classList.contains('live-player__muted')) {
				pauseBtn.classList.remove('live-player__muted');
			}
		} else {
			pauseBtn.classList.add('live-player__muted');
		}

	}

	function setStoppedStyles() {
		if (null === tdContainer) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}
		listenNow.style.display = 'inline-block';
		nowPlaying.style.display = 'none';
		pauseBtn.classList.add('live-player__muted');
	}

	function setPausedStyles() {
		if (null === tdContainer) {
			// gigya user is logged out, so everything is different ಠ_ಠ - Should we force login for inline audio as well??
			return;
		}

		if (true === playingCustomAudio && window.innerWidth <= 767) {
			playBtn.classList.add('live-player__login');
		} else {
			playBtn.classList.add('live-player__muted');
		}
		if (body.classList.contains('live-player--active')) {
			body.classList.remove('live-player--active');
		}
		listenNow.style.display = 'inline-block';
		nowPlaying.style.display = 'none';
		pauseBtn.classList.add('live-player__muted');
		if (resumeBtn.classList.contains('live-player__muted')) {
			resumeBtn.classList.remove('live-player__muted');
			resumeBtn.classList.add('resume__live');
		}
		resumeBtn.classList.add('resume__audio');
	}

	function setInlineAudioUX() {
		var audioTime = document.querySelectorAll('.audio__time'), i;

		for (i = 0; i < audioTime.length; ++i) {
			audioTime[i].classList.add('playing');
		}

		if (liveStreamPlayer != null) {
			liveStreamPlayer.classList.add('audio__playing');
		}

		if (streamStatus != null) {
			streamStatus.classList.add('audio__playing');
		}

		if (livePlayer != null) {
			livePlayer.classList.add('playing');
		}

		if (nowPlayingInfo != null) {
			nowPlayingInfo.classList.add('playing');
		}

		if (listenNow != null) {
			setTimeout(function () {
				listenNow.innerHTML = 'Switch to Live Stream';
			}, 1000);
		}
	}

	function nearestPodcastPlaying(event) {
		var eventTarget = event.target;
		var $podcastPlayer = $(eventTarget).parents('.podcast-player');
		var podcastCover = eventTarget.parentNode;
		var audioCurrent = podcastCover.nextElementSibling;
		var runtimeCurrent = audioCurrent.nextElementSibling;
		var audioTime = $podcastPlayer.find('.podcast__play .audio__time'), i;
		var runtime = document.querySelector('.podcast__runtime');
		var inlineCurrent = podcastCover.parentNode;
		var inlineMeta = inlineCurrent.nextElementSibling;
		var inlineTime = inlineMeta.querySelector('.audio__time');

		$('.playing__current').removeClass('playing__current');

		if (podcastPlayer != null && ( body.classList.contains('single-show') || body.classList.contains('post-type-archive-podcast') || body.classList.contains('single-podcast') || body.classList.contains('home'))) {
			audioCurrent.classList.add('playing__current');
			runtimeCurrent.classList.add('playing');
		} else if (podcastPlayer != null && ! (body.classList.contains('single-show') || body.classList.contains('post-type-archive-podcast') || body.classList.contains('single-podcast'))) {
			audioCurrent.classList.add('playing__current');
			inlineTime.classList.add('playing__current');
		} else {
			for (i = 0; i < audioTime.length; ++i) {
				if (audioTime[i] != null) {
					audioTime[i].classList.add('playing');
					audioTime[i].classList.add('playing__current');
				}
			}
			runtime.classList.add('playing');
		}
	}

	function resetInlineAudioUX() {
		var audioTime = document.querySelectorAll('.audio__time'), i;
		var runtime = document.querySelectorAll('.podcast__runtime');

		for (i = 0; i < audioTime.length; ++i) {
			if (audioTime[i] != null && audioTime[i].classList.contains('playing')) {
				audioTime[i].classList.remove('playing');
			}
			if (audioTime[i] != null && audioTime[i].classList.contains('playing__current')) {
				audioTime[i].classList.remove('playing__current');
			}
		}

		if (liveStreamPlayer != null) {
			liveStreamPlayer.classList.remove('audio__playing');
		}

		if (streamStatus != null) {
			streamStatus.classList.remove('audio__playing');
		}

		if (livePlayer != null) {
			livePlayer.classList.remove('playing');
		}

		for (i = 0; i < runtime.length; ++i) {
			if (runtime[i] != null && runtime[i].classList.contains('playing')) {
				runtime[i].classList.remove('playing');
			}
		}

		if (nowPlayingInfo != null) {
			nowPlayingInfo.classList.remove('playing');
		}
	}

	function replaceNPInfo() {
		if (window.innerWidth <= 767) {
			if (trackInfo.innerHTML === '') {
				onAir.classList.add('on-air__npe');
				liveStreamSelector.classList.add('full__width');
			} else if (onAir.classList.contains('on-air__npe')) {
				onAir.classList.remove('on-air__npe');
				liveStreamSelector.classList.remove('full__width');
			}
		}
	}

	function addPlayBtnHeartbeat() {
		if (playBtn != null) {
			playBtn.classList.add('play-btn--heartbeat');
		}
		if (livePlayer != null) {
			livePlayer.classList.add('live-player--heartbeat');
		}
	}

	function removePlayBtnHeartbeat() {
		if (playBtn != null && playBtn.classList.contains('play-btn--heartbeat')) {
			playBtn.classList.remove('play-btn--heartbeat');
		}
		if (livePlayer != null && livePlayer.classList.contains('live-player--heartbeat')) {
			livePlayer.classList.remove('live-player--heartbeat');
		}
	}

	var listenLiveStopCustomInlineAudio = function () {
		var listenNowText = listenNow.textContent;
		var nowPlayingTitle = document.getElementById('trackInfo');
		var nowPlayingInfo = document.getElementById('npeInfo');

		if (true === playingCustomAudio) {
			customAudio.pause();
			nowPlayingTitle.innerHTML = '';
			nowPlayingInfo.innerHTML = '';
			resetInlineAudioStates();
			resetInlineAudioUX();
			playingCustomAudio = false;
			stopInlineAudioInterval();
		}
		if (listenNowText === 'Switch to Live Stream') {
			listenNow.innerHTML = 'Listen Live';
		}
		if (window.innerWidth >= 768) {
			playLiveStream();
		}
	};

	function setInitialPlay() {
		lpInit = 1;
		debug('-- Player Initialized By Click ---');
	}

	function setPlayerReady() {
		lpInit = true;
		debug('-- Player Ready to Go ---');
	}

	function playLiveStreamDevice() {
		if (is_gigya_user_logged_in() && lpInit === true) {
			setStoppedStyles();
			if (window.innerWidth >= 768) {
				playLiveStream();
			} else {
				playLiveStreamMobile();
			}
		}
	}

	function changePlayerState() {
		if (is_gigya_user_logged_in()) {
			if (playBtn != null) {
				addEventHandler(playBtn, 'click', function(){
					if (lpInit === true) {
						setStoppedStyles();
						if (window.innerWidth >= 768) {
							playLiveStream();
						} else {
							playLiveStreamMobile();
						}
					} else {
						setInitialPlay();
					}
				});
			}
			if (listenNow != null) {
				addEventHandler(listenNow, 'click', listenLiveStopCustomInlineAudio);
			}
		} else {
			if (playBtn != null) {
				addEventHandler(playBtn, 'click', function () {
					window.location.href = gigyaLogin;
					setPlayerReady();
				});
			}
			if (listenNow != null) {
				addEventHandler(listenNow, 'click', function () {
					window.location.href = gigyaLogin;
				});
			}
			if (listenLogin != null && window.innerWidth <= 767) {
				addEventHandler(listenLogin, 'click', function () {
					window.location.href = gigyaLogin;
				});
			}
			if (podcastPlayBtn != null) {
				 addEventHandler(podcastPlayBtn, 'click', pjaxInit);
			}
		}
	}

	$(document).ready(function () {
		changePlayerState();
	});

	function loggedInGigyaUser() {
		playLiveStreamDevice();
		Cookies.set("gmlp_play_button_pushed", 0);
	}

	function preVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		body.classList.add('vast-ad--playing');

		if (preRoll != null) {
			preRoll.classList.add('vast__pre-roll');
		}
	}

	function postVastAd() {
		var preRoll = document.getElementById('live-stream__container');

		if (body.classList.contains('vast-ad--playing')) {
			body.classList.remove('vast-ad--playing');
		}

		if (preRoll != null) {
			preRoll.classList.remove('vast__pre-roll');
		}
		Cookies.set('gmr_play_live_audio', undefined);
		Cookies.set('gmr_play_live_audio', 1, {expires: 86400});
	}

	function streamVastAd() {
		var vastUrl = gmr.streamUrl;

		detachAdListeners();
		attachAdListeners();

		player.stop();
		player.skipAd();
		player.playAd('vastAd', {url: vastUrl});
		setTimeout(function() {
			this.stop();
		}, 25000);
	}

	var currentStream = $('.live-player__stream--current-name');

	currentStream.bind('DOMSubtreeModified', function () {
		if ( is_gigya_user_logged_in() ) {
			debug('--- new stream select ---');
			var station = currentStream.text();

			if (livePlaying) {
				player.stop();
			}

			if (true === playingCustomAudio) {
				listenLiveStopCustomInlineAudio();
			}

			player.play({station: station, timeShift: true});

			livePlayer.classList.add('live-player--active');
			setPlayingStyles();
		} else {
			window.location.href = gigyaLogin;
		}
	});

	function playLiveStreamMobile() {
		var station = gmr.callsign;

		pjaxInit();
		if (station === '') {
			alert('Please enter a Station');
			return;
		}
		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}
		debug('playLiveStream - station=' + station);

		preVastAd();
		streamVastAd();
		if (player.addEventListener) {
			player.addEventListener('ad-playback-complete', function () {
				postVastAd();
				debug("--- ad complete ---");

				if (livePlaying) {
					player.stop();
				}

				body.classList.add('live-player--active');
				livePlayer.classList.add('live-player--active');
				player.play({station: station, timeShift: true});
				setPlayingStyles();
			});
		} else if (player.attachEvent) {
			player.attachEvent('ad-playback-complete', function () {
				postVastAd();
				debug("--- ad complete ---");

				if (livePlaying) {
					player.stop();
				}

				body.classList.add('live-player--active');
				livePlayer.classList.add('live-player--active');
				player.play({station: station, timeShift: true});
				setPlayingStyles();
			});
		}

	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamMobileNoAd() {
		var station = gmr.callsign;

		if (station === '') {
			alert('Please enter a Station');
			return;
		}
		if (true === playingCustomAudio) {
			listenLiveStopCustomInlineAudio();
		}
		debug('playLiveStream - station=' + station);

		if (livePlaying) {
			player.stop();
		}

		body.classList.add('live-player--active');
		livePlayer.classList.add('live-player--active');
		player.play({station: station, timeShift: true});
		setPlayingStyles();

	}

	function playLiveStream() {
		var station = gmr.callsign;

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {

			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			preVastAd();
			streamVastAd();
			if (player.addEventListener) {
				player.addEventListener('ad-playback-complete', function () {
					postVastAd();
					debug("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					body.classList.add('live-player--active');
					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
				});
			} else if (player.attachEvent) {
				player.attachEvent('ad-playback-complete', function () {
					postVastAd();
					debug("--- ad complete ---");

					if (livePlaying) {
						player.stop();
					}

					body.classList.add('live-player--active');
					livePlayer.classList.add('live-player--active');
					player.play({station: station, timeShift: true});
					setPlayingStyles();
				});
			}
		}
	}

	/**
	 * Temp to remove vast ad while issues are resolves
	 */
	function playLiveStreamNoAd() {
		var station = gmr.callsign;

		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {

			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			body.classList.add('live-player--active');
			livePlayer.classList.add('live-player--active');
			player.play({station: station, timeShift: true});
			setPlayingStyles();
		}
	}

	function resumeLiveStream() {
		pjaxInit();
		if (true === playingCustomAudio) {
			resumeCustomInlineAudio();

			setPlayingStyles();
		} else {
			var station = gmr.callsign;
			if (station === '') {
				alert('Please enter a Station');
				return;
			}

			debug('playLiveStream - station=' + station);

			if (livePlaying) {
				player.stop();
			}

			livePlayer.classList.add('live-player--active');
			player.play({station: station, timeShift: true});
			setPlayingStyles();
		}
	}

	function pauseStream() {
		if (true === playingCustomAudio) {
			pauseCustomInlineAudio();
			stopInlineAudioInterval();
		} else {
			playingLiveAudio = false;
			player.pause();
			stopLiveStreamInterval();
		}

		if (livePlayer.classList.contains('live-player--active')) {
			livePlayer.classList.remove('live-player--active');
		}
		setPausedStyles();
	}

	function loadNpApi() {
		if ($("#songHistoryCallsignUser").val() === '') {
			alert('Please enter a Callsign');
			return;
		}

		var isHd = ( $("#songHistoryConnectionTypeSelect").val() == 'hdConnection' );

		//Set the hd parameter to true if the station has AAC. Set it to false if the station has no AAC.
		player.NowPlayingApi.load({mount: $("#songHistoryCallsignUser").val(), hd: isHd, numberToFetch: 15});
	}

	function onPlayerReady() {
		//Return if MediaPlayer is not loaded properly...
		if (player.MediaPlayer === undefined) {
			return;
		}

		//Listen on companion-load-error event
		//companions.addEventListener("companion-load-error", onCompanionLoadError);
		initControlsUi();

		if (player.addEventListener) {
			player.addEventListener('track-cue-point', onTrackCuePoint);
			player.addEventListener('ad-break-cue-point', onAdBreak);
			player.addEventListener('stream-track-change', onTrackChange);
			player.addEventListener('hls-cue-point', onHlsCuePoint);

			player.addEventListener('stream-status', onStatus);
			player.addEventListener('stream-geo-blocked', onGeoBlocked);
			player.addEventListener('timeout-alert', onTimeOutAlert);
			player.addEventListener('timeout-reach', onTimeOutReach);
//			player.addEventListener('npe-song', onNPESong);

			player.addEventListener('stream-select', onStreamSelect);

			player.addEventListener('stream-start', onStreamStarted);
			player.addEventListener('stream-stop', onStreamStopped);
		} else if (player.attachEvent) {
			player.attachEvent('track-cue-point', onTrackCuePoint);
			player.attachEvent('ad-break-cue-point', onAdBreak);
			player.attachEvent('stream-track-change', onTrackChange);
			player.attachEvent('hls-cue-point', onHlsCuePoint);

			player.attachEvent('stream-status', onStatus);
			player.attachEvent('stream-geo-blocked', onGeoBlocked);
			player.attachEvent('timeout-alert', onTimeOutAlert);
			player.attachEvent('timeout-reach', onTimeOutReach);
//			player.attachEvent('npe-song', onNPESong);

			player.attachEvent('stream-select', onStreamSelect);

			player.attachEvent('stream-start', onStreamStarted);
			player.attachEvent('stream-stop', onStreamStopped);
		}

		player.setVolume(1);

		setStatus('Api Ready');
		if (lpInit === 1) {
			setPlayerReady();
			playLiveStreamDevice();
		} else if (Cookies.get('gmlp_play_button_pushed') === 1) {
			setPlayerReady();
			playLiveStreamDevice();
		} else {
			setPlayerReady();
		}
		if (window.innerWidth >= 768) {
			addPlayBtnHeartbeat();
			setTimeout(removePlayBtnHeartbeat, 2000);
		}
		setTech(player.MediaPlayer.tech.type);

		if (player.addEventListener) {
			player.addEventListener('list-loaded', onListLoaded);
			player.addEventListener('list-empty', onListEmpty);
			player.addEventListener('nowplaying-api-error', onNowPlayingApiError);
		} else if (player.attachEvent) {
			player.attachEvent('list-loaded', onListLoaded);
			player.attachEvent('list-empty', onListEmpty);
			player.attachEvent('nowplaying-api-error', onNowPlayingApiError);
		}

		$("#fetchSongHistoryByUserCallsignButton").click(function () {
			loadNpApi();
		});

		if (player.addEventListener) {
			player.addEventListener('pwa-data-loaded', onPwaDataLoaded);
		} else if (player.attachEvent) {
			player.attachEvent('pwa-data-loaded', onPwaDataLoaded);
		}

		$("#pwaButton").click(function () {
			loadPwaData();
		});

		$(document).ready(function() {
			var opted_out = window.get_gigya_user_field && get_gigya_user_field('nielsen_optout');
			if (!opted_out && window._nolggGlobalParams) {
				var beacon = new NOLCMB.ggInitialize(window._nolggGlobalParams);
				bindNielsenSDKEvents(beacon, player);
			}
		});

		if (bowser.ios) {
			livePlayer.classList.add('no-volume-control');
		} else {
			volume_slider.noUiSlider({
				start: getVolume(),
				range: {
					min: 0,
					max: 1
				}
			});

			volume_slider.on('slide', function () {
				global_volume = parseFloat(volume_slider.val());
				if (isNaN(global_volume)) {
					global_volume = 1;
				}

				if (livePlaying) {
					player.setVolume(global_volume);
				}

				if (customAudio) {
					customAudio.volume = global_volume;
				}

				if (typeof(localStorage) !== "undefined") {
					localStorage.setItem("gmr-live-player-volume", global_volume);
				}
			});
		}
	}

	/**
	 * Event fired in case the loading of the companion ad returned an error.
	 * @param e
	 */
	function onCompanionLoadError(e) {
		debug('tdplayer::onCompanionLoadError - containerId=' + e.containerId + ', adSpotUrl=' + e.adSpotUrl, true);
	}

	function onAdPlaybackStart(e) {
		adPlaying = true;
		setStatus('Advertising... Type=' + e.data.type);
	}

	function onAdPlaybackComplete(e) {
		adPlaying = false;
		$("#td_adserver_bigbox").empty();
		$("#td_adserver_leaderboard").empty();
		setStatus('Ready');
	}

	/**
	 * Custom function to handle when a vast ad fails. This runs when there is an `ad-playback-error` event.
	 *
	 * @param e
	 */
	function adError(e) {
		setStatus('Ready');

		postVastAd();
		var station = gmr.callsign;
		if (livePlaying) {
			player.stop();
		}

		livePlayer.classList.add('live-player--active');
		player.play({mount: station, timeShift: true});
		setPlayingStyles();
	}

	function onAdCountdown(e) {
		debug('Ad countdown : ' + e.data.countDown + ' second(s)');
	}

	function onVastProcessComplete(e) {
		debug('Vast Process complete');

		var vastCompanions = e.data.companions;

		//Load Vast Ad companion (bigbox & leaderbaord ads)
		displayVastCompanionAds(vastCompanions);
	}

	function onVpaidAdCompanions(e) {
		debug('Vpaid Ad Companions');

		//Load Vast Ad companion (bigbox & leaderbaord ads)
		displayVastCompanionAds(e.companions);
	}

	function displayVastCompanionAds(vastCompanions) {
		if (vastCompanions && vastCompanions.length > 0) {
			var bigboxIndex = -1;
			var leaderboardIndex = -1;

			$.each(vastCompanions, function (i, val) {
				if (parseInt(val.width) == 300 && parseInt(val.height) == 250) {
					bigboxIndex = i;
				} else if (parseInt(val.width) == 728 && parseInt(val.height) == 90) {
					leaderboardIndex = i;
				}
			});

			if (bigboxIndex > -1) {
				companions.loadVASTCompanionAd('td_adserver_bigbox', vastCompanions[bigboxIndex]);
			}

			if (leaderboardIndex > -1) {
				companions.loadVASTCompanionAd('td_adserver_leaderboard', vastCompanions[leaderboardIndex]);
			}
		}
	}

	function getVolume() {
		var volume = global_volume;

		if (typeof(localStorage) !== "undefined") {
			volume = localStorage.getItem("gmr-live-player-volume");
			if (volume === null) {
				volume = 1;
			} else {
				volume = parseFloat(volume);
				if (isNaN(volume)) {
					volume = 1;
				}
			}
		}

		return volume;
	}

	function onStreamStarted() {
		livePlaying = true;
		playingLiveAudio = true;

		if (loadingBtn.classList.contains('loading')) {
			loadingBtn.classList.remove('loading');
		}

		if (pauseBtn.classList.contains('live-player__muted')) {
			pauseBtn.classList.remove('live-player__muted');
		}

		startLiveStreamInterval();

		player.setVolume(getVolume());
	}

	function onStreamSelect() {
		$('#hasHQ').html(player.MediaPlayer.hasHQ().toString());
		$('#isHQ').html(player.MediaPlayer.isHQ().toString());

		$('#hasLow').html(player.MediaPlayer.hasLow().toString());
		$('#isLow').html(player.MediaPlayer.isLow().toString());
	}

	function onStreamStopped() {
		livePlaying = false;
		playingLiveAudio = false;

		clearNpe();
		$("#trackInfo").html('');
		$("#asyncData").html('');

		$('#hasHQ').html('N/A');
		$('#isHQ').html('N/A');

		$('#hasLow').html('N/A');
		$('#isLow').html('N/A');

		stopLiveStreamInterval();
	}

	function onTrackCuePoint(e) {
		debug('New Track cuepoint received');
		debug('Title: ' + e.data.cuePoint.cueTitle + ' - Artist: ' + e.data.cuePoint.artistName);

		if (currentTrackCuePoint && currentTrackCuePoint != e.data.cuePoint) {
			clearNpe();
		}

		if (e.data.cuePoint.nowplayingURL) {
			player.Npe.loadNpeMetadata(e.data.cuePoint.nowplayingURL, e.data.cuePoint.artistName, e.data.cuePoint.cueTitle);
		}

		currentTrackCuePoint = e.data.cuePoint;

		$("#trackInfo").html('<div class="now-playing__title">' + currentTrackCuePoint.cueTitle + '</div><div class="now-playing__artist">' + currentTrackCuePoint.artistName + '</div>');

		setTimeout(replaceNPInfo, 10000);
		$(body).trigger("liveAudioTrack.gmr");
	}

	function onTrackChange(e) {
		debug('Stream Track has changed');
		debug('Codec:' + e.data.cuePoint.audioTrack.codec() + ' - Bitrate:' + e.data.cuePoint.audioTrack.bitRate());
	}

	function onHlsCuePoint(e) {
		debug('New HLS cuepoint received');
		debug('Track Id:' + e.data.cuePoint.hlsTrackId + ' SegmentId:' + e.data.cuePoint.hlsSegmentId);
	}

	function onAdBreak(e) {
		setStatus('Commercial break...');
	}

	function clearNpe() {
		$("#npeInfo").html('');
		$("#asyncData").html('');
	}

	//Song History
	function onListLoaded(e) {
		debug('Song History loaded');

		$("#asyncData").html('<br><p><span class="label label-warning">Song History:</span>');

		var tableContent = '<table class="table table-striped"><thead><tr><th>Song title</th><th>Artist name</th><th>Time</th></tr></thead>';

		var time;
		$.each(e.data.list, function (index, item) {
			time = new Date(Number(item.cueTimeStart));
			tableContent += "<tr><td>" + item.cueTitle + "</td><td>" + item.artistName + "</td><td>" + time.toLocaleTimeString() + "</td></tr>";
		});

		tableContent += "</table></p>";

		$("#asyncData").html("<div>" + tableContent + "</div>");
	}

	//Song History empty
	function onListEmpty(e) {
		$("#asyncData").html('<br><p><span class="label label-important">Song History is empty</span>');
	}

	function onNowPlayingApiError(e) {
		debug('Song History loading error', true);

		$("#asyncData").html('<br><p><span class="label label-important">Song History error</span>');
	}

	function onTimeOutAlert(e) {
		debug('Time Out Alert');
	}

	function onTimeOutReach(e) {
		debug('Time Out Reached');
	}

	function onConfigurationError(e) {
		debug('Configuration error', true);
	}

	function onModuleError(object) {
		var message = '';

		$.each(object.data.errors, function (i, val) {
			message += 'ERROR : ' + val.data.error.message + '<br/>';
		});

		$("#status").html('<p><span class="label label-important">' + message + '</span><p></p>');
	}

	function onStatus(e) {
		debug('tdplayer::onStatus');

		setStatus(e.data.status);
	}

	function onGeoBlocked(e) {
		debug('tdplayer::onGeoBlocked');

		setStatus(e.data.text);
	}

	function setStatus(status) {
		debug(status);

		$("#status").html('<p><span class="label label-success">Status: ' + status + '</span></p>');
	}

	function setTech(techType) {
		var apiVersion = player.version.major + '.' + player.version.minor + '.' + player.version.patch + '.' + player.version.flag;

		var techInfo = '<p><span class="label label-info">Api version: ' + apiVersion + ' - Technology: ' + techType;

		if (player.flash.available) {
			techInfo += ' - Your current version of flash plugin is: ' + player.flash.version.major + '.' + player.flash.version.minor + '.' + player.flash.version.rev;
		}

		techInfo += '</span></p>';

		$("#techInfo").html(techInfo);
	}

	function loadPwaData() {
		if ($("#pwaCallsign").val() === '' || $("#pwaStreamId").val() === '') {
			alert('Please enter a Callsign and a streamid');
			return;
		}

		player.PlayerWebAdmin.load($("#pwaCallsign").val(), $("#pwaStreamId").val());
	}

	function onPwaDataLoaded(e) {
		debug('PlayerWebAdmin data loaded successfully');

		$("#asyncData").html('<br><p><span class="label label-warning">PlayerWebAdmin:</span>');

		var tableContent = '<table class="table table-striped"><thead><tr><th>Key</th><th>Value</th></tr></thead>';

		for (var item in e.data.config) {
			tableContent += "<tr><td>" + item + "</td><td>" + e.data.config[item] + "</td></tr>";
		}

		tableContent += "</table></p>";

		$("#asyncData").html("<div>" + tableContent + "</div>");
	}


	function attachAdListeners() {
		if (player.addEventListener) {
			player.addEventListener('ad-playback-start', onAdPlaybackStart);
			player.addEventListener('ad-playback-error', adError);
			player.addEventListener('ad-playback-complete', onAdPlaybackComplete);
			player.addEventListener('ad-countdown', onAdCountdown);
			player.addEventListener('vast-process-complete', onVastProcessComplete);
			player.addEventListener('vpaid-ad-companions', onVpaidAdCompanions);
		} else if (player.attachEvent) {
			player.attachEvent('ad-playback-start', onAdPlaybackStart);
			player.attachEvent('ad-playback-error', adError);
			player.attachEvent('ad-playback-complete', onAdPlaybackComplete);
			player.attachEvent('ad-countdown', onAdCountdown);
			player.attachEvent('vast-process-complete', onVastProcessComplete);
			player.attachEvent('vpaid-ad-companions', onVpaidAdCompanions);
		}
	}

	function detachAdListeners() {
		if (player.removeEventListener) {
			player.removeEventListener('ad-playback-start', onAdPlaybackStart);
			player.removeEventListener('ad-playback-error', adError);
			player.removeEventListener('ad-playback-complete', onAdPlaybackComplete);
			player.removeEventListener('ad-countdown', onAdCountdown);
			player.removeEventListener('vast-process-complete', onVastProcessComplete);
			player.removeEventListener('vpaid-ad-companions', onVpaidAdCompanions);
		} else if (player.detachEvent) {
			player.detachEvent('ad-playback-start', onAdPlaybackStart);
			player.detachEvent('ad-playback-error', adError);
			player.detachEvent('ad-playback-complete', onAdPlaybackComplete);
			player.detachEvent('ad-countdown', onAdCountdown);
			player.detachEvent('vast-process-complete', onVastProcessComplete);
			player.detachEvent('vpaid-ad-companions', onVpaidAdCompanions);
		}
	}

	var artist;

	function onNPESong(e) {
		debug('tdplayer::onNPESong');

		song = e.data.song;

		artist = song.artist();
		if (artist.addEventListener) {
			artist.addEventListener('artist-complete', onArtistComplete);
		} else if (artist.attachEvent) {
			artist.attachEvent('artist-complete', onArtistComplete);
		}

		var songData = getNPEData();

		displayNpeInfo(songData, false);
	}

	function displayNpeInfo(songData, asyncData) {
		$("#asyncData").empty();

		var id = asyncData ? 'asyncData' : 'npeInfo';
		var list = $("#" + id);

		if (asyncData === false) {
			list.html('<span class="label label-inverse">Npe Info:</span>');
		}

		list.append(songData);
	}

	function onArtistComplete(e) {
		if (artist.addEventListener) {
			artist.addEventListener('picture-complete', onArtistPictureComplete);
		} else if (artist.attachEvent) {
			artist.attachEvent('picture-complete', onArtistPictureComplete);
		}

		var pictures = artist.getPictures();
		var picturesIds = [];
		for (var i = 0; i < pictures.length; i++) {
			picturesIds.push(pictures[i].id);
		}
		if (picturesIds.length > 0) {
			artist.fetchPictureByIds(picturesIds);
		}

		var songData = getArtist();

		displayNpeInfo(songData, true);
	}

	function onArtistPictureComplete(pictures) {
		debug('tdplayer::onArtistPictureComplete');

		var songData = '<span class="label label-inverse">Photos:</span><br>';

		for (var i = 0; i < pictures.length; i++) {
			if (pictures[i].getFiles()) {
				songData += '<a href="' + pictures[i].getFiles()[0].url + '" rel="lightbox[npe]" title="Click on the right side of the image to move forward."><img src="' + pictures[i].getFiles()[0].url + '" width="125" /></a>&nbsp;';
			}
		}

		$("#asyncData").append(songData);
	}

	function getArtist() {
		if (song !== undefined) {
			var songData = '<span class="label label-inverse">Artist:</span>';

			songData += '<ul><li>Artist id: ' + song.artist().id + '</li>';
			songData += '<li>Artist birth date: ' + song.artist().getBirthDate() + '</li>';
			songData += '<li>Artist end date: ' + song.artist().getEndDate() + '</li>';
			songData += '<li>Artist begin place: ' + song.artist().getBeginPlace() + '</li>';
			songData += '<li>Artist end place: ' + song.artist().getEndPlace() + '</li>';
			songData += '<li>Artist is group ?: ' + song.artist().getIsGroup() + '</li>';
			songData += '<li>Artist country: ' + song.artist().getCountry() + '</li>';

			var albums = song.artist().getAlbums();
			for (var i = 0; i < albums.length; i++) {
				songData += '<li>Album ' + ( i + 1 ) + ': ' + albums[i].getTitle() + '</li>';
			}
			var similars = song.artist().getSimilar();
			for (i < similars.length; i++;) {
				songData += '<li>Similar artist ' + ( i + 1 ) + ': ' + similars[i].name + '</li>';
			}
			var members = song.artist().getMembers();
			for (i < members.length; i++;) {
				songData += '<li>Member ' + ( i + 1 ) + ': ' + members[i].name + '</li>';
			}

			songData += '<li>Artist website: ' + song.artist().getWebsite() + '</li>';
			songData += '<li>Artist twitter: ' + song.artist().getTwitterUsername() + '</li>';
			songData += '<li>Artist facebook: ' + song.artist().getFacebookUrl() + '</li>';
			songData += '<li>Artist biography: ' + song.artist().getBiography().substring(0, 2000) + '...</small>';

			var genres = song.artist().getGenres();
			for (i < genres.length; i++;) {
				songData += '<li>Genre ' + ( i + 1 ) + ': ' + genres[i] + '</li>';
			}
			songData += '</ul>';

			return songData;
		} else {
			return '<span class="label label-important">The artist information is undefined</span>';
		}
	}

	function getNPEData() {
		var innerContent = 'NPE Data undefined';

		if (song !== undefined && song.album()) {
			var _iTunesLink = '';
			if (song.album().getBuyUrl() != null) {
				_iTunesLink = '<a target="_blank" title="' + song.album().getBuyUrl() + '" href="' + song.album().getBuyUrl() + '">Buy on iTunes</a><br/>';
			}

			innerContent = '<p><b>Album:</b> ' + song.album().getTitle() + '<br/>' +
			_iTunesLink +
			'<img src="' + song.album().getCoverArtOriginal().url + '" style="height:100px" /></p>';
		}

		return innerContent;
	}

	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	function debug(info, error) {
		if (!gmr.debug) {
			return;
		}

		if (window.console) {
			if (error) {
				console.error(info);
			} else {
				console.log(info);
			}
		}

		$('#debugInformation').append(info);
		$('#debugInformation').append('\n');
	}

	function clearDebugInfo() {
		$('#debugInformation').html('');
	}

	/* Inline Audio Support */
	var stopLiveStreamIfPlaying = function () {
		if ("undefined" !== typeof player && "undefined" !== typeof player.stop) {
			player.stop();
		}
	};

	var resetInlineAudioStates = function () {
		$('.podcast__btn--play.playing').removeClass('playing');
		$('.podcast__btn--pause.playing').removeClass('playing');
	};

	/*
	 * Finds any inline audio players with a matching hash of the current custom audio file, and sets the playing state appropriately
	 */
	var setInlineAudioStates = function () {
		var className = '.mp3-' + customHash;

		$(className + ' .podcast__btn--play').addClass('playing');
		$(className + ' .podcast__btn--pause').addClass('playing');
	};

	var setInlineAudioSrc = function (src) {
		customAudio.src = src;
	};

	var resumeCustomInlineAudio = function () {
		playingCustomAudio = true;
		stopLiveStreamIfPlaying();
		customAudio.play();
		customAudio.volume = getVolume();
		setPlayerTrackName();
		setPlayerArtist();
		resetInlineAudioStates();
		setPlayingStyles();
		setInlineAudioStates();
		setInlineAudioUX();
		startInlineAudioInterval();
	};

	var playCustomInlineAudio = function (src) {
		pjaxInit();

		// Only set the src if its different than what is already there, so we can resume the audio with the inline buttons
		if (src !== customAudio.src) {
			setInlineAudioSrc(src);
		}
		resumeCustomInlineAudio();
	};

	var pauseCustomInlineAudio = function () {
		customAudio.pause();
		resetInlineAudioStates();
		playingCustomAudio = false;
		setPausedStyles();
		stopInlineAudioInterval();
	};

	/*
	 Same as pausing, but sets the "Playing" state to false, to allow resuming live player audio
	 */
	var stopCustomInlineAudio = function () {
		customAudio.pause();
		resetInlineAudioStates();
		playingCustomAudio = false;
		setStoppedStyles();
		resetInlineAudioUX();
		stopInlineAudioInterval();
	};

	var setPlayerTrackName = function () {
		var template = _.template('<div class="now-playing__title"><%- title %></div>'),
			$trackTitleDiv = $('.now-playing__title'),
			$trackTitleWrap = '<div class="audio__title">',
			$time = '</div><div class="audio__time"><span class="audio__time--inline">(</span><div class="audio__time--elapsed"></div><span class="audio__time--inline"> / </span><div class="audio__time--remaining"></div><span class="audio__time--inline">)</span></div>';

		if ($trackTitleDiv.length > 0) {
			$trackTitleDiv.html($trackTitleWrap + customTrack + $time);
		} else {
			$trackInfo.prepend(template({title: customTrack}));
		}
	};

	var setPlayerArtist = function () {
		var template = _.template('<div class="now-playing__artist"><%- artist %></div>'),
			$trackArtistDiv = $('.now-playing__artist');

		if ($trackArtistDiv.length > 0) {
			$trackArtistDiv.text(customArtist);
		} else {
			$trackInfo.append(template({artist: customArtist}));
		}
	};

	var setCustomAudioMetadata = function (track, artist, hash) {
		customTrack = track;
		customArtist = artist;
		customHash = hash;

		setPlayerTrackName();
		setPlayerArtist();
		setInlineAudioStates();
	};

	var initCustomAudioPlayer = function () {
		if ("undefined" !== typeof Modernizr && Modernizr.audio) {
			customAudio = new Audio();

			// Revert the button states back to play once the file is done playing
			if (customAudio.addEventListener) {
				customAudio.addEventListener('ended', function () {
					resetInlineAudioStates();
					setPausedStyles();
					stopInlineAudioInterval();
				});
			} else if (customAudio.attachEvent) {
				customAudio.attachEvent('ended', function () {
					resetInlineAudioStates();
					setPausedStyles();
					stopInlineAudioInterval();
				});
			}

		}
	};

	function initInlineAudioUI() {
		if ("undefined" !== typeof Modernizr && Modernizr.audio) {
			var content = document.querySelectorAll('.content'),
				$content = $(content); // Because getElementsByClassName is not supported in IE8 ಠ_ಠ

			$content.on('click', '.podcast__btn--play', function (e) {
				var $play = $(e.currentTarget);

				nearestPodcastPlaying(e);

				playCustomInlineAudio($play.attr('data-mp3-src'));

				resetInlineAudioStates();

				setCustomAudioMetadata($play.attr('data-mp3-title'), $play.attr('data-mp3-artist'), $play.attr('data-mp3-hash'));
			});

			$content.on('click', '.podcast__btn--pause', pauseCustomInlineAudio);
		} else {
			var $meFallbacks = $('.gmr-mediaelement-fallback audio'),
				$customInterfaces = $('.podcast__play');

			$meFallbacks.mediaelementplayer();
			$customInterfaces.hide();
		}
	}

	function pjaxInit() {
		if (is_gigya_user_logged_in()) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		} else if (gmr.wpLoggedIn) {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.page-wrap', {
					'fragment': '.page-wrap',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		} else {
			if ($.support.pjax) {
				$(document).pjax('a:not(.ab-item)', '.main', {
					'fragment': '.main',
					'maxCacheLength': 500,
					'timeout': 10000
				});
			}
		}
	}

	/**
	 * Stops pjax if the live player or inline audio has stopped
	 *
	 * @param event
	 */
	function pjaxStop(event) {
		if (playingLiveAudio === true || true === playingCustomAudio) {
			// do nothing
		} else {
			event.preventDefault();
		}
	}

	$(document).bind('pjax:click', pjaxStop);

	/**
	 * calculates the time of an inline audio element and outputs the duration as a % displayed in the progress bar
	 */
	function audioUpdateProgress() {
		var progress = document.querySelectorAll('.audio__progress'), i,
			value = 0;
		for (i = 0; i < progress.length; ++i) {
			if (customAudio.currentTime > 0) {
				value = Math.floor((100 / customAudio.duration) * customAudio.currentTime);
			}
			progress[i].style.width = value + "%";
		}
	}

	/**
	 * Enables scrubbing of current audio file
	 */
	$('.audio__progress-bar').click(function(e) {
		var $this = $(this);

		var thisWidth = $this.width();
		var thisOffset = $this.offset();
		var relX = e.pageX - thisOffset.left;
		var seekLocation = Math.floor(( relX / thisWidth ) * customAudio.duration);
		customAudio.currentTime = seekLocation;
	});

	/**
	 * calculates the time of an inline audio element and outputs the time remaining
	 */
	function audioTimeRemaining() {
		var ramainings = document.querySelectorAll('.audio__time--remaining'), i,
			duration = parseInt(customAudio.duration),
			currentTime = parseInt(customAudio.currentTime),
			timeleft = new Date(2000,1,1,0,0,0),
			hours, mins, secs;

		if (isNaN(duration)) {
			duration = currentTime = 0;
		} else if (isNaN(currentTime)) {
			currentTime = 0;
		}

		timeleft.setSeconds(duration - currentTime);

		hours = timeleft.getHours();
		mins = ('0' + timeleft.getMinutes()).slice(-2);
		secs = ('0' + timeleft.getSeconds()).slice(-2);
		if (hours > 0) {
			timeleft = hours + ':' + mins + ':' + secs;
		} else {
			timeleft = mins + ':' + secs;
		}

		for (i = 0; i < ramainings.length; ++i) {
			ramainings[i].innerHTML = timeleft;
		}
	}

	/**
	 * calculates the time of an inline audio element and outputs the time that has elapsed
	 */
	function audioTimeElapsed() {
		var timeline = document.querySelectorAll('.audio__time--elapsed'),
			passedSeconds = parseInt(customAudio.currentTime),
			currentTime = new Date(2000,1,1,0,0,0),
			hours, mins, secs, i;

		currentTime.setSeconds(isNaN(passedSeconds) ? 0 : passedSeconds);

		hours = currentTime.getHours();
		mins = ('0' + currentTime.getMinutes()).slice(-2);
		secs = ('0' + currentTime.getSeconds()).slice(-2);
		if (hours > 0) {
			currentTime = hours + ':' + mins + ':' + secs;
		} else {
			currentTime = mins + ':' + secs;
		}

		for (i = 0; i < timeline.length; ++i) {
			timeline[i].innerHTML = currentTime;
		}
	}

	initCustomAudioPlayer();
	initInlineAudioUI();

	/**
	 * event listeners for customAudio time
	 */
	customAudio.addEventListener('timeupdate', function () {
		audioUpdateProgress();
		audioTimeElapsed();
		audioTimeRemaining();
	}, false);

	addEventHandler(podcastPlayBtn, 'click', setInlineAudioUX);

	addEventHandler(podcastPauseBtn, 'click', pauseCustomInlineAudio);

	// Ensures our listeners work even after a PJAX load
	$(document).on('pjax:end', function () {
		initInlineAudioUI();
		setInlineAudioStates();
		addEventHandler(podcastPlayBtn, 'click', setInlineAudioUX);
		addEventHandler(podcastPauseBtn, 'click', pauseCustomInlineAudio);
	});

})(jQuery, window);
