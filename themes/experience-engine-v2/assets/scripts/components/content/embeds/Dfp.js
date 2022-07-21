import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { IntersectionObserverContext } from '../../../context';
import { logPrebidTargeting } from '../../../redux/utilities/screen/refreshAllAds';
import {
	impressionViewableHandler,
	slotVisibilityChangedHandler,
	slotRenderEndedHandler,
	isNotSponsorOrInterstitial,
	getSlotStat,
	registerSlotStatForRefresh,
	topScrollingDivID,
	hidePlaceholder,
	showSlotElement,
	getSlotStatsCollectionObject,
} from '../../../library/ad-utils';

class Dfp extends PureComponent {
	constructor(props) {
		super(props);
		const { placeholder, unitId, unitName, pageURL } = props;
		const { bbgiconfig } = window;

		// No InContent Ads On Affiliate Pages
		if (this.isCreationCancelled(placeholder, unitName, pageURL)) {
			return;
		}

		this.onVisibilityChange = this.handleVisibilityChange.bind(this);
		this.hideSlot = this.hideSlot.bind(this);
		this.showSlot = this.showSlot.bind(this);
		this.isConfiguredToRunInterval = this.isConfiguredToRunInterval.bind(this);
		this.startInterval = this.startInterval.bind(this);
		this.stopInterval = this.stopInterval.bind(this);
		this.registerSlot = this.registerSlot.bind(this);
		this.updateSlotVisibleTimeStat = this.updateSlotVisibleTimeStat.bind(this);
		this.refreshSlot = this.refreshSlot.bind(this);
		this.destroySlot = this.destroySlot.bind(this);
		this.tryDisplaySlot = this.tryDisplaySlot.bind(this);

		this.pushRefreshBidIntoGoogleTag = this.pushRefreshBidIntoGoogleTag.bind(
			this,
		);

		// Prebid Functions
		this.loadPrebid = this.loadPrebid.bind(this);
		this.bidsBackHandler = this.bidsBackHandler.bind(this);
		this.getPrebidBidders = this.getPrebidBidders.bind(this);
		this.getBidderRubicon = this.getBidderRubicon.bind(this);
		this.getBidderAppnexus = this.getBidderAppnexus.bind(this);
		this.getBidderIx = this.getBidderIx.bind(this);
		this.getBidderResetDigital = this.getBidderResetDigital.bind(this);

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

		const isAffiliateMarketingPage = this.isAffiliateMarketingPage(pageURL);

		const adjustedUnitId = this.getAdjustedUnitId(unitId, unitName, pageURL);
		// console.log(`Adjusted Ad Unit: ${adjustedUnitId}`);

		// Initialize State. NOTE: Ensure that Minimum Poll Intervavl Is Much Longer Than
		// 	Round Trip to Ad Server. Initially we enforce 5 second minimum.
		this.state = {
			adjustedUnitId,
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
			resetDigitalEnabled: false, // 01/02/2022 - Disable ResetDigital: bbgiconfig.ad_reset_digital_enabled === 'on',
			prebidEnabled: bbgiconfig.prebid_enabled && !isAffiliateMarketingPage,
		};
	}

	isConfiguredToRunInterval() {
		// Lack of State likely means Creation was cancelled
		if (!this.state) {
			return false;
		}

		const { placeholder, unitName } = this.props;
		const { isRotateAdsEnabled } = this.state;

		return (
			unitName === 'right-rail' ||
			(isRotateAdsEnabled && isNotSponsorOrInterstitial(placeholder))
		);
	}

	isAffiliateMarketingPage(pageURL) {
		return (
			pageURL.indexOf('/category/shopping/') > -1 ||
			pageURL.indexOf('/shows/must-haves/') > -1 ||
			pageURL.indexOf('/musthaves/') > -1
		);
	}

	isIncontentAdOnAffiliatePage(unitName, pageURL) {
		return (
			(unitName === 'in-list' ||
				unitName === 'in-list-gallery' ||
				unitName === 'in-content') &&
			this.isAffiliateMarketingPage(pageURL)
		);
	}

	isAdInEmbeddedContent(placeholder) {
		// Embedded content detected when slot is child element of a Div with class .am-meta-item-description
		const slotElement = document.getElementById(placeholder);
		return !!slotElement && !!slotElement.closest('.am-meta-item-description');
	}

	isCreationCancelled(placeholder, unitName, pageURL) {
		return (
			this.isIncontentAdOnAffiliatePage(unitName, pageURL) ||
			this.isAdInEmbeddedContent(placeholder)
		);
	}

