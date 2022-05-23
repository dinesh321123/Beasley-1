import React from 'react';
import PropTypes from 'prop-types';

function ControlsV2({
	status,
	title,
	play,
	pause,
	resume,
	buttonStyle,
	svgStyle,
	isIos,
	customTitle,
}) {
	// TODO - IOS Special Style was removed from controls.css. Remove osClass once it is determined that we will never need OS Specific logic again.
	const osClass = isIos ? '-is-ios' : '';
	return (
		<div className={`status ${status} ${osClass}`}>
			<button
				type="button"
				className="play-btn"
				onClick={play}
				aria-label={`Play ${title}`}
				style={buttonStyle}
			>
				<svg
					style={svgStyle}
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						d="M10,0C4.5,0,0,4.5,0,10c0,5.5,4.5,10,10,10c5.5,0,10-4.5,10-10C20,4.5,15.5,0,10,0z M14,11.3l-6.2,3.6
	c-0.8,0.5-1.5,0.1-1.5-0.9V6.9c0-1,0.7-1.4,1.5-0.9L14,9.5C14.9,10,14.9,10.8,14,11.3z"
					/>
				</svg>
				{customTitle ? <strong>{customTitle}</strong> : null}
			</button>

			<button
				type="button"
				className="pause-btn"
				onClick={pause}
				aria-label="Pause"
				style={buttonStyle}
			>
				<svg
					style={svgStyle}
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						d="M10,0C4.5,0,0,4.5,0,10c0,5.5,4.5,10,10,10c5.5,0,10-4.5,10-10C20,4.5,15.5,0,10,0z M8.4,15.2H6.5c-0.2,0-0.3-0.1-0.3-0.3
	V6.1c0-0.2,0.1-0.3,0.3-0.3h1.9c0.2,0,0.3,0.1,0.3,0.3v8.8C8.7,15,8.5,15.2,8.4,15.2z
	 					M13.5,15.1h-1.9c-0.2,0-0.3-0.1-0.3-0.3V6
	c0-0.2,0.1-0.3,0.3-0.3h1.9c0.2,0,0.3,0.1,0.3,0.3v8.8C13.8,15,13.7,15.1,13.5,15.1z"
					/>
				</svg>
				{customTitle}
			</button>

			<button
				type="button"
				className="resume-btn"
				onClick={resume}
				aria-label="Resume"
				style={buttonStyle}
			>
				<svg
					style={svgStyle}
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						d="M10,0C4.5,0,0,4.5,0,10c0,5.5,4.5,10,10,10c5.5,0,10-4.5,10-10C20,4.5,15.5,0,10,0z M14,11.3l-6.2,3.6
	c-0.8,0.5-1.5,0.1-1.5-0.9V6.9c0-1,0.7-1.4,1.5-0.9L14,9.5C14.9,10,14.9,10.8,14,11.3z"
					/>
				</svg>
				{customTitle}
			</button>

			<button
				type="button"
				className="loading-btn"
				aria-label="Loading"
				style={buttonStyle}
			>
				<div className="loading" />
			</button>
		</div>
	);
}

ControlsV2.propTypes = {
	status: PropTypes.string.isRequired,
	title: PropTypes.string,
	play: PropTypes.func,
	pause: PropTypes.func,
	resume: PropTypes.func,
	buttonStyle: PropTypes.shape({}),
	svgStyle: PropTypes.shape({}),
	isIos: PropTypes.bool,
	customTitle: PropTypes.oneOfType([PropTypes.string, PropTypes.object]),
};

ControlsV2.defaultProps = {
	title: '',
	play: () => {},
	pause: () => {},
	resume: () => {},
	buttonStyle: {},
	svgStyle: {},
	isIos: false,
	customTitle: null,
};

export default ControlsV2;
