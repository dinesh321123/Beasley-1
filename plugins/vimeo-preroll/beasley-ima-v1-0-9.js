/*** Any Change To This File Must Entail A Version Change Due To Perma-Cache ***/
/*** Includes are held in vimeo-preroll.php ***/
/* v1-0-0 - Institute Versioning to comply with permacache */
/* v1-0-4 - Put in proxy button to convince IOS IMA that user action initiated Ad Play */

// This code is heavily based on Google's Simple IMA Example at https://github.com/googleads/googleads-ima-html5

// Copyright 2013 Google Inc. All Rights Reserved.
// You may study, modify, and use this example for any purpose.
// Note that this example is provided "as is", WITHOUT WARRANTY
// of any kind either expressed or implied.

var adsManager;
var adsLoader;
var adDisplayContainer;
var videoContent;
var vimeoControlHolder;

function setUpVimeoIMA() {
	console.log(`Initializing IMA`);

	// Create ads loader.
	adsLoader = new window.google.ima.AdsLoader(adDisplayContainer);
	// Listen and respond to ads loaded and error events.
	adsLoader.addEventListener(
		window.google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED,
		onAdsManagerLoaded, false);
	adsLoader.addEventListener(
		window.google.ima.AdErrorEvent.Type.AD_ERROR, onAdError, false);
}

function createIMADisplayContainer() {
	videoContent = document.getElementById('vimeoVideoElement');
	// An event listener to tell the SDK that our content video
	// is completed so the SDK can play any post-roll ads.
	var contentEndedListener = function () {
		adsLoader.contentComplete();
	};
	videoContent.onended = contentEndedListener;

	// We assume the adContainer is the DOM id of the element that will house
	// the ads.
	adDisplayContainer = new window.google.ima.AdDisplayContainer(
		document.getElementById('vimeoPrerollAdContainer'), videoContent);

	// Initialize the container. Must be done via a user action on mobile devices.
	videoContent.load();
	adDisplayContainer.initialize();
	console.log(`adDisplayContainer Initialized - Event Should Be Perceived As User Interaction`);
}

function playVimeoIMAAds(videoUrl, vimeoControl) {
	console.log(`Playing IMA Ad`);

	vimeoControlHolder = vimeoControl;

	setUpVimeoIMA();

	// Request video ads.
	var adsRequest = new window.google.ima.AdsRequest();
	adsRequest.adTagUrl = videoUrl;

		// Specify the linear and nonlinear slot sizes. This helps the SDK to
		// select the correct creative if multiple are returned.
		/*
		adsRequest.linearAdSlotWidth = 640;
		adsRequest.linearAdSlotHeight = 400;

		adsRequest.nonLinearAdSlotWidth = 640;
		adsRequest.nonLinearAdSlotHeight = 150;
		 */

		adsLoader.requestAds(adsRequest);
}

function playAds() {
	try {
		// Initialize the ads manager. Ad rules playlist will start at this time.
		adsManager.init(640, 360, window.google.ima.ViewMode.NORMAL);
		// Call play to start showing the ad. Single video and overlay ads will
		// start at this time; the call will be ignored for ad rules.
		adsManager.start();
	} catch (adError) {
		// An error may be thrown if there was a problem with the VAST response.
		videoContent.play();
	}
}

function updateSize() {
	if (adsManager) {
		const containerElement = document.getElementById('vimeoPrerollAdContainer');
		if (containerElement) {
			const wrapperElement = document.getElementById('vimeoPrerollWrapper');
			let vimeoContainerWidth = containerElement.clientWidth;
			let vimeoContainerHeight = (vimeoContainerWidth / 640) * 360;

			// Make sure Vimeo height does not exceed wrapper height(which is same dimension as parent);
			if (vimeoContainerHeight > wrapperElement.clientHeight) {
				vimeoContainerHeight = wrapperElement.clientHeight;
				vimeoContainerWidth = (vimeoContainerHeight / 360) * 640;
			}

			this.adsManager.resize(
				vimeoContainerWidth,
				vimeoContainerHeight,
				window.google.ima.ViewMode.NORMAL,
			);

			const wrapperHeightString = window.getComputedStyle(wrapperElement).height;
			const wrapperHeight =
				wrapperHeightString.indexOf('px') > -1
					? wrapperHeightString.replace('px', '')
					: '0';
			const computedTop = (parseInt(wrapperHeight, 10) - vimeoContainerHeight) / 2;
			containerElement.style.paddingTop = `${computedTop && computedTop > 0 ? computedTop : 0}px`;

			const computedLeft = (containerElement.clientWidth - vimeoContainerWidth) / 2;
			containerElement.style.paddingLeft = `${computedLeft && computedLeft > 0 ? computedLeft : 0}px`;
		}
	}
}

