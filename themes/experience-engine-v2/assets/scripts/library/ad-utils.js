const playerSponsorDivID = 'div-gpt-ad-1487117572008-0';
export const interstitialDivID = 'div-gpt-ad-1484200509775-3';
export const topScrollingDivID = 'div-top-scrolling-slot';
const bottomAdhesionDivID = 'div-bottom-adhesion-slot';
export const dropDownDivID = 'div-drop-down-slot';

export const isNotSponsorOrInterstitial = placeholder => {
	return (
		placeholder !== playerSponsorDivID && placeholder !== interstitialDivID
	);
};

export const getSlotStatsCollectionObject = () => {
	let { slotStatsObject } = window;
	if (!slotStatsObject) {
		window.slotStatsObject = {};
		slotStatsObject = window.slotStatsObject;
	}
	return slotStatsObject;
};

export const getSlotStat = placeholder => {
	if (!placeholder) {
		throw Error('NULL Placeholder Param in getSlotStat()');
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

export const placeholdersOutsideContentArray = [
	topScrollingDivID,
	bottomAdhesionDivID,
];

export const registerSlotStatForRefresh = placeholder => {
	if (!placeholder) {
		throw Error('NULL Placeholder Param in registerSlotStatForRefresh()');
	}

	if (
		!placeholdersOutsideContentArray.includes(placeholder) ||
		!getSlotStatsCollectionObject()[placeholder]
	) {
		console.log(`Creating slotStat for ${placeholder}`);
		const slotStat = getSlotStat(placeholder);
		// Set refresh flag to true for all Ads except DropDown
		slotStat.shouldRefresh = placeholder !== dropDownDivID;
	}
};

const getSlotsFromGAM = (googletag, placeHolderArray) => {
	const allSlots = googletag.pubads().getSlots();
	console.log(`AD STACK CURRENTLY HOLDS ${allSlots.length} ADS`);
	return allSlots.filter(
		s => placeHolderArray.indexOf(s.getSlotElementId()) > -1,
	);
};

export const logPrebidTargeting = unitId => {
	const pbjs = window.pbjs || {};
	const targeting = pbjs.getAdserverTargeting();
	let retval;

	if (targeting) {
		Object.keys(targeting).map(tkey => {
			if (targeting[tkey].hb_bidder && (!unitId || unitId === tkey)) {
				console.log(
					`High Prebid Ad ID: ${tkey} Bidder: ${targeting[tkey].hb_bidder} Price: ${targeting[tkey].hb_pb}`,
				);

				/* Disable GA Stats due to high usage
				try {
					window.ga('send', {
						hitType: 'event',
						eventCategory: 'PrebidTarget',
						eventAction: `${targeting[tkey].hb_bidder}`,
						eventLabel: `${tkey}`,
						eventValue: `${parseInt(
							parseFloat(targeting[tkey].hb_pb) * 100,
							10,
						)}`,
					});
				} catch (ex) {
					console.log(`ERROR Sending to Google Analytics: `, ex);
				}
			    */

				// Set retval when UnitID was specified and we have a high bidder
				if (unitId && unitId === tkey) {
					retval = targeting[tkey];
				}
			}
			return tkey;
		});
	}

	return retval;
};

const HEADER_BID_TIMEOUT = 1500;
const headerBidFlags = {
	amazonUAMAccountedFor: false,
	prebidAccountedFor: false,
	gamRequestWasSent: false,
};

const sendGAMRequest = slotList => {
	if (!headerBidFlags.gamRequestWasSent) {
		headerBidFlags.gamRequestWasSent = true;

		const { googletag } = window;
		const unitIdList = slotList.map(s => s.getAdUnitPath());
		// MFP 11/10/2021 - SLOT Param Not Working - pbjs.setTargetingForGPTAsync([slot]);
		window.pbjs.setTargetingForGPTAsync(unitIdList);
		unitIdList.map(uid => logPrebidTargeting(uid));
		googletag.pubads().refresh(slotList, { changeCorrelator: false });
	}
};

const bidsBackHandler = slotList => {
	if (
		headerBidFlags.amazonUAMAccountedFor &&
		headerBidFlags.prebidAccountedFor
	) {
		sendGAMRequest(slotList);
	}
};

export const requestHeaderBids = slotList => {
	headerBidFlags.gamRequestWasSent = false;
	window.lastReturnedAmazonUAMBids = null;

	// Set Amazon UAM bid sizes if a window.initializeAPS function exists, which indicates UAM enabled
	const amazonBidArray =
		window.initializeAPS && window.getAmazonUAMSlots(slotList);

	if (amazonBidArray && amazonBidArray.length > 0) {
		headerBidFlags.amazonUAMAccountedFor = false;
		window.initializeAPS();
		window.apstag.fetchBids(
			{
				slots: amazonBidArray,
				timeout: HEADER_BID_TIMEOUT,
			},
			function(bids) {
				window.lastReturnedAmazonUAMBids = bids;
				window.apstag.setDisplayBids();
				headerBidFlags.amazonUAMAccountedFor = true;
				bidsBackHandler(slotList);
			},
		);
	} else {
		headerBidFlags.amazonUAMAccountedFor = true;
	}

	headerBidFlags.prebidAccountedFor = false;
	window.pbjs.que = window.pbjs.que || [];
	window.pbjs.que.push(() => {
		window.pbjs.requestBids({
			timeout: HEADER_BID_TIMEOUT,
			adUnitCodes: slotList.map(s => s.getAdUnitPath()),
			bidsBackHandler: () => {
				headerBidFlags.prebidAccountedFor = true;
				bidsBackHandler(slotList);
			},
		});
	});

	// Attempt to Refresh GAM 100ms after timeout just in case prebid calls failed
	window.setTimeout(() => {
		sendGAMRequest(slotList);
	}, HEADER_BID_TIMEOUT + 100);
};

export const doPubadsRefreshForAllRegisteredAds = googletag => {
	const { prebid_enabled } = window.bbgiconfig;
	const statsCollectionObject = getSlotStatsCollectionObject();
	const statsObjectKeys = Object.keys(statsCollectionObject);
	if (statsObjectKeys) {
		const placeholdersToRefresh = statsObjectKeys.filter(
			statsKey =>
				statsCollectionObject[statsKey].shouldRefresh ||
				placeholdersOutsideContentArray.includes(statsKey),
		);
		placeholdersToRefresh.push(interstitialDivID); // Add Interstitial To List Of Placeholders To Refresh

		const slotList = getSlotsFromGAM(googletag, placeholdersToRefresh);

		// Push JS processing to next cycle for better Lighthouse Score
		if (slotList) {
			setTimeout(() => {
				// const slotsToRefreshArray = [...slotList.values()];
				googletag.cmd.push(() => {
					if (prebid_enabled) {
						requestHeaderBids(slotList);
					} else {
						googletag.pubads().refresh(slotList);
					}
				});
			}, 0);
		}

		// Mark All Slots as shown
		statsObjectKeys.forEach(statsKey => {
			statsCollectionObject[statsKey].shouldRefresh = false;
		});
	}
};

export const impressionViewableHandler = event => {
	const { slot } = event;
	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotSponsorOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage = 100;
	}
};

export const slotVisibilityChangedHandler = event => {
	const { slot } = event;
	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotSponsorOrInterstitial(placeholder)) {
		getSlotStat(placeholder).viewPercentage =
			typeof event.inViewPercentage === 'undefined'
				? 100
				: event.inViewPercentage;
	}
};

