import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import VolumeIcon from '../icons/volumeIcon';

import * as actions from '../../redux/actions/player';

class Volume extends Component {
	constructor() {
		super();

		this.state = {
			adjustVolume: false
		};

		this.showVolumeSlider = this.showVolumeSlider.bind( this );
	}

	showVolumeSlider() {
		const { adjustVolume } = this.state;

		this.setState( {
			adjustVolume: ! adjustVolume
		} );
	}

	render() {
		const { volume, audio, setVolume } = this.props;
		const { adjustVolume } = this.state;

		if ( audio && audio.length && 0 === audio.indexOf( 'https://omny.fm/' ) ) {
			return false;
		}

		return (
			<div className="live-player-volume">
				<button className="live-player-control-button" onClick={ this.showVolumeSlider }>
					<VolumeIcon />
				</button>
				
				{ adjustVolume && 
					<Fragment>
						<label className="screen-reader-text" htmlFor="audio-volume">Volume:</label>
						<input className="live-player-audio-input" type="range" id="audio-volume" min="0" max="100" step="1" value={volume} onChange={( e ) => setVolume( e.target.value )} />
					</Fragment>
				}
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
