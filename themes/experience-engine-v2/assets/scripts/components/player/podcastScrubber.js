import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { seekPosition } from '../../redux/actions/player';

class Progress extends PureComponent {
	static format(time) {
		const HOUR_IN_SECONDS = 3600;
		const MINUTE_IN_SECONDS = 60;

		const hours = Math.floor(time / HOUR_IN_SECONDS);
		const minutes = Math.floor((time % HOUR_IN_SECONDS) / MINUTE_IN_SECONDS);
		const seconds = Math.floor(time % MINUTE_IN_SECONDS);

		const toFixed = value =>
			value.toString().length === 2 ? value : `0${value}`;
		let result = `${toFixed(minutes)}:${toFixed(seconds)}`;
		if (hours > 0) {
			result = `${toFixed(hours)}:${result}`;
		}

		return result;
	}

	constructor(props) {
		super(props);

		this.onSeek = this.handleSeekPosition.bind(this);
	}

	handleSeekPosition(e) {
		const { target } = e;

		let time = parseFloat(target.value);
		if (Number.isNaN(time)) {
			time = 0;
		}

		this.props.seek(time);
	}

	render() {
		const { time, duration } = this.props;

		if (duration <= 0) {
			return false;
		}

		return (
			<div className="controls-progress">
				<span
					className={
						time && time > 3599
							? 'time -desktop6digits'
							: 'time -desktop4digits'
					}
				>
					{Progress.format(time)}
				</span>
				<div className="ee-range-input -progress">
					<input
						type="range"
						min="0"
						max={duration}
						value={time}
						onChange={this.onSeek}
					/>
					<p
						className="pre-bar"
						style={{ width: `${(100 * time) / duration}%` }}
					/>
				</div>
				<span
					className={
						duration && duration > 3599
							? 'time -desktop6digits'
							: 'time -desktop4digits'
					}
				>
					{Progress.format(duration)}
				</span>
			</div>
		);
	}
}

Progress.propTypes = {
	time: PropTypes.number.isRequired,
	duration: PropTypes.number.isRequired,
	seek: PropTypes.func.isRequired,
};

const mapStateToProps = ({ player }) => ({
	time: player.time,
	duration: player.duration,
});

const mapDispatchToProps = dispatch =>
	bindActionCreators({ seek: seekPosition }, dispatch);

export default connect(mapStateToProps, mapDispatchToProps)(Progress);
