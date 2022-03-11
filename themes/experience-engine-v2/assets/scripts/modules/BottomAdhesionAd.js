import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import Dfp from '../components/content/embeds/Dfp';
import ErrorBoundary from '../components/ErrorBoundary';

const BottomAdhesionAd = () => {
	console.log('FIRED BottomAdhesionAd');
	const [shouldDisplay, setShouldDisplay] = useState(true);

	const [pageURL] = document.location.href;
	// this id is also compared in /assets/scripts/components/content/embeds/Dfp.js
	const id = 'div-bottom-adhesion-slot';

	// const { unitId, unitName } = window.bbgiconfig.dfp.adhesionad;
	const unitId = '/26918149/WAZZ_FM_Leaderboard_pos1';
	const unitName = 'adhesion';
	const container = document.getElementById('bottom-adhesion-container');

	// Exit if shouldDisplay was toggled off
	if (!shouldDisplay) {
		container.remove();
		return null;
	}

	const children = (
		<ErrorBoundary>
			<Dfp
				key={`bottom-adhesion-ad-${pageURL}`}
				placeholder={id}
				unitId={unitId}
				unitName={unitName}
				shouldMapSizes={false}
				pageURL={pageURL}
			/>
			<div className="ad-button-holder">
				<button
					type="button"
					className="button modal-close"
					aria-label="Close Ad"
					onClick={() => setShouldDisplay(false)}
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 212.982 212.982"
						aria-labelledby="close-ad-title close-ad-desc"
						width="10"
						height="10"
					>
						<title id="close-ad-title">Close Ad</title>
						<desc id="close-ad-desc">Checkmark indicating ad close</desc>
						<path
							d="M131.804 106.491l75.936-75.936c6.99-6.99 6.99-18.323 0-25.312-6.99-6.99-18.322-6.99-25.312 0L106.491 81.18 30.554 5.242c-6.99-6.99-18.322-6.99-25.312 0-6.989 6.99-6.989 18.323 0 25.312l75.937 75.936-75.937 75.937c-6.989 6.99-6.989 18.323 0 25.312 6.99 6.99 18.322 6.99 25.312 0l75.937-75.937 75.937 75.937c6.989 6.99 18.322 6.99 25.312 0 6.99-6.99 6.99-18.322 0-25.312l-75.936-75.936z"
							fillRule="evenodd"
							clipRule="evenodd"
						/>
					</svg>
				</button>
			</div>
		</ErrorBoundary>
	);

	return ReactDOM.createPortal(children, container);
};

export default BottomAdhesionAd;
