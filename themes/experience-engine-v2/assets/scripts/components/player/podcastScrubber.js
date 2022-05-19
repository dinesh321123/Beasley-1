import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { seekPosition } from '../../redux/actions/player';

class PodcastScrubber extends PureComponent {
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

		// Thumb is 10px so adjust prebar width
		const progressPercentage = (100 * time) / duration;
		let prebarwidth = progressPercentage - progressPercentage * (10 / duration);

		// Min prebarwidth is 0
		if (prebarwidth < 0) {
			prebarwidth = 0;
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
					{PodcastScrubber.format(time)}
				</span>
				<div className="ee-range-input -progress">
					<input
						type="range"
						min="0"
						max={duration}
						value={time}
						onChange={this.onSeek}
					/>
					<p className="pre-bar" style={{ width: `${prebarwidth}%` }} />
				</div>
				<span
					className={
						duration && duration > 3599
							? 'time -desktop6digits'
							: 'time -desktop4digits'
					}
				>
					{PodcastScrubber.format(duration)}
				</span>
			</div>
		);
	}
}

PodcastScrubber.propTypes = {
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

export default connect(mapStateToProps, mapDispatchToProps)(PodcastScrubber);
