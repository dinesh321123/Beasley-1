import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dfp from './Dfp';
import CountdownTicker from './CountdownTicker';

const SECOND_IN_MILLISECONDS = 1000;
const MINUTE_IN_MILLISECONDS = SECOND_IN_MILLISECONDS * 60;
const HOUR_IN_MILLISECONDS = MINUTE_IN_MILLISECONDS * 60;
const DAY_IN_MILLISECONDS = HOUR_IN_MILLISECONDS * 24;

class Countdown extends Component {
	constructor(props) {
		super(props);

		this.state = this.getTimeRemaining(false);
		this.interval = null;

		this.getTimeRemaining = this.getTimeRemaining.bind(this);
	}

	componentDidMount() {
		const { payload } = this.props;
		if (payload && payload.countdownTime) {
			this.interval = setInterval(this.getTimeRemaining, 1000);
		}
	}

	componentWillUnmount() {
		this.stopInterval();
	}

	stopInterval() {
		clearInterval(this.interval);
	}

	getTimeRemaining(updateState = true) {
		const total =
			Date.parse(this.props.payload.countdownTime) - Date.parse(new Date());
		if (total <= 0) {
			this.stopInterval();
		}

		const seconds = Math.floor(total / SECOND_IN_MILLISECONDS) % 60;
		const minutes = Math.floor(total / MINUTE_IN_MILLISECONDS) % 60;
		const hours = Math.floor(total / HOUR_IN_MILLISECONDS) % 24;
		const days = Math.floor(total / DAY_IN_MILLISECONDS);

		const remaining = {
			days: days > 0 ? days : 0,
			hours: hours > 0 ? hours : 0,
			minutes: minutes > 0 ? minutes : 0,
			seconds: seconds > 0 ? seconds : 0,
		};

		if (updateState) {
			this.setState(remaining);
		}

		return remaining;
	}

	getSponsor() {
		const { unitName } = window.bbgiconfig.dfp.countdown;
		let unitId = null;

		if (
			this.props &&
			this.props.payload &&
			this.props.payload.sponsorshipAdunit
		) {
			unitId = this.props.payload.sponsorshipAdunit;
		}

		const { placeholder } = this.props;

		const params = {
			id: `${placeholder}-adunit`,
			className: 'countdown-sponsor',
		};

		// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
		return React.createElement('div', params, [
			<Dfp
				key="sponsor"
				placeholder={params.id}
				unitId={unitId}
				unitName={unitName}
			/>,
		]);
	}

	render() {
		const { payload } = this.props;

		if (!payload) {
			return false;
		}

		const { title, background, link, timeColor, timeBackground } = payload;

		const { color, image } = background;
		const blockStyle = {};
		const timeStyle = {};

		if (color) {
			blockStyle.backgroundColor = color;
		}

		if (image) {
			blockStyle.backgroundImage = `url(${image})`;
		}

		if (timeColor) {
			timeStyle.color = timeColor;
		}

		if (timeBackground) {
			timeStyle.background = timeBackground;
		}

		const titleText = link ? <a href={link}>{title}</a> : title;

		const { days, hours, minutes, seconds } = this.state;

		return (
			<div className="countdown" style={blockStyle}>
				<div className="countdown-content">
					<div className="countdown-wrapper">
						<h2 className="countdown-title">{titleText}</h2>

						<div className="countdown-labels" style={timeStyle}>
							<div id="countdown-label-day" className="countdown-labels day">
								Days
							</div>
							<div id="countdown-label-hour" className="countdown-labels hour">
								Hours
							</div>
							<div
								id="countdown-label-minute"
								className="countdown-labels minute"
							>
								Minutes
							</div>
							<div
								id="countdown-label-second"
								className="countdown-labels second"
							>
								Seconds
							</div>
						</div>

						<div className="countdown-timer">
							<div className="time" title="Days">
								<CountdownTicker number={days} timeStyle={timeStyle} />
							</div>
							<div className="time" title="Hours">
								<CountdownTicker number={hours} timeStyle={timeStyle} />
							</div>
							<div className="time" title="Minutes">
								<CountdownTicker number={minutes} timeStyle={timeStyle} />
							</div>
							<div className="time" title="Seconds">
								<CountdownTicker number={seconds} timeStyle={timeStyle} />
							</div>
						</div>
					</div>

					{this.getSponsor()}
				</div>
			</div>
		);
	}
}

Countdown.propTypes = {
	placeholder: PropTypes.string.isRequired,
	payload: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
	timeStyle: PropTypes.shape({}),
};

Countdown.defaultProps = {
	payload: false,
	timeStyle: {},
};

export default Countdown;
