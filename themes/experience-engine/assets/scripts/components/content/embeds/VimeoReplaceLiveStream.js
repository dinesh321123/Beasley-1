import React, { useEffect, useRef } from 'react';
import PropTypes from 'prop-types';

const VimeoReplaceLiveStream = ({ adjustedSrc }) => {
	console.log('FIRED VimeoReplaceLiveStream');
	VimeoReplaceLiveStream.propTypes = {
		adjustedSrc: PropTypes.string.isRequired,
	};

	const iframeRef = useRef(null);

	useEffect(() => {
		console.log('EFFECT VimeoReplaceLiveStream');
		registerForVimeoPreroll(iframeRef.current);
	});

	console.log(`Adjusted Src: ${adjustedSrc}`);

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
	console.log('Reassigning Livestreram To Vimeo Preroll');
	if (window.configureIframeToPlayVimeoPreroll) {
		window.configureIframeToPlayVimeoPreroll(iFrameEl);
	}
};

export default VimeoReplaceLiveStream;
