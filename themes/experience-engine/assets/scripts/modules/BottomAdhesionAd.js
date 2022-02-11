import React, { useState } from 'react';
import Dfp from '../components/content/embeds/Dfp';

const BottomAdhesionAd = props => {
	const [pageURL] = useState(props);
	// backward compatibility with the legacy theme to make sure that everything keeps working correctly
	// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
	const id = 'div-gpt-ad-player-0';

	const { unitId, unitName } = window.bbgiconfig.dfp.player;

	const className = 'live-player';

	// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
	return React.createElement('div', { id, className }, [
		<Dfp
			placeholder={id}
			unitId={unitId}
			unitName={unitName}
			shouldMapSizes={false}
			pageURL={pageURL}
		/>,
	]);
};

/*
BottomAdhesionAd.propTypes = {
	pageURL: PropTypes.string,
};

BottomAdhesionAd.defaultProps = {
	pageURL: '/',
};
*/

export default BottomAdhesionAd;
