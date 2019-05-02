import React, { Fragment, Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import Stations from '../components/player/Stations';
import Controls from '../components/player/Controls';
import Info from '../components/player/Info';
import Volume from '../components/player/Volume';

import Progress from '../components/player/Progress';
import RecentSongs from '../components/player/RecentSongs';
import Offline from '../components/player/Offline';
import Contacts from '../components/player/Contacts';
import Sponsor from '../components/player/Sponsor';

import ErrorBoundary from '../components/ErrorBoundary';

import * as actions from '../redux/actions/player';

class LivePlayer extends Component {

	constructor( props ) {
		super( props );

		const self = this;

		self.container = document.getElementById( 'live-player' );
		self.state = { online: window.navigator.onLine };

		self.onOnline = self.handleOnline.bind( self );
		self.onOffline = self.handleOffline.bind( self );
	}

	componentDidMount() {
		const self = this;

		// @see: https://userguides.tritondigital.com/spc/tdplay2/
		const tdmodules = [];

		tdmodules.push( {
			id: 'MediaPlayer',
			playerId: 'td_container',
			techPriority: ['Html5'],
			geoTargeting: {
				desktop: { isActive: false },
				iOS: { isActive: false },
				android: { isActive: false },
			},
		} );

		tdmodules.push( {
			id: 'NowPlayingApi',
		} );

		tdmodules.push( {
			id: 'TargetSpot',
		} );

		tdmodules.push( {
			id: 'SyncBanners',
			elements: [{ id: 'sync-banner', width: 320, height: 50 }],
		} );

		// TDSdk is loaded asynchronously, so we need to wait till its loaded and
		// parsed by browser, and only then start initializing the player
		const tdinterval = setInterval( () => {
			if ( window.TDSdk ) {
				this.props.initPlayer( tdmodules );
				clearInterval( tdinterval );
			}
		}, 500 );


		window.addEventListener( 'online',  self.onOnline );
		window.addEventListener( 'offline', self.onOffline );
	}

	componentWillUnmount() {
		const self = this;
		window.removeEventListener( 'online',  self.onOnline );
		window.removeEventListener( 'offline', self.onOffline );
	}

	handleOnline() {
		this.setState( { online: true } );
	}

	handleOffline() {
		this.setState( { online: false } );
	}

	render() {
		const self = this;
		const { container, state, props } = self;
		if ( !container ) {
			return false;
		}

		const { online } = state;
		const {
			station,
			status,
			adPlayback,
			adSynced,
			play,
			pause,
			resume,
		} = props;

		let notification = false;
		if ( ! online ) {
			notification = <Offline />;
		}

		let { customColors } = container.dataset;
		let controlsStyle = {};
		let buttonStyle = {};

		customColors = JSON.parse( customColors );
		controlsStyle.backgroundColor = customColors['--brand-background-color'] || customColors['--global-theme-secondary'];
		buttonStyle.backgroundColor = customColors['--brand-button-color'] || customColors['--global-theme-secondary'];

		console.log( customColors );

		const children = (
			<Fragment>
				{notification}

				<div className={`preroll-wrapper${adPlayback ? ' -active' : ''}`}>
					<div className="preroll-container">
						<div id="td_container" className="preroll-player"></div>
						<div className="preroll-notification">Live stream will be available after this brief ad from our sponsors</div>
					</div>
				</div>

				<div id="sync-banner" className={adSynced ? '' : '-hidden'} />

				<ErrorBoundary>
					<Progress />
				</ErrorBoundary>

				<div className="controls" style={ controlsStyle }>
					<div className="control-section">
						<ErrorBoundary>
							<Info />
						</ErrorBoundary>
					</div>
					<div className="control-section -centered">
						<ErrorBoundary>
							<RecentSongs />
						</ErrorBoundary>
						<ErrorBoundary>
							<Controls status={status} play={() => play( station )} pause={pause} resume={resume} colors={buttonStyle} />
						</ErrorBoundary>
						<ErrorBoundary>
							<Volume />
						</ErrorBoundary>
					</div>
					<div className="control-section">
						<ErrorBoundary>
							<Sponsor />
						</ErrorBoundary>
						<ErrorBoundary>
							<Stations />
						</ErrorBoundary>
						<ErrorBoundary>
							<Contacts />
						</ErrorBoundary>
					</div>
				</div>
			</Fragment>
		);

		return ReactDOM.createPortal( children, container );
	}

}

LivePlayer.propTypes = {
	station: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	adPlayback: PropTypes.bool.isRequired,
	adSynced: PropTypes.bool.isRequired,
	initPlayer: PropTypes.func.isRequired,
	play: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
};

function mapStateToProps( { player } ) {
	return {
		station: player.station,
		status: player.status,
		adPlayback: player.adPlayback,
		adSynced: player.adSynced,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		initPlayer: actions.initTdPlayer,
		play: actions.playStation,
		pause: actions.pause,
		resume: actions.resume,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( LivePlayer );
