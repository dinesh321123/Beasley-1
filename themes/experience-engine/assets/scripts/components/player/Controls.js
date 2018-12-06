import React from 'react';
import PropTypes from 'prop-types';

function Controls( { status, play, pause, resume, online } ) {
	return (
		<div className={`live-player-status status ${status}`}>
			<button type="button" className={ `play-btn ${ 'btn-offline' }` } onClick={play} aria-label="Play">
				{ ! online ? (
					<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg">
						<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z" />
					</svg>
				) : (
					<svg className="offline-btn" width="6" height="31" viewBox="0 0 6 31" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="0.359375" y="0.160156" width="5.28" height="20.7138" fill="white"/>
						<rect x="0.359375" y="25.2402" width="5.28" height="5.28" fill="white"/>
					</svg>
				) }
				
			</button>

			<button type="button" className="pause-btn" onClick={pause}  aria-label="Pause">
				<svg viewBox="0 0 28 24" xmlns="http://www.w3.org/2000/svg" fillRule="evenodd" clipRule="evenodd">
					<path d="M11 22h-4v-20h4v20zm6-20h-4v20h4v-20z" />
				</svg>
			</button>

			<button type="button" className="resume-btn" onClick={resume} aria-label="Resume">
				<svg viewBox="0 0 17 24" xmlns="http://www.w3.org/2000/svg">
					<path d="M16.1836 12.0055L0.910156 23.124L0.910156 0.887031L16.1836 12.0055Z" />
				</svg>
			</button>

			<button type="button" className="loading-btn" aria-label="Loading">
				<div className="loading" />
			</button>
		</div>
	);
}

Controls.propTypes = {
	status: PropTypes.string.isRequired,
	play: PropTypes.func,
	pause: PropTypes.func,
	resume: PropTypes.func,
	online: PropTypes.bool
};

Controls.defaultProps = {
	play: () => { },
	pause: () => { },
	resume: () => { },
};

export default Controls;
