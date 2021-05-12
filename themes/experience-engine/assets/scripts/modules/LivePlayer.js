import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import { isIOS, isAudioAdOnly } from '../library';

import {
	Controls,
	Info,
	Volume,
	Rewind,
	Progress,
	RecentSongs,
	Offline,
	Sponsor,
	PlayerAd,
} from '../components/player';

import ErrorBoundary from '../components/ErrorBoundary';

import * as actions from '../redux/actions/player';
// import mapStateToProps from "react-redux/lib/connect/mapStateToProps";
// import {durationChange, setPlayer, STATUSES, statusUpdate, timeChange} from '../redux/actions/player';

class LivePlayer extends Component {
	constructor(props) {
		super(props);

		this.state = { online: window.navigator.onLine };
		this.container = document.getElementById('live-player');
		this.onOnline = this.handleOnline.bind(this);
		this.onOffline = this.handleOffline.bind(this);
		this.handlePlay = this.handlePlay.bind(this);
	}

	componentDidMount() {
		// TDSdk is loaded asynchronously, so we need to wait till its loaded and
		// parsed by browser, and only then start initializing the player
		const tdinterval = setInterval(() => {
			if (window.TDSdk) {
				this.setUpPlayer();
				clearInterval(tdinterval);
			}
		}, 500);

		window.addEventListener('online', this.onOnline);
		window.addEventListener('offline', this.onOffline);
	}

	componentWillUnmount() {
		window.removeEventListener('online', this.onOnline);
		window.removeEventListener('offline', this.onOffline);
	}

	/**
	 * Sets up the TdPlayer
	 */
	setUpPlayer() {
		const { initTdPlayer } = this.props;

		// @see: https://userguides.tritondigital.com/spc/tdplay2/
		const tdmodules = [];

		tdmodules.push({
			id: 'MediaPlayer',
			playerId: 'td_container',
			techPriority: ['Html5'],
			idSync: {
				station: this.props.station,
			},
			geoTargeting: {
				desktop: { isActive: false },
				iOS: { isActive: false },
				android: { isActive: false },
			},
		});

		tdmodules.push({
			id: 'NowPlayingApi',
		});

		tdmodules.push({
			id: 'TargetSpot',
		});

		tdmodules.push({
			id: 'SyncBanners',
			elements: [{ id: 'sync-banner', width: 320, height: 50 }],
		});

		initTdPlayer(tdmodules);
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
			adSynced,
			pause,
			resume,
			duration,
			player,
			playerType,
		} = this.props;

		let notification = false;
		if (!online) {
			notification = <Offline />;
		}

		const progressClass = !duration ? '-live' : '-podcast';
		let { customColors } = this.container.dataset;
		const controlsStyle = {};
		const buttonsBackgroundStyle = {};
		const buttonsFillStyle = {};
		const textStyle = {};

		customColors = JSON.parse(customColors);
		controlsStyle.backgroundColor =
			customColors['--brand-background-color'] ||
			customColors['--global-theme-secondary'];
		buttonsBackgroundStyle.backgroundColor =
			customColors['--brand-button-color'] ||
			customColors['--global-theme-secondary'];
		buttonsFillStyle.fill =
			customColors['--brand-button-color'] ||
			customColors['--global-theme-secondary'];
		buttonsFillStyle.stroke =
			customColors['--brand-button-color'] ||
			customColors['--global-theme-secondary'];
		textStyle.color =
			customColors['--brand-text-color'] ||
			customColors['--global-theme-secondary'];

		const isIos = isIOS();

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

				<div id="sync-banner" className={adSynced ? '' : '-hidden'} />

				<Progress className="-mobile" colors={textStyle} />

				<div className="controls" style={controlsStyle}>
					<div className={`button-holder ${progressClass}`}>
						<Controls
							status={status}
							play={
								adPlayback && isAudioAdOnly({ player, playerType })
									? null
									: this.handlePlay
							}
							pause={pause}
							resume={resume}
							customColors={customColors}
							colors={buttonsBackgroundStyle}
							isIos={isIos}
							progressClass={progressClass}
						/>
					</div>
					<Rewind progressClass={progressClass} />
					<div className="control-section">
						<Info colors={textStyle} />
					</div>
					<div className="button-holder full-width">
						<div>
							<RecentSongs
								className={` ${progressClass} `}
								colors={customColors}
							/>
							<Progress
								className={` -desktop ${progressClass} `}
								colors={textStyle}
							/>
							<Volume colors={buttonsFillStyle} />
						</div>
					</div>
					<PlayerAd
						className="player-ad"
						minWidth="1400"
						style={controlsStyle}
					/>
				</div>
				<Sponsor
					className="sponsor-mobile"
					maxWidth="1059"
					style={controlsStyle}
				/>
			</ErrorBoundary>
		);

		return ReactDOM.createPortal(children, this.container);
	}
}

LivePlayer.propTypes = {
	station: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	adPlayback: PropTypes.bool.isRequired,
	adSynced: PropTypes.bool.isRequired,
	initTdPlayer: PropTypes.func.isRequired,
	playStation: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
	duration: PropTypes.number.isRequired,
	player: PropTypes.shape({}).isRequired,
	playerType: PropTypes.string.isRequired,
};

export default connect(
	({ player }) => ({
		player: player.player,
		playerType: player.playerType,
		station: player.station,
		status: player.status,
		adPlayback: player.adPlayback,
		adSynced: player.adSynced,
		duration: player.duration,
	}),
	{
		initTdPlayer: actions.initTdPlayer,
		playStation: actions.playStation,
		pause: actions.pause,
		resume: actions.resume,
	},
)(LivePlayer);
