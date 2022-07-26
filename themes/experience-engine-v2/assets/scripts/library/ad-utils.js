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

const getTopAdStatCollectionObject = () => {
	let { topAdStatsObject } = window;
	if (!topAdStatsObject) {
		window.topAdStatsObject = {};
		topAdStatsObject = window.topAdStatsObject;
	}
	return topAdStatsObject;
};

export const getTopAdStat = pageUrl => {
	if (!pageUrl) {
		throw Error('NULL Url Param in getTopAdStat()');
	}

	const alphaOnlyPageUrl = pageUrl.replace(/[^a-zA-Z0-9]/g, '');

	const topAdStatsObject = getTopAdStatCollectionObject();
	if (typeof topAdStatsObject[alphaOnlyPageUrl] === 'undefined') {
		topAdStatsObject[alphaOnlyPageUrl] = {};
	}

	return topAdStatsObject[alphaOnlyPageUrl];
};

export const setTopAdStatScrollPos = (pageUrl, scrollPos) => {
	if (!pageUrl) {
		throw Error('NULL Url Param in setTopAdStatScrollPos()');
	}
	if (!scrollPos) {
		throw Error('NULL scrollPos Param in setTopAdStatScrollPos()');
	}

	const topAdStatsObject = getTopAdStat(pageUrl);
	topAdStatsObject.scrollPos = scrollPos;
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

export const doPubadsRefreshForAllRegisteredAds = googletag => {
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
		if (slotList) {
			// const slotsToRefreshArray = [...slotList.values()];
			googletag.pubads().refresh(slotList);
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
		const lastContentTopMargin = parseInt(contentStyle.marginTop, 10);
		const adContainerTopMargin = parseInt(adContainerStyle.marginTop, 10);
		const newContentTopMargin =
			24 + (newAdHeight || 0) + (adContainerTopMargin || 0);

		console.log(
			`New Leaderboard => Old Scroll:${lastVerticalScroll} Old Top Margin:${lastContentTopMargin} New Top Margin:${newContentTopMargin}`,
		);

		contentElement.style.marginTop = `${newContentTopMargin}px`;

		if (lastVerticalScroll <= lastContentTopMargin) {
			console.log('SCROLLING BACK TO TOP');
			window.scrollTo(window.scrollX, 0);
		}
		/*
		else {
			const marginDelta = newContentTopMargin - lastContentTopMargin;
			// Adjust Margin Delta If Ad Had Not Been Loaded Before Now
			// if (!window.topAdsShown) {
			//	marginDelta -= lastContentTopMargin - 44;
			// }
			const newVerticalScroll = lastVerticalScroll + marginDelta;
			window.scrollTo(window.scrollX, newVerticalScroll);
			console.log(
				`ADJUSTED PAGE SCROLL BY ${marginDelta} TO ${newVerticalScroll} BECAUSE OF TOP AD`,
			);
		}
		*/
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

export const slotRenderEndedHandler = event => {
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
