import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import Controls from '../components/player/Controls';

import * as actions from '../redux/actions/player';

class LiveCta extends PureComponent {
	constructor( props ) {
		super( props );
	}

	renderLiveCta() {
		const self = this;
		const {
			streams,
			station,
			status,
			play,
			pause,
			resume,
		} = self.props;

		const stream = streams.find( item => item.stream_call_letters === station );

		return (
			<div className="content-wrap" style={{ backgroundImage: 'url(\'https://source.unsplash.com/random/1096x200\')' }}>
				{/* @TODO :: In the wrapper above, where will we dynamically pull this image from? */}
				<div className="meta">
					<h4>{stream ? stream.title : station}</h4>
					{/* @TODO:: How are we going to control this content dynamically? */}
					<h2>Home of the MRL Show</h2>

					{/* @TODO :: Button needs associated method to add station to users 'subscribed' list */}
					<button className="btn -icon">
						<svg width="15" height="15" xmlns="http://www.w3.org/2000/svg">
							<path fillRule="evenodd" clipRule="evenodd" d="M8.5 0h-2v6.5H0v2h6.5V15h2V8.5H15v-2H8.5V0z" />
						</svg>
						Subscribe
					</button>
				</div>
				<div className="action">
					<Controls status={status} play={() => play( station )} pause={pause} resume={resume} />
					<p>Play Live</p>
				</div>
			</div>
		);
	}

	render() {
		const self = this;
		const container = document.getElementById( 'live-cta' );
		const component = self.renderLiveCta();

		return ReactDOM.createPortal( component, container );
	}
}

LiveCta.propTypes = {
	station: PropTypes.string.isRequired,
	status: PropTypes.string.isRequired,
	initPlayer: PropTypes.func.isRequired,
	play: PropTypes.func.isRequired,
	pause: PropTypes.func.isRequired,
	resume: PropTypes.func.isRequired,
};

function mapStateToProps( { player } ) {
	return {
		station: player.station,
		status: player.status,
		streams: player.streams,
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

export default connect( mapStateToProps, mapDispatchToProps )( LiveCta );
