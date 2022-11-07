import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { isIOS, isAudioAdOnly } from '../library';

import { ControlsV2, Offline, GamPreroll } from '../components/player';

import ErrorBoundary from '../components/ErrorBoundary';

import * as actions from '../redux/actions/player';

class PlayerButton extends Component {
	constructor(props) {
		super(props);

		this.state = { online: window.navigator.onLine };
		this.container = document.getElementById('player-button-div');
		this.onOnline = this.handleOnline.bind(this);
		this.onOffline = this.handleOffline.bind(this);
		this.handlePlay = this.handlePlay.bind(this);
	}

	componentDidMount() {
		window.addEventListener('online', this.onOnline);
		window.addEventListener('offline', this.onOffline);
	}

	componentWillUnmount() {
		window.removeEventListener('online', this.onOnline);
		window.removeEventListener('offline', this.onOffline);
	}

	handleOnline() {
		this.setState({ online: true });
	}

	handleOffline() {
		this.setState({ online: false });
	}

	/**
	 * Handle cliks on the player play button. Those cliks will start the livestreaming
	 * if there isn't anything playing.
	 */
	handlePlay() {
		const { station, playStation } = this.props;
		playStation(station);
	}

	render() {
		if (!this.container) {
			return null;
		}

		const { online } = this.state;

		const {
			status,
			adPlayback,
			gamAdPlayback,
			adSynced,
			pause,
			resume,
			duration,
			player,
			playerType,
			inDropDown,
			customTitle,
		} = this.props;

		let notification = false;
		if (!online) {
			notification = <Offline />;
		}

		const progressClass = !duration ? '-live' : '-podcast';
		let { customColors } = this.container.dataset;
		const controlsStyle = {};
		const buttonsStyle = {};
		const svgStyle = {};
		const textStyle = {};

		customColors = JSON.parse(customColors);
		controlsStyle.backgroundColor = 'transparent';
		buttonsStyle.backgroundColor =
			customColors['--brand-button-color'] ||
			customColors['--global-theme-secondary'];
		buttonsStyle.border = '0';
		svgStyle.fill =
			customColors['--brand-text-color'] ||
			customColors['--global-theme-secondary'];
		/*
		svgStyle.stroke =
			customColors['--brand-text-color'] ||
			customColors['--global-theme-secondary'];
		*/
		textStyle.color =
			customColors['--brand-text-color'] ||
			customColors['--global-theme-secondary'];

		const isIos = isIOS();
		const gamPreroll = gamAdPlayback ? <GamPreroll /> : null;

		if (gamAdPlayback) {
			console.log('Live Player configured to render GAM preroll.');
		}

		const buttonDiv = (
			<div className="controls" style={controlsStyle}>
				<div className={`button-holder ${progressClass}`}>
					<ControlsV2
						status={status}
						play={
							adPlayback && isAudioAdOnly({ player, playerType })
								? null
								: this.handlePlay
						}
						pause={pause}
						resume={resume}
						buttonStyle={buttonsStyle}
						svgStyle={svgStyle}
						isIos={isIos}
						customTitle={customTitle}
					/>
				</div>
			</div>
		);

		const children = (
			<ErrorBoundary>
				{notification}

				<div
					className={`preroll-wrapper${
						adPlayback && !isAudioAdOnly({ player, playerType })
							? ' -active'
							: ''
					}`}
				>
					<div className="preroll-container">
						<div id="td_container" className="preroll-player" />
						<div className="preroll-notification -hidden">
							Live stream will be available after this brief ad from our
							sponsors
						</div>
					</div>
				</div>

				{gamPreroll}

				<div id="sync-banner" className={adSynced ? '' : '-hidden'} />

				{buttonDiv}
			</ErrorBoundary>
		);

		if (inDropDown) {
			return <ErrorBoundary>{buttonDiv}</ErrorBoundary>;
		}
		return ReactDOM.createPortal(children, this.container);
	}
}

PlayerButton.defaultProps = {
	station: '',
	inDropDown: false,
	customTitle: null,
	player: {},
};

PlayerButton.propTypes = {
	station: PropTypes.string,
	inDropDown: PropTypes.bool,
	customTitle: PropTypes.oneOfType([PropTypes.string, PropTypes.object]),
	status: PropTypes.string.isRequired,
	adPlayback: PropTypes.bool.isRequired,
	gamAdPlayback: PropTypes.bool.isRequired,
	adSynced: PropTypes.bool.isRequired,
	playStation: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
	duration: PropTypes.number.isRequired,
	player: PropTypes.shape({}),
	playerType: PropTypes.string.isRequired,
};

export default connect(
	({ player }) => ({
		player: player.player,
		playerType: player.playerType,
		station: player.station,
		status: player.status,
		adPlayback: player.adPlayback,
		gamAdPlayback: player.gamAdPlayback,
		adSynced: player.adSynced,
		duration: player.duration,
	}),
	{
		playStation: actions.playStation,
		pause: actions.pause,
		resume: actions.resume,
	},
)(PlayerButton);
