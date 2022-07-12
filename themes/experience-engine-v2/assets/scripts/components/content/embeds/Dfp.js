import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { IntersectionObserverContext } from '../../../context';
import { logPrebidTargeting } from '../../../redux/utilities/screen/refreshAllAds';
import { isAndroid, isIOS } from '../../../library';

const playerSponsorDivID = 'div-gpt-ad-1487117572008-0';
const interstitialDivID = 'div-gpt-ad-1484200509775-3';
const topScrollingDivID = 'div-top-scrolling-slot';
const bottomAdhesionDivID = 'div-bottom-adhesion-slot';
const dropDownDivID = 'div-drop-down-slot';

const isNotSponsorOrInterstitial = placeholder => {
	return (
		placeholder !== playerSponsorDivID && placeholder !== interstitialDivID
	);
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
	if (placeholder && isNotSponsorOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage = 100;
	}
};

const slotVisibilityChangedHandler = event => {
	const { slot } = event;
	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotSponsorOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage =
			typeof event.inViewPercentage === 'undefined'
				? 100
				: event.inViewPercentage;
	}
};

const adjustContentMarginForTopAd = slotElement => {
	const contentElement = document.getElementById('inner-content');
	const adContainerElement = document.getElementById('top-scrolling-container');

	if (slotElement && adContainerElement && contentElement) {
		const slotStyle = window.getComputedStyle(slotElement);
		const contentStyle = window.getComputedStyle(contentElement);
		const adContainerStyle = window.getComputedStyle(adContainerElement);

		const lastVerticalScroll = window.scrollY;
		const lastContentTopMargin = parseInt(contentStyle.marginTop, 10);
		const slotHeight = parseInt(slotStyle.height, 10);
		const containerMarginTop = parseInt(adContainerStyle.marginTop, 10);
		const newContentTopMargin =
			24 + (slotHeight || 0) + (containerMarginTop || 0);
		contentElement.style.marginTop = `${newContentTopMargin}px`;

		const newVerticalScroll =
			lastVerticalScroll + (newContentTopMargin - lastContentTopMargin);
		window.scrollTo(window.scrollX, newVerticalScroll);
	}
};

const adjustContentPaddingForBottomAd = slotElement => {
	const containerElement = document.getElementById('main-container-div');

	if (slotElement && containerElement) {
		// If Slot Is Not Visible
		if (slotElement.offsetParent === null) {
			console.log('Slot is not visible, so setting no padding.');
			containerElement.style.paddingBottom = '0';
		} else {
			containerElement.style.paddingBottom = slotElement.style.height;
		}
	}
};

const setSotElementHeight = (placeholder, slotElement, newAdHeight) => {
	const padBottomPxStr = window.getComputedStyle(slotElement).paddingBottom;
	const padBottomNumStr =
		padBottomPxStr.indexOf('px') > -1 ? padBottomPxStr.replace('px', '') : '0';

	// Set Slot Height To New Height Plus paddingBottom
	slotElement.style.height = `${newAdHeight + parseInt(padBottomNumStr, 10)}px`;

	if (placeholder === topScrollingDivID) {
		adjustContentMarginForTopAd(slotElement);
	} else if (placeholder === bottomAdhesionDivID) {
		adjustContentPaddingForBottomAd(slotElement);
	}
};

const showSlotElement = slotElement => {
	slotElement.classList.add('fadeInAnimation');
	slotElement.style.opacity = '1';
};

