import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

const VimeoReplaceLiveStream = ({ adjustedSrc }) => {
	VimeoReplaceLiveStream.propTypes = {
		adjustedSrc: PropTypes.string.isRequired,
	};

	const iframeRef = useRef(null);

	useEffect(() => {
		registerForVimeoPreroll(iframeRef.current);
	});

	return (
		<div className="lazy-video">
			<iframe
				ref={iframeRef}
				src={adjustedSrc}
				title="Watch Video"
				frameBorder="0"
				scrolling="no"
				allowFullScreen
			/>
		</div>
	);
};

const registerForVimeoPreroll = iFrameEl => {
	if (window.configureIframeToPlayVimeoPreroll) {
		window.configureIframeToPlayVimeoPreroll(iFrameEl);
	}
};

export default VimeoReplaceLiveStream;
