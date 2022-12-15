import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import {
	ACTION_AD_PLAYBACK_ERROR,
	ACTION_GAM_AD_PLAYBACK_COMPLETE,
} from '../../redux/actions/player';
import { isIOS } from '../../library';

class GamPreroll extends PureComponent {
	constructor(props) {
		console.log('GamPreroll Constructor');
		super(props);
		this.state = {
			startedPrerollFlag: false,
			playingPrerollFlag: false,
			isFinalized: false,
		};

		this.adsManager = null;
		this.adsLoader = null;
		this.adDisplayContainer = null;
		this.videoContent = null;

		// this.doStopVideoElement = this.doStopVideoElement.bind(this);
		this.doClaimVideoElement = this.doClaimVideoElement.bind(this);
		this.doPreroll = this.doPreroll.bind(this);
		this.playPreroll = this.playPreroll.bind(this);
		this.onAdsManagerLoaded = this.onAdsManagerLoaded.bind(this);
		this.onAdEvent = this.onAdEvent.bind(this);
		this.onAdError = this.onAdError.bind(this);
		this.playAds = this.playAds.bind(this);
		this.finalize = this.finalize.bind(this);

		this.onResize = this.handleResize.bind(this);
		this.updateSize = this.updateSize.bind(this);
	}

	handleResize() {
		this.updateSize();
	}

	updateSize() {
		if (this.adsManager) {
			console.log('Resizing Ad');
			const containerElement = document.getElementById('gamPrerollAdContainer');
			if (containerElement) {
				console.log('Found Container for Resize');
				const width = containerElement.clientWidth;
				// Height Showing as 0 so compute... const height = containerElement.clientHeight;
				const height = (width / 640) * 480;

				// Resize Video Element If IOS
				/*
				if (isIOS()) {
					console.log('IS IOS');
					const vidElement = document.getElementById(
						'gamPrerollContentElement',
					);
					if (vidElement) {
						console.log('Setting Vid Dimensions');
						// vidElement.clientHeight = height;
						// vidElement.clientWidth = width;
						vidElement.style.height = `${height}px`;
						vidElement.style.width = `${width}px`;

						console.log(`Vid Element Resized`);
					}
				}
				 */

				console.log('Resizing Ad Manager');
				this.adsManager.resize(
					width,
					height,
					window.google.ima.ViewMode.NORMAL,
				);
			}
		}
	}

	playPreroll(adUnitID, cdomain) {
		const { startedPrerollFlag } = this.state;
		if (startedPrerollFlag) {
			return;
		}

		if (this.getIsIMALoaded()) {
			// All is well - do nothing
		} else {
			console.log(`Unexpected Call To Play Preroll Without IMA Loaded`);
			this.finalize();
			return;
		}

		this.videoContent = document.getElementById('gamPrerollContentElement');
		this.setUpIMA(adUnitID, cdomain);

		// Mark State
		this.setState({
			startedPrerollFlag: true,
			playingPrerollFlag: false,
			isFinalized: false,
		});
	}

	setUpIMA(adUnitID, cdomain) {
		// Create the ad display container.
		this.createAdDisplayContainer();
		// Create ads loader.
		this.adsLoader = new window.google.ima.AdsLoader(this.adDisplayContainer);
		// Listen and respond to ads loaded and error events.
		this.adsLoader.addEventListener(
			window.google.ima.AdsManagerLoadedEvent.Type.ADS_MANAGER_LOADED,
			this.onAdsManagerLoaded,
			false,
		);
		this.adsLoader.addEventListener(
			window.google.ima.AdErrorEvent.Type.AD_ERROR,
			this.onAdError,
			false,
		);

		// An event listener to tell the SDK that our content video
		// is completed so the SDK can play any post-roll ads.
		const contentEndedListener = () => {
			this.adsLoader.contentComplete();
		};
		this.videoContent.onended = contentEndedListener;

		// Request video ads.
		console.log('Requesting GAM Video Ad');
		const adsRequest = new window.google.ima.AdsRequest();
		// adsRequest.adTagUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${adUnitID}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x480%7C640x480%7C920x508&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;
		adsRequest.adTagUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=%2F26918149%2Fstaging_wrif_preroll&description_url=[placeholder]&tfcd=0&npa=0&sz=640x480&cust_params=cdomain%3D${cdomain}&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;

		// Specify the linear and nonlinear slot sizes. This helps the SDK to
		// select the correct creative if multiple are returned.
		/*
		adsRequest.linearAdSlotWidth = 640;
		adsRequest.linearAdSlotHeight = 400;

		adsRequest.nonLinearAdSlotWidth = 640;
		adsRequest.nonLinearAdSlotHeight = 150;
		*/

		this.adsLoader.requestAds(adsRequest);
	}