const slotRenderEndedHandler = event => {
	const { slot, isEmpty, size } = event;
	const htmlVidTagArray = window.bbgiconfig.vid_ad_html_tag_csv_setting
		? window.bbgiconfig.vid_ad_html_tag_csv_setting.split(',')
		: null;

	const placeholder = slot.getSlotElementId();
	const slotElement = document.getElementById(placeholder);

	// console.log(
	//	`slotRenderEndedHandler for ${slot.getAdUnitPath()}(${placeholder}) with line item: ${lineItemId} of size: ${size}`,
	// );

	// FOR DEBUG - LOG TARGETING
	// const pbTargetKeys = slot.getTargetingKeys();
	// console.log(`Slot Keys Of Rendered Ad`);
	// pbTargetKeys.forEach(pbtk => {
	//	console.log(`${pbtk}: ${slot.getTargeting(pbtk)}`);
	// });

	if (!isEmpty) {
		if (placeholder === topScrollingDivID) {
			window.bbgiLeaderboardLoaded = true;
		} else if (placeholder === bottomAdhesionDivID) {
			window.bbgiAdhesionLoaded = true;
		}

		// Dropdown Ads May Have Display: None. Set Them To Show
		if (placeholder === dropDownDivID) {
			if (slotElement) {
				slotElement.style.display = 'flex';
			}
		}
	}

	if (placeholder && isNotSponsorOrInterstitial(placeholder)) {
		if (isEmpty) {
			console.log(`Empty Ad Returned for ${placeholder}`);
			// DropDown Ads Should Not Retain Their Realestate
			if (placeholder === dropDownDivID) {
				if (slotElement) {
					slotElement.style.display = 'none';
				}
			}

			// If Slot Is Visible
			if (slotElement.offsetParent !== null) {
				// Trick Slot to pull new Ad on next poll.
				// Set Visible Time To Huge Arbitrary MSec Value So That Next Poll Will Trigger A Refresh
				// NOTE: Minimum Poll Interval Is Set In DFP Constructor To Be Much Longer Than
				// 	Round Trip to Ad Server So That Racing/Looping Condition Is Avoided.
				getSlotStat(placeholder).timeVisible = 10000000;
			}
			if (
				placeholder === topScrollingDivID ||
				placeholder === bottomAdhesionDivID
			) {
				// Set Ad Height To 0 Since No Ad
				setSotElementHeight(placeholder, slotElement, 0);
			}
		} else {
			let adSize;
			if (size && size.length === 2 && (size[0] !== 1 || size[1] !== 1)) {
				adSize = size;
				// console.log(`Prebid Ad Not Shown - Using Size: ${adSize}`);
			} else if (slot.getTargeting('hb_size')) {
				// We ASSUME when an incomplete size is sent through event, we are dealing with Prebid.
				// Compute Size From hb_size.
				const hbSizeString = slot.getTargeting('hb_size').toString();
				// console.log(`Prebid Sizestring: ${hbSizeString}`);
				const idxOfX = hbSizeString.toLowerCase().indexOf('x');
				if (idxOfX > -1) {
					const widthString = hbSizeString.substr(0, idxOfX);
					const heightString = hbSizeString.substr(idxOfX + 1);
					adSize = [];
					adSize[0] = parseInt(widthString, 10);
					adSize[1] = parseInt(heightString, 10);
				}

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

					/* Disable GA Stats due to high usage
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
					*/
				}
			}

			// Adjust Container Div Height
			if (adSize && adSize[0] && adSize[1]) {
				setSotElementHeight(placeholder, slotElement, adSize[1]);
			}

			showSlotElement(slotElement);

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

		// Initial leaderboard height = 50 for Mobile, otherwise use setting
		const initialLeaderboardAdHeight =
			isAndroid() || isIOS()
				? 50
				: parseInt(bbgiconfig.ad_leaderboard_initial_height_setting, 10);

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
			initialLeaderboardAdHeight,
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
		const { adjustedUnitId, initialLeaderboardAdHeight } = this.state;

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

				// Adjust Default Content Margin To Help With Page Shift
				const contentElement = document.getElementById('inner-content');
				if (contentElement) {
					contentElement.style.marginTop = `${initialLeaderboardAdHeight +
						44}px`;
				}
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
		const placeholderElement = document.getElementById(placeholder);
		placeholderElement.classList.remove('fadeInAnimation');
		placeholderElement.classList.remove('fadeOutAnimation');
		placeholderElement.style.opacity = '0';
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
		const { unitName } = this.props;
		if (this.state && !this.state.slot) {
			this.registerSlot();
		}

		if (unitName === 'drop-down') {
			console.log(`Displaying ${unitName}`);
			const { googletag } = window;
			googletag.cmd.push(() => {
				console.log('Calling googletag.display() on DropDown Ad');
				googletag.display(dropDownDivID);
			});
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
