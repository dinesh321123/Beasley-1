
window.isWhiz = () => {
	const searchParams = new URLSearchParams(window.location.search?.toLowerCase());
	return searchParams.has('whiz')
};

window.getDayPart = (hourOfDay) => {
	const morning = 'Morning Drive'; // 6am to 10am
	const midday = 'Midday'; // 10am to 3pm
	const afternoon = 'Afternoon Drive'; // 3pm to 7pm
	const evening = 'Evening'; // 7pm to 12am
	const overnight = 'Overnight'; // 12am to 6am
	const dayPartArray = [
		overnight, // 0
		overnight, // 1
		overnight, // 2
		overnight, // 3
		overnight, // 4
		overnight, // 5
		morning, // 6
		morning, // 7
		morning, // 8
		morning, // 9
		midday, // 10
		midday, // 11
		midday, // 12
		midday, // 13
		midday, // 14
		afternoon, // 15
		afternoon, // 16
		afternoon, // 17
		afternoon, // 18
		evening, // 19
		evening, // 20
		evening, // 21
		evening, // 22
		evening, // 23
	];

	return dayPartArray[hourOfDay];
}

// createUUID() copied from https://www.arungudelli.com/tutorial/javascript/how-to-create-uuid-guid-in-javascript-with-examples/
// NOT WELL TESTED
window.createUUID = () => {
	return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
		(c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
	)
}


// copied from https://github.com/dbrekalo/adblockdetect
(function(root, factory) {

	/* istanbul ignore next */
	if (typeof define === 'function' && define.amd) {
		define([], factory);
	} else if (typeof module === 'object' && module.exports) {
		module.exports = factory();
	} else {
		root.adblockDetect = factory();
	}

}(this, function() {

	function adblockDetect(callback, options) {

		options = merge(adblockDetect.defaults, options || {});

		var testNode = createNode(options.testNodeClasses, options.testNodeStyle);
		var runsCounter = 0;
		var adblockDetected = false;

		console.log(`Ad Blocker Detection Initialized`);
		var testInterval = setInterval(function() {

			runsCounter++;
			adblockDetected = isNodeBlocked(testNode);
			console.log(`Ad Blocker Detection Try ${runsCounter} Yielded ${adblockDetected}`);

			if (adblockDetected || runsCounter === options.testRuns) {
				clearInterval(testInterval);
				testNode.parentNode && testNode.parentNode.removeChild(testNode);
				callback(adblockDetected);
			}

		}, options.testInterval);

	}

	function createNode(testNodeClasses, testNodeStyle) {

		var document = window.document;
		var testNode = document.createElement('div');

		testNode.innerHTML = '&nbsp;';
		testNode.setAttribute('class', testNodeClasses);
		testNode.setAttribute('style', testNodeStyle);

		document.body.appendChild(testNode);

		return testNode;

	}

	function isNodeBlocked(testNode) {

		return testNode.offsetHeight === 0 ||
			!document.body.contains(testNode) ||
			testNode.style.display === 'none' ||
			testNode.style.visibility === 'hidden'
			;

	}

	function merge(defaults, options) {

		var obj = {};

		for (var key in defaults) {
			obj[key] = defaults[key];
			options.hasOwnProperty(key) && (obj[key] = options[key]);
		}

		return obj;

	}

	adblockDetect.defaults = {
		testNodeClasses: 'pub_300x250 pub_300x250m pub_728x90 text-ad textAd text_ad text_ads text-ads text-ad-links',
		testNodeStyle: 'height: 10px !important; font-size: 20px; color: transparent; position: absolute; bottom: 0; left: -10000px;',
		testInterval: 51,
		testRuns: 4
	};

	return adblockDetect;

}));
