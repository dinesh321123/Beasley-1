/**
 * @file This is the Stations React component.
 * It handles the rendering of other available stations for the user.
 * Additionally, it filters and handles the click functionality to play the selected station.
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import dayjs from 'dayjs';
import { playStation } from '../../redux/actions/player';

const utc = require('dayjs/plugin/utc');
const timezone = require('dayjs/plugin/timezone');

const config = window.bbgiconfig;

class Stations extends Component {
	constructor(props) {
		super(props);
		this.stationModalRef = React.createRef();
	}

	/**
	 * Handles the play button click, dispatches the play action with given station.
	 * @param {Object} station The station to play.
	 */
	handlePlayClick(station) {
		this.props.play(station);
	}

	/**
	 * Determines whether the stations should be rendered given their stream and streams.
	 * @param {Object} stream The current stream.
	 * @param {Array} streams The list of available streams.
	 * @return {boolean} True if stations should be rendered, false otherwise.
	 */
	shouldRender(stream, streams) {
		if (!stream) {
			return true;
		}

		if (streams && streams.length > 1) {
			return true;
		}

		return false;
	}

	/**
	 * Checks and returns the timezone from the config.
	 * @return {string|boolean} The timezone if it exists, false otherwise.
	 */
	checkTimeZone() {
		const tz = config.timezone_string;
		if (!Intl || !Intl.DateTimeFormat().resolvedOptions().timeZone) {
			return tz;
		}

		try {
			Intl.DateTimeFormat(undefined, { timeZone: tz });
			return tz;
		} catch (ex) {
			return false;
		}
	}

	/**
	 * Renders the list of stations, filtering by the time if their availability is conditional.
	 * @param {Object} stream The current stream.
	 * @param {Array} streams The list of available streams.
	 * @return {Array} An array of rendered station buttons.
	 */
	renderStations(stream, streams) {
		const stations = [];
		const days = [
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
		];
		dayjs.extend(utc);
		dayjs.extend(timezone);
		const timezoneString = this.checkTimeZone();
		if (timezoneString) {
			dayjs.tz.setDefault(timezoneString);
		}

		const dayName = days[dayjs.tz().day()].toLowerCase();
		const dayTime = dayjs.tz();

		// this code block is to handle the case where there are multiple streams
		/* eslint-disable camelcase */
		const secondaryStreams = streams.filter(s => s !== stream);
		for (let i = 0; i < secondaryStreams.length; i++) {
			const {
				title, // title of the stream
				stream_call_letters, // call letters of the stream
				picture, // logo of the stream
				secondStreamTime, // availability of the stream
			} = secondaryStreams[i]; // stream object
			const { large, original } = picture || {};
			const { url } = large || original || {};

			let logo = false;
			if (url) {
				logo = <img src={url} alt={title} />;
			}

			// if the stream is conditional, check if it is available
			if (secondStreamTime) {
				let { startTime, endTime } = '';

				// if the stream is not available on the current day, skip it
				if (!(dayName in secondStreamTime)) {
					continue;
				}

				if (secondStreamTime[dayName].startTime) {
					const temp = secondStreamTime[dayName].startTime.split(':');
					startTime = dayjs
						.tz()
						.set('hour', temp[0])
						.set('minute', temp[1]);
				}

				if (secondStreamTime[dayName].endTime) {
					const temp = secondStreamTime[dayName].endTime.split(':');
					endTime = dayjs
						.tz()
						.set('hour', temp[0])
						.set('minute', temp[1]);
				}
				// if the stream is not available at the current time, skip it
				if (dayTime < startTime || dayTime > endTime) {
					continue;
				}
			}

			stations.push(
				<div key={stream_call_letters}>
					<button
						type="button"
						className="control-station-button"
						onClick={this.handlePlayClick.bind(this, stream_call_letters)}
					>
						{logo}
						<span>{title}</span>
					</button>
				</div>,
			);
		}
		/* eslint-enable */

		return stations;
	}

	render() {
		const { stream, streams } = this.props;

		if (!this.shouldRender(stream, streams)) {
			return false;
		}

		return this.renderStations(stream, streams);
	}
}

Stations.propTypes = {
	play: PropTypes.func.isRequired,
	stream: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
	streams: PropTypes.arrayOf(PropTypes.object).isRequired,
};

Stations.defaultProps = {
	stream: false,
};

function mapStateToProps({ player }) {
	const { streams, station } = player;

	return {
		stream: streams.find(item => item.stream_call_letters === station),
		streams,
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators({ play: playStation }, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(Stations);
