import React from 'react';

const Offline = () => (
	<div className="offline">
		<span className="offline-title">No internet connection detected</span>
		<span className="offline-description">Audio will automatically try to reconnect when it detects an internet connection.</span>
	</div>
);

export default Offline;
