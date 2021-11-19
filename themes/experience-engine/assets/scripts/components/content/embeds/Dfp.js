import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { IntersectionObserverContext } from '../../../context';
import { logPrebidTargeting } from '../../../redux/utilities/screen/refreshAllAds';

const playerSponsorDivID = 'div-gpt-ad-1487117572008-0';
const interstitialDivID = 'div-gpt-ad-1484200509775-3';
const playerAdhesionDivID = 'div-gpt-ad-player-0';
const isNotPlayerOrInterstitial = placeholder => {
	return (
		placeholder !== playerSponsorDivID && placeholder !== interstitialDivID
	);
};

let resetAdContainerWidthTimeout;
const changeAdhesionAdContainerWidth = (
	placeholder,
	newWidthInt = 1,
	mSecDelay = 1500,
) => {
	if (resetAdContainerWidthTimeout) {
		clearTimeout(resetAdContainerWidthTimeout);
	}

	resetAdContainerWidthTimeout = setTimeout(() => {
		const slotElement = document.getElementById(placeholder);
		slotElement.style.width = `${newWidthInt}px`;
		slotElement.style.transition = 'all .5s ease-in-out';
	}, mSecDelay);
};

const getSlotStatsCollectionObject = () => {
	let { slotStatsObject } = window;
	if (!slotStatsObject) {
		window.slotStatsObject = {};
		slotStatsObject = window.slotStatsObject;
	}
	return slotStatsObject;
};

const getSlotStat = placeholder => {
	if (!placeholder) {
		throw Error('NULL Slot ID Param in getSlotStat()');
	}

	const slotStatsObject = getSlotStatsCollectionObject();
	if (typeof slotStatsObject[placeholder] === 'undefined') {
		slotStatsObject[placeholder] = {
			viewPercentage: 0,
			timeVisible: 0,
			isVideo: false,
		};
	}

	return slotStatsObject[placeholder];
};

const impressionViewableHandler = event => {
	const { slot } = event;
	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotPlayerOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage = 100;
	}
};

const slotVisibilityChangedHandler = event => {
	const { slot } = event;
	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotPlayerOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage =
			typeof event.inViewPercentage === 'undefined'
				? 100
				: event.inViewPercentage;
	}
};

