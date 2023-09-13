import React from 'react';

const OutbrainWidget = () => {
	return (
		<div>
			<div
				className="OUTBRAIN"
				data-src="DROP_PERMALINK_HERE"
				data-widget-id="AR_1"
			/>
			<script
				type="text/javascript"
				async="async"
				src="//widgets.outbrain.com/outbrain.js"
			/>
		</div>
	);
};

export default OutbrainWidget;
