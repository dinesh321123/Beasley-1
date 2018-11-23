import { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Dfp extends PureComponent {

	componentDidMount() {
		const self = this;
		const { placeholder, network, unitId, unitName, targeting } = self.props;
		const { googletag } = window;

		googletag.cmd.push( () => {
			const size = window.bbgiconfig.dfp.sizes[unitName];
			const slot = googletag.defineSlot( `/${network}/${unitId}`, size, placeholder );

			let sizeMapping = false;
			if ( 'dfp_ad_leaderboard_pos1' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					.addSize( [1024, 200], [[970, 66], [970, 90], [728, 90]] )
					.addSize( [768, 200], [728, 90] )
					.addSize( [0, 0], [[320, 50], [320, 100]] )
					.build();
			} else if ( 'dfp_ad_leaderboard_pos2' === unitName ) {
				sizeMapping = googletag.sizeMapping()
					.addSize( [1024, 200], [[970, 90], [728, 90]] )
					.addSize( [768, 200], [728, 90] )
					.addSize( [0, 0], [[320, 50], [320, 100]] )
					.build();
			}

			if ( sizeMapping ) {
				slot.defineSizeMapping( sizeMapping );
			}

			for ( let i = 0; i < targeting.length; i++ ) {
				slot.setTargeting( targeting[i][0], targeting[i][1] );
			}

			slot.addService( googletag.pubads() );
			googletag.display( slot );

			self.slot = slot;
		} );
	}

	componentWillUnmount() {
		const { slot } = this;
		if ( slot ) {
			const { googletag } = window;
			googletag.destroySlots( [slot] );
		}
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
