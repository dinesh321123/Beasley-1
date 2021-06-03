import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { IntersectionObserverContext } from '../../../context/intersection-observer';

const playerSponsorDivID = 'div-gpt-ad-1487117572008-0';
const interstitialDivID = 'div-gpt-ad-1484200509775-3';
const isNotPlayerOrInterstitial = placeholder => {
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
	const { slot, isEmpty, size } = event;
	const htmlVidTagArray = window.bbgiconfig.vid_ad_html_tag_csv_setting
		? window.bbgiconfig.vid_ad_html_tag_csv_setting.split(',')
		: null;

	const placeholder = slot.getSlotElementId();
	if (placeholder && isNotPlayerOrInterstitial(placeholder)) {
		const slotElement = document.getElementById(placeholder);
		if (isEmpty) {
			// If Slot Is Visible
			if (slotElement.offsetParent !== null) {
				// Set Visible Time To Huge Arbitrary MSec Value So That Next Poll Will Trigger A Refresh
				// NOTE: Minimum Poll Interval Is Set In DFP Constructor To Be Much Longer Than
				// 	Round Trip to Ad Server So That Racing/Looping Condition Is Avoided.
				getSlotStat(placeholder).timeVisible = 10000000;
			}
		} else {
			// Adjust Container Div Height
			if (size && size[1]) {
				const imageHeight = size[1];
				const padBottomStr = window.getComputedStyle(slotElement).paddingBottom;
				const padBottom =
					padBottomStr.indexOf('px') > -1
						? padBottomStr.replace('px', '')
						: '0';
				slotElement.style.height = `${imageHeight + parseInt(padBottom, 10)}px`;
			}

			slotElement.classList.add('fadeInAnimation');
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

			console.log(`IsVideo - ${getSlotStat(placeholder).isVideo}`);
		}
	}
};

class Dfp extends PureComponent {
	constructor(props) {
		const { bbgiconfig } = window;
		super(props);

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
		};

		this.onVisibilityChange = this.handleVisibilityChange.bind(this);
		this.updateSlotVisibleTimeStat = this.updateSlotVisibleTimeStat.bind(this);
		this.refreshSlot = this.refreshSlot.bind(this);
	}

	isConfiguredToRunInterval() {
		const { placeholder, unitName } = this.props;
		const { isRotateAdsEnabled } = this.state;

		return (
			unitName === 'right-rail' ||
			(isRotateAdsEnabled && isNotPlayerOrInterstitial(placeholder))
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

	registerSlot() {
		const { placeholder, unitId, unitName, targeting } = this.props;
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
			if (unitName === 'top-leaderboard') {
				sizeMapping = googletag
					.sizeMapping()

					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common desktop banner formats
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();
			} else if (unitName === 'in-list') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// Same as top-leaderboard
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();
			} else if (unitName === 'in-list-gallery') {
				sizeMapping = googletag
					.sizeMapping()

					// does not display on very small screens
					.addSize([0, 0], [])

					// accepts common small screen banner formats
					.addSize([300, 0], [[300, 250]])
					.addSize([320, 0], [[300, 250]])

					.build();
			} else if (unitName === 'bottom-leaderboard') {
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// accepts common desktop banner formats
					.addSize([300, 0], [[320, 50], [320, 100], 'fluid'])
					.addSize([1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'])

					.build();
			} else if (unitName === 'adhesion') {
				console.log('PLAYER AD');
				sizeMapping = googletag
					.sizeMapping()
					// does not display on small screens
					.addSize([0, 0], [])

					// accepts only two sizes
					.addSize([1350, 0], [[728, 90], [970, 90], 'fluid'])

					.build();
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
			}

			if (sizeMapping) {
				slot.defineSizeMapping(sizeMapping);
			}

			for (let i = 0; i < targeting.length; i++) {
				slot.setTargeting(targeting[i][0], targeting[i][1]);
			}

			this.setState({ slot });
			return true;
		});
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
			const slotStat = getSlotStat(placeholder);
			if (slotStat.viewPercentage > 50) {
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
					placeholderClasslist.add('fadeOutAnimation');
				}
				setTimeout(() => {
					this.refreshSlot();
				}, 100);
			}
		}
	}

	refreshSlot() {
		const { googletag } = window;
		const { placeholder } = this.props;
		const { slot } = this.state;

		if (slot) {
			googletag.cmd.push(() => {
				googletag.pubads().collapseEmptyDivs(); // Stop Collapsing Empty Slots
				googletag.pubads().refresh([slot]);
				const placeholderElement = document.getElementById(placeholder);
				placeholderElement.style.opacity = '0';
				placeholderElement.classList.remove('fadeOutAnimation');
			});
		}
	}

	destroySlot() {
		const { placeholder } = this.props;
		const { slot } = this.state;
		if (slot) {
			const { googletag } = window;
			// Remove Slot Stat Property
			delete getSlotStatsCollectionObject()[placeholder];

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
};

Dfp.defaultProps = {
	targeting: [],
};

Dfp.contextType = IntersectionObserverContext;

export default Dfp;
