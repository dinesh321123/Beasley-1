import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { playStation } from '../../redux/actions/player';

class Stations extends Component {
	constructor(props) {
		super(props);

		this.state = { isOpen: false };
		this.stationModalRef = React.createRef();

		this.onToggle = this.handleToggleClick.bind(this);
		this.handleEscapeKeyDown = this.handleEscapeKeyDown.bind(this);
		this.handleUserEventOutside = this.handleUserEventOutside.bind(this);
	}

	componentDidMount() {
		document.addEventListener('mousedown', this.handleUserEventOutside, false);
		document.addEventListener('scroll', this.handleUserEventOutside, false);
		document.addEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	componentWillUnmount() {
		document.removeEventListener(
			'mousedown',
			this.handleUserEventOutside,
			false,
		);
		document.removeEventListener('scroll', this.handleUserEventOutside, false);
		document.removeEventListener('keydown', this.handleEscapeKeyDown, false);
	}

	handlePlayClick(station) {
		this.setState({ isOpen: false });
		this.props.play(station);
	}

	handleToggleClick() {
		const { streams } = this.props;

		if (streams.length === 1) {
			this.props.play(streams[0].stream_call_letters);
		} else {
			this.setState(prevState => ({ isOpen: !prevState.isOpen }));
		}
	}

	handleUserEventOutside(e) {
		const { current: ref } = this.stationModalRef;

		if (!ref || !ref.contains(e.target)) {
			this.setState({ isOpen: false });
		}
	}

	handleEscapeKeyDown(e) {
		if (e.keyCode === 27) {
			this.setState({ isOpen: false });
		}
	}

	renderStations(textStyle) {
		const { isOpen } = this.state;
		if (!isOpen) {
			return false;
		}

		const { streams } = this.props;
		const stations = [];

		/* eslint-disable camelcase */
		streams.forEach(({ title, stream_call_letters, picture }) => {
			const { large, original } = picture || {};
			const { url } = large || original || {};

			let logo = false;
			if (url) {
				logo = <img src={url} alt={title} />;
			}

			stations.push(
				<div key={stream_call_letters}>
					<button
						type="button"
						className="control-station-button"
						onClick={this.handlePlayClick.bind(this, stream_call_letters)}
						style={textStyle}
					>
						{logo}
						<span>{title}</span>
					</button>
				</div>,
			);
		});
		/* eslint-enable */

		return stations;
	}

	render() {
		const { stream, colors } = this.props;
		const { isOpen } = this.state;

		const textStyle = {
			color: colors['--brand-text-color'] || colors['--global-theme-secondary'],
		};

		const modalStyle = {
			background: colors['--brand-background-color'],
		};

		let label = 'Listen Live';
		if (stream) {
			label = (
				<span>
					<span className="controls-station-title">Saved Stations</span>
				</span>
			);
		}

		return (
			<div
				ref={this.stationModalRef}
				className={`controls-station ${isOpen ? ' -open' : ''}
				`}
			>
				<button
					className="listenlive-btn"
					onClick={this.onToggle}
					aria-label="Open Stations Selector"
					style={textStyle}
					type="button"
				>
					{label}
				</button>
				<div className="live-player-modal" style={modalStyle}>
					{this.renderStations(textStyle)}
				</div>
			</div>
		);
	}
}

Stations.propTypes = {
	colors: PropTypes.shape({
		'--global-theme-secondary': PropTypes.string,
		'--brand-button-color': PropTypes.string,
		'--brand-background-color': PropTypes.string,
		'--brand-text-color': PropTypes.string,
	}),
	play: PropTypes.func.isRequired,
	stream: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
	streams: PropTypes.arrayOf(PropTypes.object).isRequired,
};

Stations.defaultProps = {
	colors: {},
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
