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
		const { title, stream_dial_numbers } = stream; // eslint-disable-line camelcase

		return (
			<Fragment>
				<div className="live-player-station -center -section">
					<button className="live-player-control-button -pronounced -icon-highlight -center" onClick={self.onToggle} title="Open Stations Selector">
						{ stream ? (
							<Fragment>
								{ title }
								<span className="divider">{' / '}</span>
								{ stream_dial_numbers /* eslint-disable-line camelcase */ } 
							</Fragment>
						) : (
							<Fragment>
								{'Listen Live'}
							</Fragment>
						) }
						<span className="caret">
							<svg width="12" height="21" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg" aria-labelledby="caret-icon-title caret-icon-desc">
								<title id="caret-icon-title">Caret Icon</title>
								<desc id="caret-icon-desc">Icon of a small outlined triangle used to indicate a collapsable element</desc>
								<path d="M1.09988 5.67364L5.76963 1.09822C5.83634 1.03277 5.9131 1 5.99993 1C6.08676 1 6.16366 1.03277 6.23041 1.09822L10.8998 5.67364C10.9667 5.73919 11 5.8144 11 5.89952C11 5.9846 10.9666 6.05991 10.8998 6.12532L10.3989 6.6161C10.3321 6.68155 10.2552 6.71425 10.1684 6.71425C10.0816 6.71425 10.0047 6.68155 9.93791 6.6161L5.99993 2.75747L2.06181 6.61634C1.99506 6.68179 1.91816 6.71428 1.83147 6.71428C1.7445 6.71428 1.66764 6.68158 1.60089 6.61634L1.09992 6.12536C1.03317 6.05995 1 5.9846 1 5.89955C0.999965 5.8144 1.03313 5.73909 1.09988 5.67364Z" fill="#4898D3" stroke="#4898D3" strokeWidth="0.5"/>
							</svg>
						</span>
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