function onAdsManagerLoaded(adsManagerLoadedEvent) {
	// Get the ads manager.
	var adsRenderingSettings = new window.google.ima.AdsRenderingSettings();
	adsRenderingSettings.restoreCustomPlaybackStateOnAdBreakComplete = true;
	// videoContent should be set to the content video element.
	adsManager =
		adsManagerLoadedEvent.getAdsManager(videoContent, adsRenderingSettings);

	// Add listeners to the required events.
	adsManager.addEventListener(window.google.ima.AdErrorEvent.Type.AD_ERROR, onAdError);
	adsManager.addEventListener(
		window.google.ima.AdEvent.Type.CONTENT_PAUSE_REQUESTED, onContentPauseRequested);
	adsManager.addEventListener(
		window.google.ima.AdEvent.Type.CONTENT_RESUME_REQUESTED,
		onContentResumeRequested);
	adsManager.addEventListener(
		window.google.ima.AdEvent.Type.ALL_ADS_COMPLETED, onAdEvent);
	adsManager.addEventListener(window.google.ima.AdEvent.Type.LOADED, onAdEvent);
	adsManager.addEventListener(window.google.ima.AdEvent.Type.STARTED, onAdEvent);
	adsManager.addEventListener(window.google.ima.AdEvent.Type.COMPLETE, onAdEvent);

	adsManager.addEventListener(window.google.ima.AdEvent.Type.CLICK, onAdEvent);
	adsManager.addEventListener(window.google.ima.AdEvent.Type.VIDEO_CLICKED, onAdEvent);
	adsManager.addEventListener(window.google.ima.AdEvent.Type.VIDEO_ICON_CLICKED, onAdEvent);
	playAds();
}

function onAdEvent(adEvent) {
	// Retrieve the ad from the event. Some events (e.g. ALL_ADS_COMPLETED)
	// don't have ad object associated.
	var ad = adEvent.getAd();
	console.log(`IMA Event - '${adEvent.type}'`);
	switch (adEvent.type) {
		case window.google.ima.AdEvent.Type.LOADED:
			// This is the first event sent for an ad - it is possible to
			// determine whether the ad is a video ad or an overlay.
			if (!ad.isLinear()) {
				// Position AdDisplayContainer correctly for overlay.
				// Use ad.width and ad.height.
				videoContent.play();
			}
			break;
		case window.google.ima.AdEvent.Type.STARTED:
			// This event indicates the ad has started - the video player
			// can adjust the UI, for example display a pause button and
			// remaining time.
			const wrapperDiv = document.getElementById('vimeoPrerollWrapper');
			wrapperDiv.classList.add('-active');
			updateSize();
			window.removeEventListener('resize', updateSize);
			window.addEventListener('resize', updateSize);
			break;
		// case window.google.ima.AdEvent.Type.COMPLETE:
		case window.google.ima.AdEvent.Type.CLICK:
		case window.google.ima.AdEvent.Type.VIDEO_CLICKED:
		case window.google.ima.AdEvent.Type.VIDEO_ICON_CLICKED:
		case window.google.ima.AdEvent.Type.ALL_ADS_COMPLETED:
			// This event indicates that ALL Ads have finished.
			// This event was seen emitted from a Google example ad upon pressing a "Skip Ad" button.
			const vimeoControlHolderClosure = vimeoControlHolder;
			beasleyIMACleanup();
			vimeoControlHolderClosure.prerollCallback();
			break;
		default:
			console.log(`Unhandled IMA Event - '${adEvent.type}'`);
			break;
	}
}

function onAdError(adErrorEvent) {
	// Handle the error logging.
	console.log('IMA onAdError Event');
	console.log(adErrorEvent.getError());

	try {
		console.log('Calling Callback');
		vimeoControlHolder.prerollCallback();
	}
	catch {}

	try {
		console.log('Clearing Ad Manager');
		// imaIsSetUp = false;
		if (adsManager && adsManager.destroy) {
			adsManager.destroy();
		}
	}
	catch {}

	beasleyIMACleanup();
}

function beasleyIMACleanup() {
	console.log('Clearing Vimeo Control Holder');
	vimeoControlHolder = null;
}

function onContentPauseRequested() {
	// Safari Is Autopausing after Preroll
	//videoContent.pause();

	// This function is where you should setup UI for showing ads (e.g.
	// display ad timer countdown, disable seeking etc.)
	// setupUIForAds();
}

function onContentResumeRequested() {
	videoContent.play();
	// This function is where you should ensure that your UI is ready
	// to play content. It is the responsibility of the Publisher to
	// implement this function when necessary.
	// setupUIForContent();
}

