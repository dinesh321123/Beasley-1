import React from 'react';
import ReactDOM from 'react-dom';
import Dfp from '../components/content/embeds/Dfp';
import ErrorBoundary from '../components/ErrorBoundary';

const TopScrollingAd = () => {
	console.log('FIRED TopScrollingAd');

	const [pageURL] = document.location.href;
	// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
	const id = 'div-top-scrolling-slot';

	// const { unitId, unitName } = window.bbgiconfig.dfp.headerad;
	const unitId = '/26918149/WAZZ_FM_Leaderboard_pos1';
	const unitName = 'top-leaderboard';
	const container = document.getElementById('top-scrolling-container');

	const children = (
		<ErrorBoundary>
			<Dfp
				key={`top-scrolling-ad-${pageURL}`}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={false}
				pageURL={pageURL}
			/>
		</ErrorBoundary>
	);

	return ReactDOM.createPortal(children, container);
};

export default TopScrollingAd;