	getAdjustedUnitId(unitId, unitName, pageURL) {
		let retval = unitId;
		// Change Ad Unit Depending On AdName If We Are On An Affiliate Page
		if (unitId && pageURL && this.isAffiliateMarketingPage(pageURL)) {
			const nameStartIdx = unitId.lastIndexOf('/');
			if (nameStartIdx > -1) {
				const prefix = unitId.substring(0, nameStartIdx + 1);
				switch (unitName) {
					case 'top-leaderboard':
						retval = `${prefix}MUST_HAVES_Leaderboard_pos1`;
						break;
					case 'bottom-leaderboard':
						retval = `${prefix}MUST_HAVES_Leaderboard_pos2`;
						break;
					case 'right-rail':
						retval = `${prefix}MUST_HAVES_RightRail_pos1`;
						break;
					case 'adhesion':
						retval = `${prefix}MUST_HAVES_Adhesion`;
						break;
					default:
						break;
				}
			}
		}
		return retval;
	}

	componentDidMount() {
		const { googletag } = window;
		const { placeholder } = this.props;

		// Lack of State likely means Creation was cancelled
		if (!this.state) {
			return;
		}

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
		// Lack of State likely means Creation was cancelled
		if (!this.state) {
			return;
		}

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
		const { placeholder, unitName, targeting } = this.props;
		const { googletag, bbgiconfig } = window;
		const { adjustedUnitId } = this.state;

		if (!document.getElementById(placeholder)) {
			console.log(
				`NOT REGISTERRING AD BECAUSE NO PLACE HOLDER FOR - ${unitName}`,
			);
			return;
		}

		// If Adblocker is enabled googletag will be absent
		if (!googletag) {
			return;
		}

		if (!adjustedUnitId) {
			return;
		}

		googletag.cmd.push(() => {
			const size = bbgiconfig.dfp.sizes[unitName];
			const slot = googletag.defineSlot(adjustedUnitId, size, placeholder);

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
					.addSize(
						[300, 0],
						[
							[320, 50],
							[320, 100],
						],
					)
					.addSize(
						[800, 0],
						[
							[320, 50],
							[320, 100],
							[728, 90],
						],
					)
					.addSize(
						[1020, 0],
						[
							[728, 90],
							[970, 90],
						],
					)
					.addSize(
						[1020, 650],
						[
							[728, 90],
							[970, 90],
							[970, 250],
						],
					)
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
						minViewPort: [800, 0],
						sizes: [
							[320, 50],
							[320, 100],
							[728, 90],
						],
					},
					{
						minViewPort: [1020, 0],
						sizes: [
							[728, 90],
							[970, 90],
						],
					},
					{
						minViewPort: [1020, 650],
						sizes: [
							[728, 90],
							[970, 90],
							[970, 250],
						],
					},
				];
			} else if (unitName === 'drop-down') {
				console.log('Building sizes for Dropdown');
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])
					.addSize([300, 0], [[320, 50]])
					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [[320, 50]],
					},
				];
			} else if (unitName === 'in-list') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// Same as top-leaderboard
					.addSize(
						[300, 0],
						[
							[320, 50],
							[320, 100],
						],
					)
					.addSize(
						[800, 0],
						[
							[320, 50],
							[320, 100],
							[728, 90],
						],
					)
					.addSize(
						[1020, 0],
						[
							[728, 90],
							[970, 90],
						],
					)
					.addSize(
						[1020, 650],
						[
							[728, 90],
							[970, 90],
							[970, 250],
						],
					)

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
						minViewPort: [800, 0],
						sizes: [
							[320, 50],
							[320, 100],
							[728, 90],
						],
					},
					{
						minViewPort: [1020, 0],
						sizes: [
							[728, 90],
							[970, 90],
						],
					},
					{
						minViewPort: [1020, 650],
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
					.addSize(
						[300, 0],
						[
							[320, 50],
							[320, 100],
						],
					)
					.addSize(
						[800, 0],
						[
							[320, 50],
							[320, 100],
							[728, 90],
						],
					)
					.addSize(
						[1020, 0],
						[
							[728, 90],
							[970, 90],
						],
					)
					.addSize(
						[1020, 650],
						[
							[728, 90],
							[970, 90],
							[970, 250],
						],
					)

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
						minViewPort: [800, 0],
						sizes: [
							[320, 50],
							[320, 100],
							[728, 90],
						],
					},
					{
						minViewPort: [1020, 0],
						sizes: [
							[728, 90],
							[970, 90],
						],
					},
					{
						minViewPort: [1020, 650],
						sizes: [
							[728, 90],
							[970, 90],
							[970, 250],
						],
					},
				];
			} else if (unitName === 'adhesion') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common desktop banner formats
					.addSize([300, 0], [[320, 50]])
					.addSize(
						[800, 0],
						[
							[320, 50],
							[728, 90],
						],
					)
					.addSize(
						[1020, 0],
						[
							[728, 90],
							[970, 90],
						],
					)

					.build();

				prebidSizeConfig = [
					{ minViewPort: [0, 0], sizes: [] },
					{
						minViewPort: [300, 0],
						sizes: [[320, 50]],
					},
					{
						minViewPort: [800, 0],
						sizes: [
							[320, 50],
							[728, 90],
						],
					},
					{
						minViewPort: [1020, 0],
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

			const prebidEnabled = this.loadPrebid(adjustedUnitId, prebidSizeConfig);

			for (let i = 0; i < targeting.length; i++) {
				slot.setTargeting(targeting[i][0], targeting[i][1]);
			}

			registerSlotStatForRefresh(placeholder, slot);

			this.setState({ slot, prebidEnabled });
			return true;
		});
	}

	// HTML layout and CSS styles are preventing Google slotVisibilityChangedHandler event from properly detecting viewability
	topAdReallyIsVisible(slotElement) {
		const topAdHeight = parseInt(slotElement.style.height, 10);
		return window.scrollY < topAdHeight / 2;
	}

	updateSlotVisibleTimeStat() {
		const { placeholder } = this.props;
		const {
			slot,
			slotPollMillisecs,
			slotRefreshMillisecs,
			slotVideoRefreshMillisecs,
		} = this.state;

		if (slot) {
			const placeholderElement = document.getElementById(placeholder);
			const slotStat = getSlotStat(placeholder);

			if (slotStat.viewPercentage > 50) {
				if (
					placeholder !== topScrollingDivID ||
					this.topAdReallyIsVisible(placeholderElement)
				) {
					slotStat.timeVisible += slotPollMillisecs;
				}
			}

			const msecThreshold =
				slotStat.isVideo === true
					? slotVideoRefreshMillisecs
					: slotRefreshMillisecs;
			if (slotStat.timeVisible >= msecThreshold) {
				if (placeholderElement.style.opacity === '1') {
					const placeholderClasslist = placeholderElement.classList;
					placeholderClasslist.remove('fadeInAnimation');
					placeholderClasslist.remove('fadeOutAnimation');
					placeholderClasslist.add('fadeOutAnimation');
				}
				setTimeout(() => {
					this.refreshSlot();
				}, 100);
			}
		}
	}

	bidsBackHandler() {
		const { googletag } = window;
		const { slot, adjustedUnitId } = this.state;
		// MFP 11/10/2021 - SLOT Param Not Working - pbjs.setTargetingForGPTAsync([slot]);
		window.pbjs.setTargetingForGPTAsync([adjustedUnitId]);
		logPrebidTargeting(adjustedUnitId);
		googletag.pubads().refresh([slot], { changeCorrelator: false });
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

	hideSlot() {
		const { placeholder } = this.props;
		hidePlaceholder(placeholder);
	}

	showSlot() {
		const { slot } = this.state;
		if (slot) {
			const placeholder = slot.getSlotElementId();
			const slotElement = document.getElementById(placeholder);
			showSlotElement(slotElement);
		}
	}

	refreshSlot() {
		const { googletag } = window;
		const { slot, prebidEnabled, adjustedUnitId } = this.state;

		if (slot) {
			googletag.cmd.push(() => {
				googletag.pubads().collapseEmptyDivs(); // Stop Collapsing Empty Slots
				if (prebidEnabled) {
					this.pushRefreshBidIntoGoogleTag(adjustedUnitId, slot);
				} else {
					googletag.pubads().refresh([slot]);
				}
				this.hideSlot();
			});
		}
	}

	destroySlot() {
		// Lack of State likely means Creation was cancelled
		if (!this.state) {
			return;
		}

		const { placeholder } = this.props;
		const { slot, prebidEnabled, adjustedUnitId } = this.state;

		if (slot) {
			const { googletag } = window;
			// Remove Slot Stat Property
			delete getSlotStatsCollectionObject()[placeholder];

			if (prebidEnabled) {
				console.log(`Removing Ad Unit From Prebid: ${adjustedUnitId}`);
				const pbjs = window.pbjs || {};
				// pbjs.removeAdUnit(adUnitCode)
				pbjs.removeAdUnit(adjustedUnitId);
			}

			console.log(`Destroying Slot: ${placeholder}`);

			if (googletag && googletag.destroySlots) {
				googletag.destroySlots([slot]);
			}
		}
	}

	tryDisplaySlot() {
		if (this.state && !this.state.slot) {
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
	pageURL: PropTypes.string,
};

Dfp.defaultProps = {
	targeting: [],
	pageURL: '',
};

Dfp.contextType = IntersectionObserverContext;

export default Dfp;