const slotRenderEndedHandler = event => {
	const { slot, lineItemId, isEmpty, size } = event;
	const htmlVidTagArray = window.bbgiconfig.vid_ad_html_tag_csv_setting
		? window.bbgiconfig.vid_ad_html_tag_csv_setting.split(',')
		: null;

	const placeholder = slot.getSlotElementId();

	console.log(
		`slotRenderEndedHandler for ${slot.getAdUnitPath()}(${placeholder}) with line item: ${lineItemId} of size: ${size}`,
	);

	// FOR DEBUG - LOG TARGETING
	const pbTargetKeys = slot.getTargetingKeys();
	console.log(`Slot Keys Of Rendered Ad`);
	pbTargetKeys.forEach(pbtk => {
		console.log(`${pbtk}: ${slot.getTargeting(pbtk)}`);
	});

	if (placeholder && isNotPlayerOrInterstitial(placeholder)) {
		const slotElement = document.getElementById(placeholder);
		if (isEmpty) {
			// If Slot Is Visible
			if (slotElement.offsetParent !== null) {
				// Trick Slot to pull new Ad on next poll.
				// Set Visible Time To Huge Arbitrary MSec Value So That Next Poll Will Trigger A Refresh
				// NOTE: Minimum Poll Interval Is Set In DFP Constructor To Be Much Longer Than
				// 	Round Trip to Ad Server So That Racing/Looping Condition Is Avoided.
				getSlotStat(placeholder).timeVisible = 10000000;
			}
		} else {
			let adSize;
			if (size && size.length === 2 && (size[0] !== 1 || size[1] !== 1)) {
				adSize = size;
			} else if (slot.getTargeting('hb_size')) {
				// We ASSUME when an incomplete size is sent through event, we are dealing with Prebid.
				// Compute Size From hb_size.
				const hbSizeString = slot.getTargeting('hb_size').toString();
				console.log(`Prebid Sizestring: ${hbSizeString}`);
				const idxOfX = hbSizeString.toLowerCase().indexOf('x');
				if (idxOfX > -1) {
					const widthString = hbSizeString.substr(0, idxOfX);
					const heightString = hbSizeString.substr(idxOfX + 1);
					adSize = [];
					adSize[0] = parseInt(widthString, 10);
					adSize[1] = parseInt(heightString, 10);
				}

				// Now Send GA Stats
				if (
					slot &&
					slot.getTargeting('hb_bidder') &&
					slot
						.getTargeting('hb_bidder')
						.toString()
						.trim()
				) {
					console.log(
						`PREBID AD SHOWN - ${slot.getTargeting(
							'hb_bidder',
						)} - ${slot.getAdUnitPath()} - ${slot.getTargeting('hb_pb')}`,
					);

					try {
						window.ga('send', {
							hitType: 'event',
							eventCategory: 'PrebidAdShown',
							eventAction: `${slot.getTargeting('hb_bidder')}`,
							eventLabel: `${slot.getAdUnitPath()}`,
							eventValue: `${parseInt(
								parseFloat(slot.getTargeting('hb_pb')) * 100,
								10,
							)}`,
						});
					} catch (ex) {
						console.log(`ERROR Sending to Google Analytics: `, ex);
					}
				}
			}

			console.log(`USING Size: ${adSize}`);
			// Adjust Container Div Height
			if (adSize && adSize[0] && adSize[1]) {
				const imageWidth = adSize[0];
				const imageHeight = adSize[1];
				const padBottomStr = window.getComputedStyle(slotElement).paddingBottom;
				const padBottom =
					padBottomStr.indexOf('px') > -1
						? padBottomStr.replace('px', '')
						: '0';
				slotElement.style.height = `${imageHeight + parseInt(padBottom, 10)}px`;
				// Adjust Width Of Adhesion Ad
				if (placeholder === playerAdhesionDivID) {
					changeAdhesionAdContainerWidth(placeholder, imageWidth, 1);
				}
			}

			if (placeholder !== playerAdhesionDivID) {
				slotElement.classList.add('fadeInAnimation');
			}
			slotElement.style.opacity = '1';
			getSlotStat(placeholder).timeVisible = 0; // Reset Timeout So That Next Few Polls Do Not Trigger A Refresh
			const slotHTML = slot.getHtml();
			let isVideo = false;
			if (slotHTML && htmlVidTagArray) {
				htmlVidTagArray.forEach(tag => {
					isVideo = isVideo || slotHTML.indexOf(tag) > -1;
				});
			}
			getSlotStat(placeholder).isVideo = isVideo;
		}
	}
};

