import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import {
	ACTION_GAM_AD_PLAYBACK_COMPLETE,
	adPlaybackStop,
} from '../../redux/actions/player';

class GamPreroll extends PureComponent {
	constructor(props) {
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
			const containerElement = document.getElementById('gamPrerollAdContainer');
			if (containerElement) {
				const width = containerElement.clientWidth;
				// Height Showing as 0 so compute... const height = containerElement.clientHeight;
				const height = (width / 640) * 360;
				this.adsManager.resize(
					width,
					height,
					window.google.ima.ViewMode.NORMAL,
				);
			}
		}
	}

	playPreroll(adUnitID) {
		const { startedPrerollFlag } = this.state;
		if (startedPrerollFlag) {
			return;
		}

		if (!window.google.ima.AdsLoader) {
			this.finalize();
		}

		this.videoContent = document.getElementById('gamPrerollContentElement');
		this.setUpIMA(adUnitID);

		// Mark State
		this.setState({
			startedPrerollFlag: true,
			playingPrerollFlag: false,
			isFinalized: false,
		});
	}

	setUpIMA(adUnitID) {
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
		const adsRequest = new window.google.ima.AdsRequest();
		adsRequest.adTagUrl = `https://pubads.g.doubleclick.net/gampad/live/ads?iu=${adUnitID}&description_url=[placeholder]&tfcd=0&npa=0&sz=640x360%7C640x480%7C920x508&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=`;

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
		// Mark State
		this.setState({ playingPrerollFlag: true });

		// Initialize the container. Must be done via a user action on mobile devices.
		this.videoContent.load();
		this.adDisplayContainer.initialize();

		try {
			// Initialize the ads manager. Ad rules playlist will start at this time.
			this.adsManager.init(640, 360, window.google.ima.ViewMode.NORMAL);
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

		this.playAds();
	}

	onAdEvent(adEvent) {
		const wrapperEl = document.getElementById('gamPrerollWrapper');

		// Retrieve the ad from the event. Some events (e.g. ALL_ADS_COMPLETED)
		// don't have ad object associated.
		const ad = adEvent.getAd();
		switch (adEvent.type) {
			case window.google.ima.AdEvent.Type.LOADED:
				// This is the first event sent for an ad - it is possible to
				// determine whether the ad is a video ad or an overlay.
				if (!ad.isLinear()) {
					// Position AdDisplayContainer correctly for overlay.
					// Use ad.width and ad.height.
					this.videoContent.play();
				}
				break;
			case window.google.ima.AdEvent.Type.STARTED:
				// This event indicates the ad has started - the video player
				// can adjust the UI, for example display a pause button and
				// remaining time.
				if (wrapperEl) {
					wrapperEl.classList.add('gampreroll-shade');
				}
				break;
			case window.google.ima.AdEvent.Type.COMPLETE:
				// This event indicates the ad has finished - the video player
				// can perform appropriate UI actions, such as removing the timer for
				// remaining time detection.
				this.finalize();
				break;
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

		const { gampreroll } = window.bbgiconfig.dfp;

		if (gampreroll && gampreroll.unitId) {
			// Put In Delayed Guard
			setTimeout(() => {
				const { playingPrerollFlag } = this.state;
				if (!playingPrerollFlag) {
					this.finalize();
				}
			}, 3000);

			// Play the preroll
			this.playPreroll(gampreroll.unitId);
		} else {
			this.finalize();
		}
	}

	componentWillUnmount() {
		window.removeEventListener('resize', this.onResize);
		this.finalize();
	}

	finalize() {
		if (this.adsManager) {
			this.adsManager.destroy();
		}

		const { isFinalized } = this.state;
		if (!isFinalized) {
			// Mark State
			this.setState({
				startedPrerollFlag: false,
				playingPrerollFlag: false,
				isFinalized: true,
			});

			// Call Player Action
			const { adPlaybackStop } = this.props;
			adPlaybackStop(ACTION_GAM_AD_PLAYBACK_COMPLETE);
		}
	}

	render() {
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

const mapDispatchToProps = dispatch =>
	bindActionCreators({ adPlaybackStop }, dispatch);

export default connect(null, mapDispatchToProps)(GamPreroll);
