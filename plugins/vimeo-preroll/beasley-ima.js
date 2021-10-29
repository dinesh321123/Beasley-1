// Copyright 2013 Google Inc. All Rights Reserved.
// You may study, modify, and use this example for any purpose.
// Note that this example is provided "as is", WITHOUT WARRANTY
// of any kind either expressed or implied.

var adsManager;
var adsLoader;
var adDisplayContainer;
var playButton;
var videoContent;
var imaIsSetUp = false;
var prerollCallbackFunc;

window.setUpVimeoIMA = () => {
	console.log(`Initializing IMA`);

	if (!imaIsSetUp) {
		imaIsSetUp = true;
		videoContent = document.getElementById('vimeoPrerollContentElement');
		// Create the ad display container.
		createAdDisplayContainer();
		// Create ads loader.
		adsLoader = new window.google.ima.AdsLoader(adDisplayContainer);
		// Listen and respond to ads loaded and error events.
		adsLoader.addEventListener(
			window.google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED,
			onAdsManagerLoaded, false);
		adsLoader.addEventListener(
			window.google.ima.AdErrorEvent.Type.AD_ERROR, onAdError, false);

		// An event listener to tell the SDK that our content video
		// is completed so the SDK can play any post-roll ads.
		var contentEndedListener = function () {
			adsLoader.contentComplete();
		};
		videoContent.onended = contentEndedListener;
	}
}

function createAdDisplayContainer() {
	// We assume the adContainer is the DOM id of the element that will house
	// the ads.
	adDisplayContainer = new window.google.ima.AdDisplayContainer(
		document.getElementById('vimeoPrerollAdContainer'), videoContent);
}

function playVimeoIMAAds(videoUrl, prerollCallbackFuncParam) {
	console.log(`Playing IMA Ad`);
	prerollCallbackFunc = prerollCallbackFuncParam;



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

	// Initialize the container. Must be done via a user action on mobile devices.
	videoContent.load();
	adDisplayContainer.initialize();

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
}

function onAdEvent(adEvent) {
	// Retrieve the ad from the event. Some events (e.g. ALL_ADS_COMPLETED)
	// don't have ad object associated.
	var ad = adEvent.getAd();
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
			break;
		case window.google.ima.AdEvent.Type.COMPLETE:
			// This event indicates the ad has finished - the video player
			// can perform appropriate UI actions, such as removing the timer for
			// remaining time detection.
			prerollCallbackFunc();
			break;
		case window.google.ima.AdEvent.Type.ALL_ADS_COMPLETED:
			// This event indicates that ALL Ads have finished.
			// This event was seen emitted from a Google example ad upon pressing a "Skip Ad" button.
			prerollCallbackFunc();
			break;
	}
}

function onAdError(adErrorEvent) {
	// Handle the error logging.
	console.log(adErrorEvent.getError());
	adsManager.destroy();
	prerollCallbackFunc();
}

function onContentPauseRequested() {
	videoContent.pause();
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

// Wire UI element references and UI event listeners.
//init();