class Dfp extends PureComponent {
	constructor(props) {
		super(props);
		const { pageURL } = props;
		const { bbgiconfig } = window;
		this.getIsAffiliateMarketingPage = this.getIsAffiliateMarketingPage.bind(
			this,
		);
		this.onVisibilityChange = this.handleVisibilityChange.bind(this);
		this.updateSlotVisibleTimeStat = this.updateSlotVisibleTimeStat.bind(this);
		this.refreshSlot = this.refreshSlot.bind(this);
		this.loadPrebid = this.loadPrebid.bind(this);
		this.pushRefreshBidIntoGoogleTag = this.pushRefreshBidIntoGoogleTag.bind(
			this,
		);
		this.bidsBackHandler = this.bidsBackHandler.bind(this);
		this.destroySlot = this.destroySlot.bind(this);
		this.getPrebidBidders = this.getPrebidBidders.bind(this);
		this.getBidderRubicon = this.getBidderRubicon.bind(this);
		this.getBidderAppnexus = this.getBidderAppnexus.bind(this);
		this.getBidderIx = this.getBidderIx.bind(this);

		const slotPollSecs = parseInt(
			bbgiconfig.ad_rotation_polling_sec_setting,
			10,
		);
		const slotRefreshSecs = parseInt(
			bbgiconfig.ad_rotation_refresh_sec_setting,
			10,
		);
		const slotVideoRefreshSecs = parseInt(
			bbgiconfig.ad_vid_rotation_refresh_sec_setting,
			10,
		);

		const isAffiliateMarketingPage = this.getIsAffiliateMarketingPage(pageURL);

		// Initialize State. NOTE: Ensure that Minimum Poll Intervavl Is Much Longer Than
		// 	Round Trip to Ad Server. Initially we enforce 5 second minimum.
		this.state = {
			slot: false,
			interval: false,
			isRotateAdsEnabled: bbgiconfig.ad_rotation_enabled !== 'off',
			slotPollMillisecs:
				slotPollSecs && slotPollSecs >= 5 ? slotPollSecs * 1000 : 5000,
			slotRefreshMillisecs:
				slotRefreshSecs && slotRefreshSecs >= 15
					? slotRefreshSecs * 1000
					: 30000,
			slotVideoRefreshMillisecs:
				slotVideoRefreshSecs && slotVideoRefreshSecs >= 30
					? slotVideoRefreshSecs * 1000
					: 60000,
			ixSiteID: bbgiconfig.ad_ix_siteid_setting,
			rubiconZoneID: bbgiconfig.ad_rubicon_zoneid_setting,
			appnexusPlacementID: bbgiconfig.ad_appnexus_placementid_setting,
			resetDigitalEnabled: bbgiconfig.ad_reset_digital_enabled !== 'off',
			prebidEnabled: bbgiconfig.prebid_enabled && !isAffiliateMarketingPage,
		};
	}

	isConfiguredToRunInterval() {
		const { placeholder, unitName } = this.props;
		const { isRotateAdsEnabled } = this.state;

		return (
			unitName === 'right-rail' ||
			(isRotateAdsEnabled && isNotPlayerOrInterstitial(placeholder))
		);
	}

	getIsAffiliateMarketingPage(pageURL) {
		return (
			pageURL.indexOf('/category/shopping/') > -1 ||
			pageURL.indexOf('/shows/must-haves/') > -1 ||
			pageURL.indexOf('/musthaves/') > -1
		);
	}

	componentDidMount() {
		const { googletag } = window;
		const { placeholder } = this.props;

		this.container = document.getElementById(placeholder);
		this.tryDisplaySlot();

		if (this.isConfiguredToRunInterval()) {
			this.startInterval();
			document.addEventListener('visibilitychange', this.onVisibilityChange);
		}

		// If Ad Blocker is enabled googletag will be absent
		if (!googletag) {
			throw Error(`NO googletag FOUND IN DFP COMPONENT DID MOUNT`);
			// return;
		}

		if (!window.addedSlotListeners) {
			window.addedSlotListeners = true;
			googletag.cmd.push(() => {
				googletag
					.pubads()
					.addEventListener('impressionViewable', impressionViewableHandler);
				googletag
					.pubads()
					.addEventListener(
						'slotVisibilityChanged',
						slotVisibilityChangedHandler,
					);
				googletag
					.pubads()
					.addEventListener('slotRenderEnded', slotRenderEndedHandler);
			});
		}
	}

	componentWillUnmount() {
		this.destroySlot();

		if (this.isConfiguredToRunInterval()) {
			this.stopInterval();
			document.removeEventListener('visibilitychange', this.onVisibilityChange);
		}
	}

	handleVisibilityChange() {
		if (document.visibilityState === 'hidden') {
			this.stopInterval();
		} else if (!this.interval) {
			this.startInterval();
		}
	}

