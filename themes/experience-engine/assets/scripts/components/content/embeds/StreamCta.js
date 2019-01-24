import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import AddToFavorites from './AddToFavorites';
import Controls from '../../player/Controls';

import * as actions from '../../../redux/actions/player';

function StreamCta( { audio, station, status, play, pause, resume, payload } ) {
	const { id, title, subtitle, picture, stream_call_letters: stream } = payload;

	const styles = {};
	if ( picture && picture.large && picture.large.url ) {
		styles.backgroundImage = `url("${picture.large.url}")`;
	}

	const playerStatus = !audio && stream === station
		? status
		: actions.STATUSES.LIVE_STOP;

	return (
		<div className="content-wrap" style={styles}>
			<div className="meta">
				<h4>{title}</h4>
				<h2>{subtitle}</h2>
				<AddToFavorites feedId={id} classes="-icon" addLabel="Subscribe" removeLabel="Unsubscribe" />
			</div>

			<div className="action">
				<Controls status={playerStatus} play={() => play( stream )} pause={pause} resume={resume} />
				<p>Play Live</p>
			</div>
		</div>
	);
}

StreamCta.propTypes = {
	audio: PropTypes.string.isRequired,
	station: PropTypes.string.isRequired,
	payload: PropTypes.oneOfType( [PropTypes.bool, PropTypes.object] ),
	status: PropTypes.string.isRequired,
	play: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
};

StreamCta.defaultProps = {
	payload: false,
};

function mapStateToProps( { player } ) {
	return {
		audio: player.audio,
		status: player.status,
		station: player.station,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		play: actions.playStation,
		pause: actions.pause,
		resume: actions.resume,
	}, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( StreamCta );
