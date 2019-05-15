import React from 'react';
import PropTypes from 'prop-types';

import Dfp from '../content/embeds/Dfp';

function Sponsor( { className, minWidth, maxWidth } ) {
	if ( 0 < minWidth && ! window.matchMedia( `(min-width: ${minWidth}px)` ).matches ) {
		return false;
	}

	if ( 0 < maxWidth && ! window.matchMedia( `(max-width: ${maxWidth}px)` ).matches ) {
		return false;
	}

	// backward compatibility with the legacy theme to make sure that everything keeps working correctly
	const placeholder = 'div-gpt-ad-1487117572008-0';
	const { unitId, unitName } = window.bbgiconfig.dfp.player;
	const params = {
		id: placeholder,
		className,
	};

	// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
	return React.createElement( 'div', params, [
		<Dfp key="sponsor" placeholder={placeholder} unitId={unitId} unitName={unitName} />,
	] );
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
