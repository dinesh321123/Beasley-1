import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import * as actions from '../../redux/actions/player';

const Volume = ( { volume, audio, setVolume } ) => {
	// hide volume control if playing an omny clip because it doesn't support volume change
	if ( audio && audio.length && 0 === audio.indexOf( 'https://omny.fm/' ) ) {
		return false;
	}

	return (
		<div>
			<label htmlFor="audio-volume">Volume:</label>
			<input type="range" id="audio-volume" min="0" max="100" step="1" value={volume} onChange={( e ) => setVolume( e.target.value )} />
		</div>
	);
};

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
