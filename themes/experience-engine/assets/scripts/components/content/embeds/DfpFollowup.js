import React, { useEffect } from 'react';

/**
 * This embed component is responsible for any followup actions required by DFP
 */
const DfpFollowup = () => {
	useEffect(() => {
		const { googletag } = window;
		if (googletag) {
			googletag.cmd.push(() => {
				googletag.pubads().refresh(); // Refresh ALL Slots
			});
		}
	});

	return <></>;
};

export default DfpFollowup;