const adjustContentMarginForTopAd = newAdHeight => {
	console.log(`Adjusting Page For Top Ad Number ${window.topAdsShown + 1}`);
	const contentElement = document.getElementById('inner-content');
	const adContainerElement = document.getElementById('top-scrolling-container');

	if (adContainerElement && contentElement) {
		const contentStyle = window.getComputedStyle(contentElement);
		const adContainerStyle = window.getComputedStyle(adContainerElement);

		const lastVerticalScroll = window.scrollY;
		// If First Time Shown, use holder variable set in page-utils
		const lastContentTopMargin =
			window.topAdsShown || !window.lastContentTopMargin
				? parseInt(contentStyle.marginTop, 10)
				: window.lastContentTopMargin;

		console.log(
			`FOR DEBUG - contentStyle.marginTop: ${parseInt(
				contentStyle.marginTop,
				10,
			)}  window.lastContentTopMargin ${window.lastContentTopMargin}`,
		);

		const adContainerTopMargin = parseInt(adContainerStyle.marginTop, 10);
		const newContentTopMargin =
			24 + (newAdHeight || 0) + (adContainerTopMargin || 0);

		contentElement.style.marginTop = `${newContentTopMargin}px`;

		console.log(
			`New Leaderboard => Old Scroll:${lastVerticalScroll} Old Top Margin:${lastContentTopMargin} New Top Margin:${newContentTopMargin}`,
		);

		// Adjust Scroll
		const marginDelta = newContentTopMargin - lastContentTopMargin;
		if (lastVerticalScroll <= newContentTopMargin) {
			console.log('SCROLLING BACK TO TOP BECAUSE NEW AD WOULD BE CUT OFF');
			window.scrollTo(window.scrollX, 0);
		} else {
			const newVerticalScroll = lastVerticalScroll + marginDelta;
			window.scrollTo(window.scrollX, newVerticalScroll);
			console.log(
				`ADJUSTED PAGE SCROLL BY ${marginDelta} TO ${newVerticalScroll} BECAUSE OF TOP AD`,
			);
		}

		window.topAdsShown++;
	}
};

const adjustContentPaddingForAdhesionAd = slotElement => {
	const mainContainerElement = document.getElementById('main-container-div');
	const adContainerElement = document.getElementById(
		'bottom-adhesion-container',
	);

	if (slotElement && mainContainerElement && adContainerElement) {
		// If Slot Is Not Visible
		if (slotElement.offsetParent === null) {
			console.log('Adhesion Slot is not visible, so setting no padding.');
			mainContainerElement.style.paddingBottom = '0';
			adContainerElement.style.height = '0';
		} else {
			mainContainerElement.style.paddingBottom = slotElement.style.height;
			adContainerElement.style.height = slotElement.style.height;
		}
	}
};

