/***
 * This script is used to create a gate over the iframe of the national contents
 * when the user is not logged in.
 * The gate is created by creating a div with the same size and position of the iframe
 * and setting its background color to black with 50% opacity.
 */

/***
 * This function is called when the user clicks on the login button in the gate.
 *
 * @param {string} iframeId The id of the iframe
 * @param {string} iGateId The class of the gate
 */
window.createGate = (iframeId, iGateId) => {
	let iframe = document.getElementById(iframeId);
	if (!iframe) return;

	// Create a new div for the gate
	let gate = document.getElementById(iGateId);
	if (!gate) return;

	// Set the gate styles
	gate.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
	gate.style.position = "absolute";
	gate.style.top = iframe.offsetTop + "px";
	gate.style.left = iframe.offsetLeft + "px";
	gate.style.width = iframe.offsetWidth + "px";
	gate.style.height = iframe.offsetHeight + "px";
	gate.style.visibility = "visible";

	// Set gate's class and append it to body
	gate.classList.add("gate-" + iframeId);

	/* set gate notification styles */
	let gateNotification = document.getElementsByClassName("gate-notification")[0];
	gateNotification.style.display = "flex";
	gateNotification.style.flexDirection = "column";
	gateNotification.style.justifyContent = "center";
	gateNotification.style.alignItems = "center";
	gateNotification.style.color = "white";
	gateNotification.style.height = "100%";

	/* set gate notification button styles */
	let gateNotificationButton = gateNotification.getElementsByTagName("a")[0];
	gateNotificationButton.style.display = "inline-block";
	gateNotificationButton.style.backgroundColor = "red";
	gateNotificationButton.style.color = "white";
	gateNotificationButton.style.border = "none";
	gateNotificationButton.style.padding = "10px";
	gateNotificationButton.style.borderRadius = "5px";
	gateNotificationButton.style.marginTop = "10px";
	gateNotificationButton.style.cursor = "pointer";
	gateNotificationButton.style.textDecoration = "none"; /* to make it look like a button */
	gateNotificationButton.textContent = "Login";

	/* Read the query string parameters */
	const urlParams = new URLSearchParams(window.location.search);
	const whizParam = urlParams.get('whiz');

	// Function to handle click event
	const handleClick = function (e) {
		e.preventDefault();
		window.openLoginRegistration();
	};

	if (whizParam) {
		/* If whiz querystring parameter is present */
		gateNotificationButton.href = "/_login_activity_";
	} else {
		gateNotificationButton.addEventListener("click", handleClick);
		// Store the event listener function reference
		iframe.dataset.eventListenerFunc = handleClick;
	}
	// Instantiate the ResizeObserver and observe the iframe
	let resizeObserver = new ResizeObserver(function (entries) {
		entries.forEach(function (entry) {
			// Adjust gate size when iframe size changes
			gate.style.width = entry.contentRect.width + "px";
			gate.style.height = entry.contentRect.height + "px";
		});
	});
	resizeObserver.observe(iframe);

	// Instantiate the MutationObserver and observe the iframe's parent
	let mutationObserver = new MutationObserver(function () {
		// Adjust gate position when iframe position changes
		gate.style.top = iframe.offsetTop + "px";
		gate.style.left = iframe.offsetLeft + "px";
	});
	mutationObserver.observe(iframe, {attributes: true, attributeFilter: ['style']});

	// Store the gate and observers in the iframe's dataset for later removal
	iframe.dataset.gateId = gate.id;
	iframe.dataset.resizeObserverId = resizeObserver;
	iframe.dataset.mutationObserverId = mutationObserver;
};

// 100 ms debounce function for createGate using a timeout
window.createGateTimeout = -1;
window.createGateDebounce = (iframeId, iGateId) => {
	clearTimeout(window.createGateTimeout);
	window.createGateTimeout = setTimeout(() => {
		window.createGate(iframeId, iGateId);
	}, 100);
};

/***
 * This function is called when the user logs in.
 *
 * 	@param {string} iframeId The id of the iframe
 */
window.removeGate = iframeId => {
	let iframe = document.getElementById(iframeId);
	let gate = document.getElementById(iframe.dataset.gateId);

	if (iframe && gate) {
		// hide visibility of gate
		gate.style.visibility = "hidden";
	}

	// determine if observers exist if not return
	if (iframe.dataset.resizeObserverId && iframe.dataset.mutationObserverId) {
		let resizeObserver = iframe.dataset.resizeObserverId;
		let mutationObserver = iframe.dataset.mutationObserverId;

		// Disconnect observers
		resizeObserver.disconnect();
		mutationObserver.disconnect();
	} else {
		return;
	}

	// Remove the event listener using the stored function reference
	if (iframe.dataset.eventListenerFunc) {
		let gateNotificationButton = gate.getElementsByClassName("gate-notification")[0].getElementsByTagName("a")[0];
		gateNotificationButton.removeEventListener("click", iframe.dataset.eventListenerFunc);
	}


	// Clean up the iframe's dataset
	delete iframe.dataset.gateId;
	delete iframe.dataset.resizeObserverId;
	delete iframe.dataset.mutationObserverId;
	delete iframe.dataset.eventListenerFunc; // Also remove the event listener function reference
};

// 100 ms debounce function for removeGate using a timeout
window.removeGateTimeout = -1;
window.removeGateDebounce = iframeId => {
	clearTimeout(window.removeGateTimeout);
	window.removeGateTimeout = setTimeout(() => {
		window.removeGate(iframeId);
	}, 100);
};
