import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

import Dfp from '../content/embeds/Dfp';

class Sponsor extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { render: this.getRender() };

		self.onResize = self.handleResize.bind( self );
		self.onRef = self.handleSlotRef.bind( self );
	}

	componentDidMount() {
		window.addEventListener( 'resize', this.onResize );
	}

	componentWillUnmount() {
		window.removeEventListener( 'resize', this.onResize );
	}

	getRender() {
		const { minWidth, maxWidth } = this.props;

		if ( 0 < minWidth && window.matchMedia( `(min-width: ${minWidth}px)` ).matches ) {
			return true;
		}

		if ( 0 < maxWidth && window.matchMedia( `(max-width: ${maxWidth}px)` ).matches ) {
			return true;
		}

		return false;
	}

	handleResize() {
		const self = this;
		window.requestAnimationFrame( () => {
			const render = self.getRender();
			if ( render != self.state.render ) {
				self.setState( { render } );
			}
		} );
	}

	handleSlotRef( dfp ) {
		if ( dfp ) {
			setTimeout( dfp.refreshSlot.bind( dfp ), 50 );
		}
	}

	render() {
		const self = this;
		const { render } = self.state;
		if ( ! render ) {
			return false;
		}

		// backward compatibility with the legacy theme to make sure that everything keeps working correctly
		// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
		const id = 'div-gpt-ad-1487117572008-0';
		const { unitId, unitName } = window.bbgiconfig.dfp.player;
		const { className, style } = self.props;

		// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
		return React.createElement( 'div', { id, className, style }, [
			<Dfp key="sponsor" ref={self.onRef} placeholder={id} unitId={unitId} unitName={unitName} />,
		] );
	}
}

Sponsor.propTypes = {
	className: PropTypes.string.isRequired,
	minWidth: PropTypes.number,
	maxWidth: PropTypes.number,
};

Sponsor.defaultProps = {
	minWidth: 0,
	maxWidth: 0,
};

export default Sponsor;
