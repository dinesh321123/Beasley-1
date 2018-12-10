import React from 'react';

import Dfp from '../content/embeds/Dfp';

function Sponsor() {
	const { network, unitId, unitName } = window.bbgiconfig.dfp.player;

	// backward compatibility with the legacy theme to make sure that everything keeps working correctly
	const placeholder = 'div-gpt-ad-1487117572008-0';

	const params = {
		id: placeholder,
		className: 'sponsor',
	};

	// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
	return React.createElement( 'div', params, [
		<Dfp key="sponsor" placeholder={placeholder} network={network} unitId={unitId} unitName={unitName} />,
	] );
}

export default Sponsor;
