import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { playStation } from '../../redux/actions/player';

class Stations extends Component {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { isOpen: false };

		self.onToggle = self.handleToggleClick.bind( self );
	}

	handlePlayClick( station ) {
		const self = this;
		self.setState( { isOpen: false } );
		self.props.play( station );
	}

	handleToggleClick() {
		this.setState( prevState => ( { isOpen: !prevState.isOpen } ) );
	}

	renderStations() {
		const self = this;
		const { isOpen } = self.state;
		if ( !isOpen ) {
			return false;
		}

		const { streams } = self.props;
		const stations = [];

		/* eslint-disable camelcase */
		streams.forEach( ( { title, subtitle, stream_call_letters, picture } ) => {
			const styles = {};
			const { large } = ( picture || {} );
			const { url } = ( large || {} );
			if ( url ) {
				styles.backgroundImage = `url(${url})`;
			}

			stations.push(
				<div key={stream_call_letters} style={styles}>
					<button type="button" onClick={self.handlePlayClick.bind( self, stream_call_letters )}>
						{title}
					</button>
					<span>{subtitle}</span>
				</div>
			);
		} );
		/* eslint-enable */

		return (
			<div>
				{stations}
			</div>
		);
	}

	render() {
		const self = this;
		const { stream } = self.props;

		let label = 'Listen Live';
		if ( stream ) {
			/* eslint-disable camelcase */
			const { title, stream_dial_numbers } = stream;
			label = `${title} / ${stream_dial_numbers}`;
			/* eslint-enable */
		}

		return (
			<Fragment>
				<div>
					<button onClick={self.onToggle} title="Open Stations Selector">
						{label}
						{' ^'}
					</button>
				</div>
				{self.renderStations()}
			</Fragment>
		);
	}

}

Stations.propTypes = {
	play: PropTypes.func.isRequired,
	stream: PropTypes.oneOfType( [PropTypes.bool, PropTypes.object] ),
	streams: PropTypes.arrayOf( PropTypes.object ).isRequired,
};

Stations.defaultProps = {
	stream: false,
};

function mapStateToProps( { player } ) {
	const { streams, station } = player;

	return {
		stream: streams.find( item => item.stream_call_letters === station ),
		streams,
	};
}

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( { play: playStation }, dispatch );
}

export default connect( mapStateToProps, mapDispatchToProps )( Stations );
