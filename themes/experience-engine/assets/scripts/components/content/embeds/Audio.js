import React, { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import PropTypes from 'prop-types';

import * as actions from '../../../redux/actions/player';
import Controls from '../../player/Controls';

class AudioEmbed extends Component {

	constructor( props ) {
		super( props );

		const self = this;
		self.onPlayClick = self.handlePlayClick.bind( self );
	}

	getTitle() {
		const { src, title, sources } = this.props;
		const extractTitle = url => url
			.split( '/' ).pop() // take file name from URL
			.split( '?' ).shift() // drop query parameters
			.split( '.' ).shift() // drop file extension
			.split( '_' ).join( ' ' ); // replace underscores with white spaces

		if ( title ) {
			return title;
		}

		if ( src ) {
			return extractTitle( src );
		}

		if ( sources ) {
			const keys = Object.keys( sources );
			if ( keys && keys.length ) {
				return extractTitle( keys.shift() );
			}
		}

		return false;
	}

	getPlayableSource() {
		const { src, sources } = this.props;
		const urls = Object.keys( sources );
		const audio = document.createElement( 'audio' );

		let maybe = false;
		for ( let i = 0, len = urls.length; i < len; i++ ) {
			const url = urls[i];
			const playable = audio.canPlayType( sources[url] );
			if ( 'probably' === playable ) {
				return url;
			}

			if ( 'maybe' === playable && false === maybe ) {
				maybe = url;
			}
		}

		if ( maybe ) {
			return maybe;
		}

		return src;
	}

	handlePlayClick() {
		const self = this;
		const { omny, title, author, playAudio, playOmny } = self.props;
		const src = self.getPlayableSource();

		if ( omny ) {
			playOmny( src, title, author );
		} else {
			playAudio( src, self.getTitle(), author );
		}
	}

	getStatus() {
		const self = this;
		const { audio, status } = self.props;
		const src = self.getPlayableSource();

		return audio === src ? status : actions.STATUSES.LIVE_STOP;
	}

	render() {
		const self = this;
		const { pause, resume, title } = self.props;

		return (
			<Fragment>
				<Controls status={self.getStatus()} title={title} play={self.onPlayClick} pause={pause} resume={resume} />
			</Fragment>
		);
	}

}

AudioEmbed.propTypes = {
	audio: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	src: PropTypes.string.isRequired,
	omny: PropTypes.bool,
	title: PropTypes.string,
	author: PropTypes.string,
	sources: PropTypes.shape( {} ),
	playAudio: PropTypes.func.isRequired,
	playOmny: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
};

AudioEmbed.defaultProps = {
	omny: false,
	title: '',
	author: '',
	sources: {},
};

const mapStateToProps = ( { player } ) => ( {
	audio: player.audio,
	status: player.status,
} );

const mapDispatchToProps = ( dispatch ) => bindActionCreators( {
	playAudio: actions.playAudio,
	playOmny: actions.playOmny,
	pause: actions.pause,
	resume: actions.resume,
}, dispatch );

export default connect( mapStateToProps, mapDispatchToProps )( AudioEmbed );
