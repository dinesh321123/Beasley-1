import React from 'react';

import Dfp from '../content/embeds/Dfp';

function Sponsor() {
	const { network, unitId, unitName } = window.bbgiconfig.dfp.player;
	const placeholder = 'sponsor';
	
	// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
	return React.createElement( 'div', { id: placeholder }, [
		<Dfp key="sponsor" placeholder={placeholder} network={network} unitId={unitId} unitName={unitName} />,
	] );
}

export default Sponsor;