	startInterval() {
		const { slotPollMillisecs } = this.state;
		this.setState({
			interval: setInterval(this.updateSlotVisibleTimeStat, slotPollMillisecs),
		});
	}

	stopInterval() {
		clearInterval(this.state.interval);
		this.setState({ interval: false });
	}

	getBidderRubicon() {
		const { rubiconZoneID } = this.state;
		if (!rubiconZoneID) {
			return null;
		}

		const retval = {
			bidder: 'rubicon',
			params: {
				accountId: 18458,
				siteId: 375130,
				zoneId: parseInt(rubiconZoneID, 10),
			},
		};

		return retval;
	}

	getBidderAppnexus() {
		const { appnexusPlacementID } = this.state;
		if (!appnexusPlacementID) {
			return null;
		}

		const retval = {
			bidder: 'appnexus',
			params: {
				placementId: parseInt(appnexusPlacementID, 10),
			},
		};

		return retval;
	}

	getBidderIx() {
		const { ixSiteID } = this.state;
		if (!ixSiteID) {
			return null;
		}

		const retval = {
			bidder: 'ix',
			params: {
				siteId: parseInt(ixSiteID, 10),
			},
		};

		return retval;
	}

	getBidderResetDigital() {
		const { resetDigitalEnabled } = this.state;
		if (!resetDigitalEnabled) {
			return null;
		}

		const retval = {
			bidder: 'resetdigital',
			params: {
				pubId: '44',
			},
		};

		return retval;
	}

	getPrebidBidders() {
		const retval = [];

		retval.push(this.getBidderRubicon());
		retval.push(this.getBidderAppnexus());
		retval.push(this.getBidderIx());
		retval.push(this.getBidderResetDigital());

		return retval.filter(bidObj => bidObj);
	}

	// Returns whether Prebid is actually Enabled for this slot
	loadPrebid(unitID, prebidSizes) {
		const { prebidEnabled } = this.state;
		if (!prebidEnabled || !unitID || !prebidSizes) {
			console.log('PREBID DISABLED'); // TODO - Remove After Debugged
			return false;
		}

		const prebidBidders = this.getPrebidBidders();
		if (!prebidBidders || prebidBidders.length === 0) {
			console.log('No Bidders Enabled - PREBID Dysfunctional');
			return false;
		}

		const pbjs = window.pbjs || {};
		pbjs.que = pbjs.que || [];

		const adUnits = [
			{
				code: unitID,
				mediaTypes: {
					banner: {
						sizeConfig: prebidSizes,
					},
				},
				bids: prebidBidders,
			},
		];

		pbjs.que.push(() => {
			pbjs.setConfig({
				bidderTimeout: 1000,
				rubicon: { singleRequest: true },
				priceGranularity: {
					buckets: [
						{
							min: 0,
							max: 5,
							increment: 0.01,
						},
						{
							min: 5,
							max: 20,
							increment: 0.05,
						},
						{
							min: 20,
							max: 50,
							increment: 0.5,
						},
					],
				},
			});

			pbjs.addAdUnits(adUnits);
		});

		return true;
	}

