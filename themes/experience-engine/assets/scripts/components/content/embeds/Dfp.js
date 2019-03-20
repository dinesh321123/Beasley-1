import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import IntersectionObserverContext from '../../../context/intersection-observer';

class Dfp extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.slot = false;
		self.interval = false;

		self.onVisibilityChange = self.handleVisibilityChange.bind( self );
		self.refreshSlot = self.refreshSlot.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { placeholder } = self.props;

		self.container = document.getElementById( placeholder );
		self.context.observe( self.container, self.tryDisplaySlot.bind( self ) );

		if ( 'right-rail' === self.props.unitName ) {
			self.startInterval();
			document.addEventListener( 'visibilitychange', self.onVisibilityChange );
		}
	}

	componentWillUnmount() {
		const self = this;

		self.context.unobserve( self.container );
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
		const self = this;
		self.interval = setInterval( self.refreshSlot, 20000 ); // 20 sec
	}

	stopInterval() {
		const self = this;
		clearInterval( self.interval );
		self.interval = false;
	}

	registerSlot() {
		const self = this;
		const { placeholder, unitId, unitName, targeting } = self.props;
		const { googletag, bbgiconfig } = window;

		if ( !unitId ) {
			return;
		}

		googletag.cmd.push( () => {
			const size = bbgiconfig.dfp.sizes[unitName];
			const slot = googletag
				.defineSlot( unitId, size, placeholder )
				.addService( googletag.pubads() );

			let sizeMapping = false;
			if ( 'top-leaderboard' === unitName ) {
				sizeMapping = googletag.sizeMapping()

					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common desktop banner formats
					.addSize( [300, 0], [[320, 50], [320, 100], 'fluid'] )
					.addSize( [728, 0], [[728, 90], 'fluid'] )
					.addSize( [900, 0], [[320, 50], [320, 100], 'fluid'] )
					.addSize( [918, 0], [[728, 90], 'fluid'] )
					.addSize( [1160, 0], [[728, 90], [970, 90], [970, 250], 'fluid'] )

					.build();
			} else if ( 'in-list' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common desktop banner formats
					.addSize( [300, 0], [[300, 250], [320, 50], [320, 100]] )
					.addSize( [728, 0], [[728, 90]] )
					.addSize( [900, 0], [[300, 250], [320, 50], [320, 100]] )
					.addSize( [918, 0], [[728, 90]] )
					.addSize( [1060, 0], [[300, 250], [320, 50], [320, 100]] )
					.addSize( [1238, 0], [[728, 90]] )
					.addSize( [1480, 0], [[728, 90], [970, 90], [970, 250]] )
					.build();

			} else if ( 'bottom-leaderboard' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					// does not display on small screens
					.addSize( [0, 0], [] )

					// accepts common desktop banner formats
					.addSize( [300, 0], [[320, 50], [320, 100]] )
					.addSize( [728, 0], [[728, 90]] )
					.addSize( [900, 0], [[320, 50], [320, 100]] )
					.addSize( [918, 0], [[728, 90]] )
					.addSize( [1160, 0], [[728, 90], [970, 90], [970, 250]] )

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

			self.slot = slot;
		} );
	}

	refreshSlot() {
		const { slot } = this;
		const { googletag } = window;

		if ( slot ) {
			googletag.pubads().refresh( [slot] );
		}
	}

	destroySlot() {
		const { slot } = this;
		if ( slot ) {
			const { googletag } = window;

			googletag.cmd.push( () => {
				googletag.destroySlots( [slot] );
			} );
		}
	}

	tryDisplaySlot() {
		window.requestAnimationFrame( () => {
			const self = this;

			self.context.unobserve( self.container );

			if ( !self.slot ) {
				self.registerSlot();
			}
		} );
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
