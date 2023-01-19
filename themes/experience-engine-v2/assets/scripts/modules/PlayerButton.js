import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { isIOS, isAudioAdOnly } from '../library';
import { ControlsV2, GamPreroll, Offline } from '../components/player';
import ErrorBoundary from '../components/ErrorBoundary';
import * as actions from '../redux/actions/player';
import { STATUSES } from '../redux/actions/player';

class PlayerButton extends Component {
	constructor(props) {
		super(props);

		this.gamPrerollRef = React.createRef();

		this.state = { online: window.navigator.onLine, forceSpinner: false };
		this.container = document.getElementById('player-button-div');
		this.onOnline = this.handleOnline.bind(this);
		this.onOffline = this.handleOffline.bind(this);
		this.handlePlay = this.handlePlay.bind(this);
		this.turnOffForcedSpinner = this.turnOffForcedSpinner.bind(this);
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
		this.setState({ forceSpinner: true });
	}

	turnOffForcedSpinner() {
		this.setState({ forceSpinner: false });
	}

	componentDidUpdate(prevProps, prevState, snapshot) {
		const { gamAdPlayback, gamAdPlaybackStop, status } = this.props;
		console.log(
			`Player Button Updated: Current gamAdPlayback: ${
				gamAdPlayback ? 'true' : 'false'
			}, Previous gamAdPlayback: ${
				prevProps.gamAdPlayback ? 'true' : 'false'
			}, gamAdPlaybackStop: ${
				gamAdPlaybackStop ? 'true' : 'false'
			},  ${status}`,
		);
		if (this.state.forceSpinner && status === STATUSES.LIVE_CONNECTING) {
			this.turnOffForcedSpinner();
		} else if (gamAdPlayback && this.gamPrerollRef.current) {
			this.gamPrerollRef.current.doPreroll();
		} else if (gamAdPlaybackStop && this.state.forceSpinner) {
			console.log('Player Button Triggering GamPreroll Finalize');
			this.turnOffForcedSpinner();
		}
	}

	render() {
		if (!this.container) {
			return null;
		}

		const { online, forceSpinner } = this.state;

		const {
			status,
			adPlayback,
			adSynced,
			pause,
			resume,
			duration,
			player,
			playerType,
			inDropDown,
			customTitle,
			adPlaybackStop,
		} = this.props;

		const renderStatus = forceSpinner ? STATUSES.LIVE_CONNECTING : status;

		const notification = online ? <></> : <Offline />;

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
		let gamPreroll = <></>;
		if (forceSpinner) {
			gamPreroll = (
				<GamPreroll ref={this.gamPrerollRef} adPlaybackStop={adPlaybackStop} />
			);
		}

		const buttonDiv = (
			<div className="controls" style={controlsStyle}>
				<div className={`button-holder ${progressClass}`}>
					<ControlsV2
						status={renderStatus}
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
	gamAdPlaybackStop: PropTypes.bool.isRequired,
	adSynced: PropTypes.bool.isRequired,
	playStation: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
	duration: PropTypes.number.isRequired,
	player: PropTypes.shape({}),
	playerType: PropTypes.string.isRequired,
	adPlaybackStop: PropTypes.func.isRequired,
};

export default connect(
	({ player, screen }) => ({
		player: player.player,
		playerType: player.playerType,
		station: player.station,
		status: player.status,
		adPlayback: player.adPlayback,
		gamAdPlayback: player.gamAdPlayback,
		gamAdPlaybackStop: player.gamAdPlaybackStop,
		adSynced: player.adSynced,
		duration: player.duration,
	}),
	{
		playStation: actions.playStation,
		pause: actions.pause,
		resume: actions.resume,
		adPlaybackStop: actions.adPlaybackStop,
	},
)(PlayerButton);