	registerSlot() {
		const {
			placeholder,
			unitId,
			unitName,
			targeting,
			shouldMapSizes,
		} = this.props;
		const { googletag, bbgiconfig } = window;

		if (!document.getElementById(placeholder)) {
			return;
		}

		// If Adblocker is enabled googletag will be absent
		if (!googletag) {
			return;
		}

		if (!unitId) {
			return;
		}

		googletag.cmd.push(() => {
			const size = bbgiconfig.dfp.sizes[unitName];
			const slot = googletag.defineSlot(unitId, size, placeholder);

			// If Slot was already defined this will be null
			// Ignored to fix the exception
			if (!slot) {
				return false;
			}

			slot.addService(googletag.pubads());

			let sizeMapping = false;
			let prebidSizeConfig = false;
			if (unitName === 'top-leaderboard') {
				sizeMapping = googletag
					.sizeMapping()

					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common desktop banner formats
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [
							[320, 50],
							[320, 100],
						],
					},
					{
						minViewPort: [1160, 0],
						sizes: [
							[728, 90],
							[970, 90],
							[970, 250],
						],
					},
				];
			} else if (unitName === 'in-list') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// Same as top-leaderboard
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [
							[320, 50],
							[320, 100],
						],
					},
					{
						minViewPort: [1160, 0],
						sizes: [
							[728, 90],
							[970, 90],
							[970, 250],
						],
					},
				];
			} else if (unitName === 'in-list-gallery') {
				sizeMapping = googletag
					.sizeMapping()

					// does not display on very small screens
					.addSize([0, 0], [])

					// accepts common small screen banner formats
					.addSize([300, 0], [[300, 250]])
					.addSize([320, 0], [[300, 250]])

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [[300, 250]],
					},
					{
						minViewPort: [320, 0],
						sizes: [[300, 250]],
					},
				];
			} else if (unitName === 'bottom-leaderboard') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common desktop banner formats
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [
							[320, 50],
							[320, 100],
						],
					},
					{
						minViewPort: [1160, 0],
						sizes: [
							[728, 90],
							[970, 90],
							[970, 250],
						],
					},
				];
			} else if (unitName === 'adhesion') {
				if (shouldMapSizes) {
					sizeMapping = googletag
						.sizeMapping()
						// does not display on 0 width
						.addSize([0, 0], [])

						// Div visibility is controlled in react so always show at small ad when at least 1 pixel wide
						.addSize([1, 0], [[728, 90]])

						// accepts both sizes
						.addSize(
							[1400, 0],
							[
								[728, 90],
								[970, 90],
							],
						)
						.build();
				}

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [1, 0],
						sizes: [[728, 90]],
					},
					{
						minViewPort: [1400, 0],
						sizes: [
							[728, 90],
							[970, 90],
						],
					},
				];
			} else if (unitName === 'right-rail') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// rail comes in on larger screens
					.addSize(
						[1060, 0],
						[
							[300, 250],
							[300, 600],
						],
					)

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [1060, 0],
						sizes: [
							[300, 250],
							[300, 600],
						],
					},
				];
			} else if (unitName === 'in-content') {
				sizeMapping = googletag
					.sizeMapping()

					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common box formats
					.addSize(
						[300, 0],
						[
							[300, 250],
							[1, 1],
						],
					)
					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [
							[300, 250],
							[1, 1],
						],
					},
				];
			}

			if (sizeMapping) {
				slot.defineSizeMapping(sizeMapping);
			}

			const prebidEnabled = this.loadPrebid(unitId, prebidSizeConfig);

			for (let i = 0; i < targeting.length; i++) {
				slot.setTargeting(targeting[i][0], targeting[i][1]);
			}

			this.setState({ slot, prebidEnabled });
			return true;
		});
	}

	updateSlotVisibleTimeStat() {
		const { placeholder, unitName } = this.props;
		const {
			slot,
			slotPollMillisecs,
			slotRefreshMillisecs,
			slotVideoRefreshMillisecs,
		} = this.state;

		if (slot) {
			const slotStat = getSlotStat(placeholder);

			if (unitName === 'adhesion') {
				const playerElement = document.getElementById('live-player');
				// adhesion ads are enabled when screen > window.playerAdThreshold (1250 or 1350)
				if (
					playerElement &&
					playerElement.offsetWidth > window.playerAdThreshold
				) {
					slotStat.timeVisible += slotPollMillisecs;
				}
			} else if (slotStat.viewPercentage > 50) {
				slotStat.timeVisible += slotPollMillisecs;
			}

			const msecThreshold =
				slotStat.isVideo === true
					? slotVideoRefreshMillisecs
					: slotRefreshMillisecs;
			if (slotStat.timeVisible >= msecThreshold) {
				const placeholderElement = document.getElementById(placeholder);
				if (placeholderElement.style.opacity === '1') {
					const placeholderClasslist = placeholderElement.classList;
					placeholderClasslist.remove('fadeInAnimation');
					placeholderClasslist.remove('fadeOutAnimation');
					if (unitName !== 'adhesion') {
						placeholderClasslist.add('fadeOutAnimation');
					}
				}
				setTimeout(() => {
					this.refreshSlot();
				}, 100);
			}
		}
	}

	bidsBackHandler() {
		const { googletag } = window;
		const { unitId } = this.props;
		const { slot } = this.state;
		// MFP 11/10/2021 - SLOT Param Not Working - pbjs.setTargetingForGPTAsync([slot]);
		window.pbjs.setTargetingForGPTAsync([unitId]);
		const pbTargeting = logPrebidTargeting(unitId);
		const pbTargetKeys = Object.keys(pbTargeting);
		googletag.pubads().refresh([slot], { changeCorrelator: false });

		console.log(`Slot Keys After Refresh`);
		pbTargetKeys.forEach(pbtk => {
			console.log(`${pbtk}: ${slot.getTargeting(pbtk)}`);
		});
	}

	pushRefreshBidIntoGoogleTag(unitId, slot) {
		const { prebidEnabled } = this.state;

		if (!prebidEnabled) {
			const { googletag } = window;
			googletag.pubads().refresh([slot]);
			return; // EXIT FUNCTION
		}

		window.pbjs.que = window.pbjs.que || [];
		window.pbjs.que.push(() => {
			const PREBID_TIMEOUT = 2000;
			window.pbjs.requestBids({
				timeout: PREBID_TIMEOUT,
				adUnitCodes: [unitId],
				bidsBackHandler: this.bidsBackHandler,
			});
		});
	}

	refreshSlot() {
		const { googletag } = window;
		const { placeholder, unitName, unitId } = this.props;
		const { slot, prebidEnabled } = this.state;

		if (slot) {
			googletag.cmd.push(() => {
				googletag.pubads().collapseEmptyDivs(); // Stop Collapsing Empty Slots
				if (prebidEnabled) {
					this.pushRefreshBidIntoGoogleTag(unitId, slot);
				} else {
					googletag.pubads().refresh([slot]);
				}
				const placeholderElement = document.getElementById(placeholder);
				placeholderElement.classList.remove('fadeOutAnimation');
				if (unitName === 'adhesion') {
					changeAdhesionAdContainerWidth(placeholder, 1, 2000);
				} else {
					placeholderElement.style.opacity = '0';
				}
			});
		}
	}

	destroySlot() {
		const { placeholder, unitId } = this.props;
		const { slot, prebidEnabled } = this.state;

		if (slot) {
			const { googletag } = window;
			// Remove Slot Stat Property
			delete getSlotStatsCollectionObject()[placeholder];

			if (prebidEnabled) {
				console.log(`Removing Ad Unit From Prebid: ${unitId}`);
				const pbjs = window.pbjs || {};
				// pbjs.removeAdUnit(adUnitCode)
				pbjs.removeAdUnit(unitId);
			}

			console.log(`Destroying Slot: ${placeholder}`);

			if (googletag && googletag.destroySlots) {
				googletag.destroySlots([slot]);
			}
		}
	}

	tryDisplaySlot() {
		if (!this.state.slot) {
			this.registerSlot();
		}
	}

	render() {
		return false;
	}
}

Dfp.propTypes = {
	placeholder: PropTypes.string.isRequired,
	unitId: PropTypes.string.isRequired,
	unitName: PropTypes.string.isRequired,
	targeting: PropTypes.arrayOf(PropTypes.array),
	shouldMapSizes: PropTypes.bool,
	pageURL: PropTypes.string,
};

Dfp.defaultProps = {
	targeting: [],
	shouldMapSizes: true,
	pageURL: '',
};

Dfp.contextType = IntersectionObserverContext;

export default Dfp;