const setSlotElementHeight = (placeholder, slotElement, newAdHeight) => {
	const padBottomPxStr = window.getComputedStyle(slotElement).paddingBottom;
	const padBottomNumStr =
		padBottomPxStr.indexOf('px') > -1 ? padBottomPxStr.replace('px', '') : '0';

	// Set Slot Height To New Height Plus paddingBottom
	slotElement.style.height = `${newAdHeight + parseInt(padBottomNumStr, 10)}px`;

	if (placeholder === topScrollingDivID) {
		adjustContentMarginForTopAd(newAdHeight);
	} else if (placeholder === bottomAdhesionDivID) {
		adjustContentPaddingForAdhesionAd(slotElement);
	}
};

export const showSlotElement = slotElement => {
	slotElement.classList.add('fadeInAnimation');
	slotElement.style.opacity = '1';
};

export const hidePlaceholder = placeholder => {
	if (placeholder) {
		const placeholderElement = document.getElementById(placeholder);
		if (placeholderElement) {
			placeholderElement.classList.remove('fadeInAnimation');
			placeholderElement.classList.remove('fadeOutAnimation');
			placeholderElement.style.opacity = '0';
		}
	}
};

const removeAllHtmlElementsExceptForLast = elementArray => {
	if (elementArray) {
		const numElementsToDelete = elementArray.length - 1;
		let idx = 0;
		while (idx < numElementsToDelete) {
			elementArray[idx].remove();
			idx++;
		}
	}
};

const removeExtraIntegratorLinksFromHead = () => {
	// Remove Pairs of Redundant Tags That GAM Is Injecting Into Head Upon Each Ad Refresh.
	// NOTE: THIS IS BRITTLE AND IF GAM CHANGES THEIR LINKS WE NEED TO ADJUST
	// Currently the links like like so:
	// <link rel="preload" href="https://adservice.google.com/adsid/integrator.js?domain=wmmr.beasley.test" as="script">
	// <script type="text/javascript" src="https://adservice.google.com/adsid/integrator.js?domain=wmmr.beasley.test"></script>
	const SEARCHSTRING = 'adservice.google.com/adsid/integrator.js?';
	const linkElements = Array.from(
		document.querySelectorAll('head > link'),
	).filter(el => el.href && el.href.indexOf(SEARCHSTRING) > -1);
	const scriptElements = Array.from(
		document.querySelectorAll('head > script'),
	).filter(el => el.src && el.src.indexOf(SEARCHSTRING) > -1);

	removeAllHtmlElementsExceptForLast(linkElements);
	removeAllHtmlElementsExceptForLast(scriptElements);
};

const getSizeArrayFromString = sizeString => {
	// console.log(`Prebid Sizestring: ${sizeString}`);
	const idxOfX = sizeString.toLowerCase().indexOf('x');
	if (idxOfX > -1) {
		const widthString = sizeString.substr(0, idxOfX);
		const heightString = sizeString.substr(idxOfX + 1);
		const retval = [];
		retval[0] = parseInt(widthString, 10);
		retval[1] = parseInt(heightString, 10);
		return retval;
	}
	return null;
};

export const slotRenderEndedHandler = event => {
	removeExtraIntegratorLinksFromHead();

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
			console.log('Empty Ad Returned');
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
				setSlotElementHeight(placeholder, slotElement, 0);
			}
		} else {
			let adSize;
			if (size && size.length === 2 && (size[0] !== 1 || size[1] !== 1)) {
				adSize = size;
				// console.log(`Prebid Ad Not Shown - Using Size: ${adSize}`);
			} else if (
				slot.getTargeting('hb_size') &&
				slot.getHtml().indexOf('prebid') > -1
			) {
				// We ASSUME when an incomplete size was sent through, but we are dealing with Prebid.
				// Compute Size From hb_size.
				const hbSizeString = slot.getTargeting('hb_size').toString();
				adSize = getSizeArrayFromString(hbSizeString);

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
				}
			} else if (
				window.lastReturnedAmazonUAMBids &&
				slot.getHtml().indexOf('apstag') > -1
			) {
				// Assume Amazon UAM
				const bidForSlot = window.lastReturnedAmazonUAMBids.find(
					bid => bid.slotID === slot.getSlotElementId(),
				);
				if (bidForSlot) {
					adSize = getSizeArrayFromString(bidForSlot.amznsz);
				}
				console.log(
					`Rendering Amazon Bid Size: ${
						adSize ? JSON.stringify(adSize) : 'NONE'
					}`,
				);
			} else {
				console.log('UNKNOWN AD TYPE RETURNED');
			}

			// Adjust Container Div Height
			if (adSize && adSize[0] && adSize[1]) {
				setSlotElementHeight(placeholder, slotElement, adSize[1]);
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
