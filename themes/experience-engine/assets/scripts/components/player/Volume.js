import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as actions from '../../redux/actions/player';

class Volume extends PureComponent {

	constructor() {
		super();

		const self = this;
		self.state = { showVolume: false };
		self.volumeRef = React.createRef();

		self.toggleVolumeSlider = self.toggleVolumeSlider.bind( self );
		self.handleUserEventOutside = self.handleUserEventOutside.bind( self );
		self.handleEscapeKeyDown = self.handleEscapeKeyDown.bind( self );
	}

	componentDidMount() {
		document.addEventListener( 'mousedown', this.handleUserEventOutside, false );
		document.addEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	componentWillUnmount() {
		document.removeEventListener( 'mousedown', this.handleUserEventOutside, false );
		document.removeEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}
	
	toggleVolumeSlider() {
		this.setState( prevState => ( { showVolume: !prevState.showVolume } ) );
	}

	handleUserEventOutside( e ) {
		const self = this;
		const { current: ref } = self.volumeRef;

		if ( !ref || !ref.contains( e.target ) ) {
			self.setState( { showVolume: false } );
		}
	}

	handleEscapeKeyDown( e ) {
		if ( 27 === e.keyCode ) {
			this.setState( { showVolume: false } );
		}
	}

	render() {
		const self = this;
		const { audio, setVolume, colors } = self.props;
		const { showVolume } = self.state;
		let { volume } = self.props;

		if ( audio && audio.length && 0 === audio.indexOf( 'https://omny.fm/' ) ) {
			return false;
		}

		// Max volume is 82
		if ( 82 < volume ) {
			volume = 82;
		}

		return (
			<div ref={self.volumeRef} className="controls-volume">
				<button onClick={self.toggleVolumeSlider} aria-label="Toggle volume controls">
					<svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.79511 4.99539L9.99617 0.595406C10.2092 0.470276 10.4573 0.468062 10.6725 0.589772L10.9187 0.154558L10.6725 0.589774C10.8976 0.717097 11.0322 0.945565 11.0322 1.21398V16.4703C11.0322 16.7387 10.8977 16.9669 10.673 17.0938C10.5648 17.1549 10.4507 17.1836 10.3382 17.1838C10.2239 17.1837 10.107 17.1534 9.99787 17.0895L4.80119 12.316L4.65764 12.1842L4.46272 12.1843L0.5 12.1861V5.11367H4.47218H4.6553L4.79511 4.99539ZM10.0075 17.0983L10.0072 17.098L10.0075 17.0983Z" fill="#707070" style={colors} />
						<path d="M4.79511 4.99539L9.99617 0.595406C10.2092 0.470276 10.4573 0.468062 10.6725 0.589772L10.9187 0.154558L10.6725 0.589774C10.8976 0.717097 11.0322 0.945565 11.0322 1.21398V16.4703C11.0322 16.7387 10.8977 16.9669 10.673 17.0938C10.5648 17.1549 10.4507 17.1836 10.3382 17.1838C10.2239 17.1837 10.107 17.1534 9.99787 17.0895L4.80119 12.316L4.65764 12.1842L4.46272 12.1843L0.5 12.1861V5.11367H4.47218H4.6553L4.79511 4.99539ZM10.0075 17.0983L10.0072 17.098L10.0075 17.0983Z" stroke="#707070" style={colors} />
						<path d="M15.9164 1.55849C15.7154 1.49045 15.497 1.59809 15.4286 1.79913C15.3606 1.99979 15.4682 2.21852 15.6692 2.28694C18.3989 3.21374 20.2329 5.77081 20.2329 8.65001C20.2329 11.5265 18.4024 14.084 15.6781 15.0135C15.4774 15.0819 15.3698 15.3002 15.4386 15.5017C15.4932 15.6612 15.6423 15.7619 15.8023 15.7619C15.8434 15.7619 15.8853 15.7554 15.9264 15.7411C18.9617 14.7059 21.0013 11.8563 21.0013 8.65039C21.0017 5.44099 18.9582 2.59139 15.9164 1.55849Z" fill="#707070" style={colors} />
						<path d="M15.9164 1.55849C15.7154 1.49045 15.497 1.59809 15.4286 1.79913C15.3606 1.99979 15.4682 2.21852 15.6692 2.28694C18.3989 3.21374 20.2329 5.77081 20.2329 8.65001C20.2329 11.5265 18.4024 14.084 15.6781 15.0135C15.4774 15.0819 15.3698 15.3002 15.4386 15.5017C15.4932 15.6612 15.6423 15.7619 15.8023 15.7619C15.8434 15.7619 15.8853 15.7554 15.9264 15.7411C18.9617 14.7059 21.0013 11.8563 21.0013 8.65039C21.0017 5.44099 18.9582 2.59139 15.9164 1.55849Z" stroke="#707070" strokeWidth="0.25" style={colors} />
						<path d="M17.5414 8.64992C17.5414 6.35424 16.0003 4.30536 13.7938 3.66725C13.5889 3.6092 13.3771 3.72568 13.3179 3.93018C13.2591 4.13392 13.3763 4.34726 13.5808 4.40608C15.4602 4.94924 16.773 6.69444 16.773 8.64992C16.773 10.6054 15.4602 12.3506 13.5808 12.8938C13.3767 12.9526 13.2591 13.1659 13.3179 13.3696C13.3667 13.538 13.5201 13.6476 13.6873 13.6476C13.7227 13.6476 13.7584 13.6426 13.7938 13.6326C16.0007 12.9945 17.5414 10.9456 17.5414 8.64992Z" fill="#707070" style={colors} />
						<path d="M17.5414 8.64992C17.5414 6.35424 16.0003 4.30536 13.7938 3.66725C13.5889 3.6092 13.3771 3.72568 13.3179 3.93018C13.2591 4.13392 13.3763 4.34726 13.5808 4.40608C15.4602 4.94924 16.773 6.69444 16.773 8.64992C16.773 10.6054 15.4602 12.3506 13.5808 12.8938C13.3767 12.9526 13.2591 13.1659 13.3179 13.3696C13.3667 13.538 13.5201 13.6476 13.6873 13.6476C13.7227 13.6476 13.7584 13.6426 13.7938 13.6326C16.0007 12.9945 17.5414 10.9456 17.5414 8.64992Z" stroke="#707070" strokeWidth="0.25" style={colors} />
					</svg>
				</button>
				<div className="ee-range-input">
					<div className={`ee-range-input-slider ${showVolume ? '-volume-visible' : ''}`} aria-hidden={ !showVolume }>
						<label className="screen-reader-text" htmlFor="audio-volume">Volume:</label>
						<div className="new-test-range-input">
							<label className="screen-reader-text" htmlFor="audio-volume">Volume:</label>
							<input type="range" min="0" max="82" step="1" value={volume} onChange={( e ) => setVolume( e.target.value )} />
						</div>
						<p className="pre-bar" style={{ width: `${volume}px`}} />
					</div>
				</div>
			</div>
		);
	}
}

Volume.propTypes = {
	volume: PropTypes.number.isRequired,
	audio: PropTypes.string,
	setVolume: PropTypes.func.isRequired,
};

Volume.defaultProps = {
	audio: '',
};

const mapStateToProps = ( { player } ) => ( {
	volume: player.volume,
	audio: player.audio,
} );

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { setVolume: actions.setVolume }, dispatch );

export default connect( mapStateToProps, mapDispatchToProps )( Volume );
