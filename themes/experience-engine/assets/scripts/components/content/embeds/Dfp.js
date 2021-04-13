import { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { IntersectionObserverContext } from '../../../context/intersection-observer';

const playerSponsorDivID = 'div-gpt-ad-1487117572008-0';
const SlotUpdateTimeInterval = 5000;

const getSlotStatsObject = () => {
	let { slotStatsObject } = window;
	if (!slotStatsObject) {
		console.log(`Creating slotStatsObject in slotVisibilityChangedHandler `);
		window.slotStatsObject = {};
		slotStatsObject = window.slotStatsObject;
	}
	return slotStatsObject;
};

const impressionViewableHandler = event => {
	const { slot } = event;
	const slotStatsObject = getSlotStatsObject();

	console.log(`impressionViewableHandler FIRED`);

	const slotID = slot.getSlotElementId();
	if (typeof slotStatsObject[slotID] === 'undefined') {
		slotStatsObject[slotID] = {
			viewPercentage: 100,
			timeVisible: 0,
		};
	} else {
		slotStatsObject[slotID].viewPercentage = 100;
	}
};

const slotVisibilityChangedHandler = event => {
	let { inViewPercentage } = event;
	const { slot } = event;
	const slotStatsObject = getSlotStatsObject();

	console.log(`slotVisibilityChangedHandler FIRED`);

	if (typeof event.inViewPercentage === 'undefined') {
		inViewPercentage = 100;
	}

	const slotID = slot.getSlotElementId();
	if (typeof slotStatsObject[slotID] === 'undefined') {
		slotStatsObject[slotID] = {
			viewPercentage: inViewPercentage,
			timeVisible: 0,
		};
	} else {
		slotStatsObject[slotID].viewPercentage = inViewPercentage;
	}
};

const slotRenderEndedHandler = event => {
	const { slot, isEmpty, size } = event;

	console.log(`slotRenderEndedHandler FIRED`);
	if (!isEmpty && size && size[1]) {
		const imageHeight = size[1];
		const slotID = slot.getSlotElementId();
		const slotElement = document.getElementById(slotID);
		const padBottomStr = window.getComputedStyle(slotElement).paddingBottom;
		console.log(`Padding Bottom String: ${padBottomStr}`);
		const padBottom =
			padBottomStr.indexOf('px') > -1 ? padBottomStr.replace('px', '') : '0';
		console.log(`Padding Bottom: ${padBottom}`);
		slotElement.style.height = `${imageHeight + parseInt(padBottom, 10)}px`;
		slotElement.classList.add('fadeInAnimation');
		slotElement.style.opacity = '1';
	}
};

class Dfp extends PureComponent {
	constructor(props) {
		const { placeholder } = props;
		super(props);

		this.state = {
			slot: false,
			interval: false,
		};

		if (placeholder !== playerSponsorDivID) {
			document.getElementById(placeholder).classList.add('fadeInAnimation');
		}

		this.onVisibilityChange = this.handleVisibilityChange.bind(this);
		this.updateSlot = this.updateSlot.bind(this);
		this.refreshSlot = this.refreshSlot.bind(this);
	}

	componentDidMount() {
		const { googletag, addedSlotListeners } = window;
		const { placeholder } = this.props;

		this.container = document.getElementById(placeholder);
		this.tryDisplaySlot();

		if (placeholder !== playerSponsorDivID) {
			this.startInterval();
			document.addEventListener('visibilitychange', this.onVisibilityChange);
		}

		// Fire sponsored ad utility to determine if
		// a sponsor ad will in fact load in the player
		this.maybeLoadedPlayerSponsorAd();

		// If Ad Blocker is enabled googletag will be absent
		if (!googletag) {
			console.log(`NO googletag FOUND IN DFP COMPONENT DID MOUNT`);
			return;
		}

		if (!addedSlotListeners) {
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

	/**
	 * @function maybeLoadedPlayerSponsorAd
	 * This is a small utility that listens for the specific
	 * sponsor ad slot in the player element. Due to the fixed
	 * CSS nature of the interface, when a Player Sponsor loads
	 * the height of certain elements (ie. nav and signin) needs
	 * to be adjusted dynamically. This utility can help add to the
	 * body to enable accurate CSS settings.
	 */
	maybeLoadedPlayerSponsorAd() {
		// Make sure that googletag.cmd exists.
		window.googletag = window.googletag || {};
		window.googletag.cmd = window.googletag.cmd || [];

		// Don't assume readiness, instead, push to queue
		window.googletag.cmd.push(() => {
			// listen for ad slot loading
			window.googletag.pubads().addEventListener('slotOnload', event => {
				// get current loaded slot id
				const idLoaded = event.slot.getSlotElementId();

				// compare against sponsor slot id
				// this value is fixed and can be found in
				// /assets/scripts/components/player/Sponsor.js
				if (idLoaded === playerSponsorDivID) {
					// Add class to body
					document
						.getElementsByTagName('body')[0]
						.classList.add('station-has-sponsor');
				}
			});
		});
	}

	componentWillUnmount() {
		const { placeholder } = this.props;
		this.destroySlot();

		if (placeholder !== playerSponsorDivID) {
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
		this.setState({
			interval: setInterval(this.updateSlot, SlotUpdateTimeInterval),
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
			console.log(`CREATED SLOT ${slot.getSlotElementId()} for ID ${unitId}`);

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

			// MFP 09/17/2020 - Added a refresh() that fires as last embed of first content block.
			//                - Calls to display should not be required.
			// googletag.display(slot);

			this.setState({ slot });

			return true;
		});
	}

	updateSlot() {
		// const { googletag } = window;
		const { slot } = this.state;
		const slotStatsObject = getSlotStatsObject();

		if (slot) {
			console.log(`update() ${slot.getSlotElementId()}`);
			const slotID = slot.getSlotElementId();
			if (typeof slotStatsObject[slotID] === 'undefined') {
				console.log(`Creating new stat item for ${slotID}`);
				slotStatsObject[slotID] = {
					viewPercentage: 0,
					timeVisible: 0,
				};
			} else if (slotStatsObject[slotID].viewPercentage > 50) {
				slotStatsObject[slotID].timeVisible += SlotUpdateTimeInterval;
				console.log(
					`Stat item for ${slotID} has was incremented to ${slotStatsObject[slotID].timeVisible} seconds of viewability`,
				);
			}

			if (slotStatsObject[slotID].timeVisible >= 30000) {
				slotStatsObject[slotID].timeVisible = 0;
				document.getElementById(slotID).classList.remove('fadeInAnimation');
				document.getElementById(slotID).classList.remove('fadeOutAnimation');
				document.getElementById(slotID).classList.add('fadeOutAnimation');
				setTimeout(() => {
					this.refreshSlot();
				}, 100);
			}
		}
	}

	refreshSlot() {
		const { googletag } = window;
		const { slot } = this.state;

		if (slot) {
			const slotID = slot.getSlotElementId();
			console.log(`REFRESH - ${slotID}`);
			googletag.cmd.push(() => {
				document.getElementById(slotID).style.opacity = '0';
				document.getElementById(slotID).classList.remove('fadeOutAnimation');
				googletag.pubads().collapseEmptyDivs(); // Stop Collapsing Empty Slots
				googletag.pubads().refresh([slot], { changeCorrelator: false });
			});
		}
	}

	destroySlot() {
		const { slot } = this.state;
		if (slot) {
			const { googletag } = window;

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
