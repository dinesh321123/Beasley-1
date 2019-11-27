import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import IntersectionObserverContext from '../../../context/intersection-observer';

class Dfp extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			slot     : false,
			interval : false,
		};

		self.onVisibilityChange = self.handleVisibilityChange.bind( self );
		self.refreshSlot = self.refreshSlot.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { placeholder } = self.props;

		self.container = document.getElementById( placeholder );
		self.tryDisplaySlot();

		if ( 'right-rail' === self.props.unitName ) {
			self.startInterval();
			document.addEventListener( 'visibilitychange', self.onVisibilityChange );
		}
	}

	componentWillUnmount() {
		const self = this;

		self.destroySlot();

		if ( 'right-rail' === self.props.unitName ) {
			self.stopInterval();
			document.removeEventListener( 'visibilitychange', self.onVisibilityChange );
		}
	}

	handleVisibilityChange() {
		const self = this;

		if ( 'hidden' === document.visibilityState ) {
			self.stopInterval();
		} else if ( !self.interval ) {
			self.startInterval();
		}
	}

	startInterval() {
		this.setState( {
			interval: setInterval( this.refreshSlot, 20000 ), // 20 sec
		} );
	}

	stopInterval() {
		clearInterval( this.state.interval );
		this.setState( { interval: false } );
	}

	registerSlot() {
		const self = this;
		const { placeholder, unitId, unitName, targeting } = self.props;
		const { googletag, bbgiconfig } = window;

		if ( ! document.getElementById( placeholder ) ) {
			return;
		}

		// If Adblocker is enabled googletag will be absent
		if ( ! googletag ) {
			return;
		}

		if ( ! unitId ) {
			return;
		}

		googletag.cmd.push( () => {
			let size = bbgiconfig.dfp.sizes[unitName];
			let slot = googletag
				.defineSlot( unitId, size, placeholder );


			// If Slot was already defined this will be null
			// Ignored to fix the exception
			if ( ! slot ) {
				return false;
			}

			slot.addService( googletag.pubads() );

			if ( 'player-sponsorship' === unitName ) {

				// reference to sponsor slot
				const sponsorSlot = slot;

				// listen to slot loading
				googletag.pubads().addEventListener( 'slotOnload', function( event ) {

					// get current loaded slot id
					const idLoaded = event.slot.getSlotElementId();

					// compare against sponsor slot id
					if ( idLoaded === sponsorSlot.getSlotElementId() ) {
						console.log( 'sponsor loaded' );
					}

				} );
			}



			let sizeMapping = false;
			if ( 'top-leaderboard' === unitName ) {
				sizeMapping = googletag.sizeMapping()

					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common desktop banner formats
					.addSize( [300, 0], [[320, 50], [320, 100], 'fluid'] )
					.addSize( [1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'] )

					.build();
			} else if ( 'in-list' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					// does not display on small screens
					.addSize( [0, 0], [] )

					// Same as top-leaderboard
					.addSize( [300, 0], [[320, 50], [320, 100], 'fluid'] )
					.addSize( [1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'] )

					.build();

			} else if ( 'in-list-gallery' === unitName ) {

				sizeMapping = googletag.sizeMapping()

					// does not display on very small screens
					.addSize( [0, 0], [] )

					// accepts common small screen banner formats
					.addSize( [300, 0], [[300, 250]] )
					.addSize( [320, 0], [[300, 250], [320, 50], [320, 100]] )

					.build();

			} else if ( 'bottom-leaderboard' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common desktop banner formats
					.addSize( [300, 0], [[320, 50], [320, 100], 'fluid'] )
					.addSize( [1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'] )

					.build();
			} else if ( 'right-rail' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					// does not display on small screens
					.addSize( [0, 0], [] )

					// rail comes in on larger screens
					.addSize( [1060, 0], [[300, 250], [300, 600]] )

					.build();
			} else if ( 'in-content' === unitName ) {
				sizeMapping = googletag.sizeMapping()

					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common box formats
					.addSize( [300, 0], [[300, 250], [1, 1]] )

					.build();
			}

			if ( sizeMapping ) {
				slot.defineSizeMapping( sizeMapping );
			}

			for ( let i = 0; i < targeting.length; i++ ) {
				slot.setTargeting( targeting[i][0], targeting[i][1] );
			}

			googletag.display( slot );
			self.setState( { slot: slot } );
		} );
	}

	refreshSlot() {
		const { slot } = this.state;
		const { googletag } = window;

		if ( slot ) {
			googletag.pubads().refresh( [slot] );
		}
	}

	destroySlot() {
		const { slot } = this.state;
		if ( slot ) {
			const { googletag } = window;

			if ( googletag && googletag.destroySlots ) {
				googletag.destroySlots( [slot] );
			}
		}
	}

	tryDisplaySlot() {
		if ( ! this.state.slot ) {
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
	targeting: PropTypes.arrayOf( PropTypes.array ),
};

Dfp.defaultProps = {
	targeting: [],
};

Dfp.contextType = IntersectionObserverContext;

export default Dfp;