	createAdDisplayContainer() {
		// We assume the adContainer is the DOM id of the element that will house
		// the ads.
		this.adDisplayContainer = new window.google.ima.AdDisplayContainer(
			document.getElementById('gamPrerollAdContainer'),
			this.videoContent,
		);
	}

	playAds() {
		// Put In Delayed Guard
		setTimeout(() => {
			const { playingPrerollFlag } = this.state;
			if (!playingPrerollFlag) {
				console.log(
					`Detected That Preroll Is Not Playing After 3 Seconds. Finalizing Preroll`,
				);
				this.finalize();
			}
		}, 3000);

		// Mark State
		this.setState({ playingPrerollFlag: true });

		// Initialize the container. Must be done via a user action on mobile devices.
		this.videoContent.load();
		this.adDisplayContainer.initialize();

		try {
			// Initialize the ads manager. Ad rules playlist will start at this time.
			this.adsManager.init(640, 480, window.google.ima.ViewMode.NORMAL);
			this.updateSize();
			// Call play to start showing the ad. Single video and overlay ads will
			// start at this time; the call will be ignored for ad rules.
			this.adsManager.start();
		} catch (adError) {
			// An error may be thrown if there was a problem with the VAST response.
			this.finalize();
		}
	}

	onAdsManagerLoaded(adsManagerLoadedEvent) {
		// Get the ads manager.
		const adsRenderingSettings = new window.google.ima.AdsRenderingSettings();

		adsRenderingSettings.restoreCustomPlaybackStateOnAdBreakComplete = true;

		// videoContent should be set to the content video element.
		this.adsManager = adsManagerLoadedEvent.getAdsManager(
			this.videoContent,
			adsRenderingSettings,
		);

		// Add listeners to the required events.
		this.adsManager.addEventListener(
			window.google.ima.AdErrorEvent.Type.AD_ERROR,
			this.onAdError,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.ALL_ADS_COMPLETED,
			this.onAdEvent,
		);

		// Listen to any additional events, if necessary.
		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.LOADED,
			this.onAdEvent,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.STARTED,
			this.onAdEvent,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.COMPLETE,
			this.onAdEvent,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.CLICK,
			this.onAdEvent,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.VIDEO_CLICKED,
			this.onAdEvent,
		);

		this.adsManager.addEventListener(
			window.google.ima.AdEvent.Type.VIDEO_ICON_CLICKED,
			this.onAdEvent,
		);

