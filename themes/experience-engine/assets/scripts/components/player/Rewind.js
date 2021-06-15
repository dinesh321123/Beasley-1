import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import { seekPosition } from '../../redux/actions/player';
import Progress from './Progress';

class Rewind extends PureComponent {
	/**
	 * Bind Functions
	 */
	constructor() {
		super();

		this.handleOnClick = this.handleOnClick.bind(this);
	}

	/**
	 * Rewinds time 15 seconds on click
	 * If we're less than 15 seconds in
	 * Go back to 0
	 */
	handleOnClick() {
		const { currentTime, seek } = this.props;
		let newTime = currentTime - 15;

		if (newTime < 0) {
			newTime = 0;
		}

		seek(newTime);
	}

	/**
	 * Display rewind icon
	 * @returns {*}
	 */
	render() {
		const { currentTime, duration, progressClass } = this.props;

		return (
			<div className={`controls-rewind ${progressClass}`}>
				<p>
					<span className="time -mobile -current">
						{Progress.format(currentTime)}
					</span>
					<span className="time -mobile -total">
						{Progress.format(duration)}
					</span>
				</p>

				<button
					className="rewind"
					aria-label="Rewind fifteen seconds"
					onClick={this.handleOnClick}
					type="button"
				>
					<svg
						width="32"
						height="18"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
					>
						<path
							d="M22.516 5.725l-1.711.491-.247-.766 2.216-.706h.752v8.443h-1.01V5.725zM25.52 12l.674-.682c.79.706 1.658 1.15 2.58 1.15 1.295 0 2.19-.791 2.19-1.857v-.024c0-1.042-.934-1.76-2.255-1.76-.764 0-1.373.215-1.905.478l-.687-.419.26-4.083h5.221v.874H27.27l-.194 2.682c.532-.216 1.037-.371 1.789-.371 1.762 0 3.135.97 3.135 2.564v.023c0 1.641-1.347 2.754-3.24 2.754-1.296 0-2.424-.575-3.24-1.329zM8.988 17.228a8.233 8.233 0 0 1-5.539-2.142h-.017v-.026a.337.337 0 0 1-.106-.246c0-.105.027-.193.088-.255l.018-.017 1.606-1.606a.366.366 0 0 1 .5 0 5.31 5.31 0 0 0 3.449 1.29 5.248 5.248 0 0 0 5.24-5.24 5.248 5.248 0 0 0-5.24-5.24 5.129 5.129 0 0 0-3.431 1.29L7.758 7.22c.106.096.123.263.088.412a.382.382 0 0 1-.36.229H.379A.373.373 0 0 1 0 7.485V.376a.382.382 0 0 1 .228-.36C.378-.018.544 0 .641.105l2.79 2.809A8.174 8.174 0 0 1 8.989.745c4.537 0 8.24 3.703 8.24 8.24 0 4.538-3.703 8.243-8.24 8.243z"
							fill="currentColor"
						/>
					</svg>
				</button>
			</div>
		);
	}
}

Rewind.propTypes = {
	currentTime: PropTypes.number.isRequired,
	duration: PropTypes.number.isRequired,
	seek: PropTypes.func.isRequired,
	progressClass: PropTypes.string,
};

Rewind.defaultProps = {
	progressClass: '',
};

const mapStateToProps = ({ player }) => ({
	currentTime: player.time,
	duration: player.duration,
});

const mapDispatchToProps = dispatch =>
	bindActionCreators({ seek: seekPosition }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Rewind);
