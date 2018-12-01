import { PureComponent } from 'react';
import PropTypes from 'prop-types';

import { isInViewport } from '../../../library/dom';

class Dfp extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.slot = false;
		self.interval = false;
		self.displayed = false;

		self.onVisibilityChange = self.handleVisibilityChange.bind( self );
		self.refreshSlot = self.refreshSlot.bind( self );
		self.tryDisplaySlot = self.tryDisplaySlot.bind( self );
	}

	componentDidMount() {
		const self = this;

		window.addEventListener( 'scroll', self.tryDisplaySlot, true );
		window.addEventListener( 'resize', self.tryDisplaySlot );

		self.tryDisplaySlot();

		if ( 'dfp_ad_right_rail_pos1' === self.props.unitName ) {
			self.startInterval();
			document.addEventListener( 'visibilitychange', self.onVisibilityChange );
		}
	}

	componentWillUnmount() {
		const self = this;

		self.removeListeners();
		self.destroySlot();

		if ( 'dfp_ad_right_rail_pos1' === self.props.unitName ) {
			self.stopInterval();
			document.removeEventListener( 'visibilitychange', self.onVisibilityChange );
		}
	}

	removeListeners() {
		const self = this;
		window.removeEventListener( 'scroll', self.tryDisplaySlot, true );
		window.removeEventListener( 'resize', self.tryDisplaySlot );
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
		const { placeholder, network, unitId, unitName, targeting } = self.props;
		const { googletag, bbgiconfig } = window;

		googletag.cmd.push( () => {
			const size = bbgiconfig.dfp.sizes[unitName];
			const slot = googletag
				.defineSlot( `/${network}/${unitId}`, size, placeholder )
				.addService( googletag.pubads() );

			let sizeMapping = false;
			if ( 'dfp_ad_leaderboard_pos1' === unitName || 'dfp_ad_inlist_infinite' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					.addSize( [970, 200], ['fluid', [970, 250], [970, 90], [728, 90]] )
					.addSize( [729, 200], ['fluid', [728, 90]] )
					.addSize( [0, 0], ['fluid', [320, 100], [320, 50]] )
					.build();
			} else if ( 'dfp_ad_leaderboard_pos2' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					.addSize( [970, 200], [[970, 250], [970, 90], [728, 90]] )
					.addSize( [729, 200], [728, 90] )
					.addSize( [0, 0], [[320, 100], [320, 50]] )
					.build();
			} else if ( 'dfp_ad_right_rail_pos1' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					.addSize( [1060, 200], [[300, 600], [300, 250]] )
					.addSize( [0, 0], [] )
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
		const { slot, displayd } = this;
		const { googletag } = window;

		if ( slot && displayd ) {
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
			const { placeholder } = self.props;
			const container = document.getElementById( placeholder );

			if ( !self.slot && isInViewport( container, 0, 100 ) ) {
				self.removeListeners();
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
	network: PropTypes.string.isRequired,
	unitId: PropTypes.string.isRequired,
	unitName: PropTypes.string.isRequired,
	targeting: PropTypes.arrayOf( PropTypes.array ),
};

Dfp.defaultProps = {
	targeting: [],
};

export default Dfp;