		this.playAds();
	}

	onAdEvent(adEvent) {
		const wrapperEl = document.getElementById('gamPrerollWrapper');

		// Retrieve the ad from the event. Some events (e.g. ALL_ADS_COMPLETED)
		// don't have ad object associated.
		const ad = adEvent.getAd();
		console.log(`IMA Event - '${adEvent.type}'`);
		switch (adEvent.type) {
			case window.google.ima.AdEvent.Type.LOADED:
				console.log(`FOR DEBUG - IMA Ad Loaded Event.`);
				this.updateSize();
				// This is the first event sent for an ad - it is possible to
				// determine whether the ad is a video ad or an overlay.
				if (ad.isLinear()) {
					console.log(`Ad Is Linear.`);
				} else {
					console.log(`Ad Is Not Linear - Playing.`);
					this.videoContent.play();
				}
				setTimeout(() => {
					this.adsManager.pause();
					console.log('Pausing Ad For Debug');
				}, 3000);
				break;
			case window.google.ima.AdEvent.Type.STARTED:
				// This event indicates the ad has started - the video player
				// can adjust the UI, for example display a pause button and
				// remaining time.
				if (wrapperEl) {
					wrapperEl.classList.add('gampreroll-shade');
				}
				break;
			// case window.google.ima.AdEvent.Type.COMPLETE:
			case window.google.ima.AdEvent.Type.CLICK:
			case window.google.ima.AdEvent.Type.VIDEO_CLICKED:
			case window.google.ima.AdEvent.Type.VIDEO_ICON_CLICKED:
			case window.google.ima.AdEvent.Type.ALL_ADS_COMPLETED:
				// This event indicates that ALL Ads have finished.
				// This event was seen emitted from a Google example ad upon pressing a "Skip Ad" button.
				this.finalize();
				break;
			default:
				console.log(`Unhandled IMA Event - '${adEvent.type}'`);
				break;
		}
	}

	onAdError(adErrorEvent) {
		// Handle the error logging.
		console.log(adErrorEvent.getError());
		this.finalize();
	}

	componentDidMount() {
		window.addEventListener('resize', this.onResize);
		if (isIOS()) {
			this.doClaimVideoElement();
		}
	}

	doClaimVideoElement() {
		const vidElement = document.getElementById('gamPrerollContentElement');
		if (vidElement) {
			(async () => {
				try {
					await vidElement.play();
					await vidElement.pause();
					// eslint-disable-next-line no-empty
				} catch (err) {}
			})();
		}
	}

	doPreroll() {
		const { isFinalized } = this.state;
		if (isFinalized) {
			console.log('Not Re-Playing Preroll');
			return;
		}

		const { global, tunerpreroll } = window.bbgiconfig.dfp;
		// global holds a 2 dimensional array like "global":[["cdomain","wmmr.com"],["cpage","home"],["ctest",""],["genre","rock"],["market","philadelphia, pa"]]
		const globalObj = global.reduce((acc, item) => {
			const key = `${item[0]}`;
			acc[key] = `${item[1]}`;
			return acc;
		}, {});

		if (tunerpreroll && tunerpreroll.unitId) {
			// Play the preroll
			this.playPreroll(tunerpreroll.unitId, globalObj.cdomain);
		} else {
			console.log(`NOT playing GAM Preroll - no tunerpreroll.unitId found`);
			this.finalize();
		}
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
		this.finalize();
	}

	getIsIMALoaded() {
		return window.google && window.google.ima && window.google.ima.AdsLoader;
	}

	finalize() {
		console.log('GAM Preroll Finalize()');

		if (this.adsManager) {
			this.adsManager.destroy();
		}

		const { isFinalized, playingPrerollFlag } = this.state;
		if (!isFinalized) {
			// Mark State
			this.setState({
				startedPrerollFlag: false,
				playingPrerollFlag: false,
				isFinalized: true,
			});

			console.log(
				`GAM Preroll Actually Finalizing - playingPrerollFlag: ${playingPrerollFlag}`,
			);
			// Call Player Action
			const { adPlaybackStop } = this.props;
			// If we even started a preroll, pretend it was a success
			if (playingPrerollFlag) {
				adPlaybackStop(ACTION_GAM_AD_PLAYBACK_COMPLETE);
			} else {
				adPlaybackStop(ACTION_AD_PLAYBACK_ERROR);
			}
		}
	}

	render() {
		if (isIOS()) {
			const width = window.document.body.clientWidth;
			const height = (width / 640) * 480;
			const topMargin = (window.document.body.clientHeight - height) / 2;
			return (
				<div id="gamPrerollWrapper" className="gampreroll-wrapper -active">
					<div id="gamPrerollContent" style={{ marginTop: topMargin }}>
						<video
							id="gamPrerollContentElement"
							width={width}
							height={height}
							playsInline
						>
							<track
								src="captions_en.vtt"
								kind="captions"
								srcLang="en"
								label="english_captions"
							/>
						</video>
					</div>
					<div id="gamPrerollAdContainer" className="gam-preroll-player" />
				</div>
			);
		}

		return (
			<div id="gamPrerollWrapper" className="gampreroll-wrapper -active">
				<div id="gamPrerollContent">
					<video id="gamPrerollContentElement">
						<track
							src="captions_en.vtt"
							kind="captions"
							srcLang="en"
							label="english_captions"
						/>
					</video>
				</div>
				<div id="gamPrerollAdContainer" className="gam-preroll-player" />
			</div>
		);
	}
}

GamPreroll.propTypes = {
	adPlaybackStop: PropTypes.func.isRequired,
};

export default GamPreroll;
