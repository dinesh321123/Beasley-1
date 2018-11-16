import React from 'react';
import PropTypes from 'prop-types';

const Controls = ( { status, play, pause, resume } ) => (
	<div className={`status ${status}`}>
		<button type="button" className="play-btn" onClick={play}>
			<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg">
				<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z" />
			</svg>
		</button>

		<button type="button" className="pause-btn" onClick={pause}>
			<svg viewBox="0 0 28 24" xmlns="http://www.w3.org/2000/svg" fillRule="evenodd" clipRule="evenodd">
				<path d="M11 22h-4v-20h4v20zm6-20h-4v20h4v-20z" />
			</svg>
		</button>

		<button type="button" className="resume-btn" onClick={resume}>
			<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg">
				<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z" />
			</svg>
		</button>

		<button type="button" className="loading-btn">
			<div className="loading" />
		</button>
	</div>
);

Controls.propTypes = {
	status: PropTypes.string.isRequired,
	play: PropTypes.func,
	pause: PropTypes.func,
	resume: PropTypes.func,
};

Controls.defaultProps = {
	play: () => { },
	pause: () => { },
	resume: () => { },
};

export default Controls;
