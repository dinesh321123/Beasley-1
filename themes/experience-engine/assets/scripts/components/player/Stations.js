import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { playStation } from '../../redux/actions/player';

class Stations extends Component {
	constructor( props ) {
		super( props );

		this.state = { isOpen: false };
		this.stationModalRef = React.createRef();

		this.onToggle = this.handleToggleClick.bind( this );
		this.handleEscapeKeyDown = this.handleEscapeKeyDown.bind( this );
		this.handleUserEventOutside = this.handleUserEventOutside.bind( this );
	}

	componentDidMount() {
		document.addEventListener( 'mousedown', this.handleUserEventOutside, false );
		document.addEventListener( 'scroll', this.handleUserEventOutside, false );
		document.addEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	componentWillUnmount() {
		document.removeEventListener(
			'mousedown',
			this.handleUserEventOutside,
			false,
		);
		document.removeEventListener( 'scroll', this.handleUserEventOutside, false );
		document.removeEventListener( 'keydown', this.handleEscapeKeyDown, false );
	}

	handlePlayClick( { stream_call_letters } ) {
		this.setState( { isOpen: false } );
		this.props.play( stream_call_letters );
	}

	handleToggleClick() {
		this.setState( prevState => ( { isOpen: !prevState.isOpen } ) );
	}

	handleUserEventOutside( e ) {
		const { current: ref } = this.stationModalRef;

		if ( !ref || !ref.contains( e.target ) ) {
			this.setState( { isOpen: false } );
		}
	}

	handleEscapeKeyDown( e ) {
		if ( 27 === e.keyCode ) {
			this.setState( { isOpen: false } );
		}
	}

	renderStations( textStyle ) {
		const { isOpen } = this.state;
		if ( !isOpen ) {
			return false;
		}

		const { streams } = this.props;
		const stations = [];

		/* eslint-disable camelcase */
		streams.forEach( ( stream ) => {
			const { title, stream_call_letters, picture } = stream;
			const { large, original } = picture || {};
			const { url } = large || original || {};

			let logo = false;
			if ( url ) {
				logo = <img src={url} alt={title} />;
			}

			stations.push(
				<div key={stream_call_letters}>
					<button
						type="button"
						className="control-station-button"
						onClick={() => this.handlePlayClick( stream )}
						style={textStyle}
					>
						{logo}
						<span>{title}</span>
					</button>
				</div>,
			);
		} );
		/* eslint-enable */

		return stations;
	}

	render() {
		const { stream, colors } = this.props;
		const { isOpen } = this.state;

		const textStyle = {
			color: colors['--brand-text-color'] || colors['--global-theme-secondary'],
		};

		const modalStyle = {
			background: colors['--brand-background-color'],
		};

		const buttonFillStyle = {
			fill:
				colors['--brand-button-color'] || colors['--global-theme-secondary'],
			stroke:
				colors['--brand-button-color'] || colors['--global-theme-secondary'],
		};

		let label = 'Listen Live';
		if ( stream ) {
			label = (
				<span>
					<span className="controls-station-title">Saved Stations</span>
				</span>
			);
		}

		return (
			<div
				ref={this.stationModalRef}
				className={`controls-station control-border${isOpen ? ' -open' : ''}`}
			>
				<button
					onClick={this.onToggle}
					aria-label="Open Stations Selector"
					style={textStyle}
				>
					{label}
					<svg
						width="12"
						height="12"
						viewBox="0 0 12 7"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
						style={buttonFillStyle}
					>
						<path
							d="M1.09988 5.67364L5.76963 1.09822C5.83634 1.03277 5.9131 1 5.99993 1C6.08676 1 6.16366 1.03277 6.23041 1.09822L10.8998 5.67364C10.9667 5.73919 11 5.8144 11 5.89952C11 5.9846 10.9666 6.05991 10.8998 6.12532L10.3989 6.6161C10.3321 6.68155 10.2552 6.71425 10.1684 6.71425C10.0816 6.71425 10.0047 6.68155 9.93791 6.6161L5.99993 2.75747L2.06181 6.61634C1.99506 6.68179 1.91816 6.71428 1.83147 6.71428C1.7445 6.71428 1.66764 6.68158 1.60089 6.61634L1.09992 6.12536C1.03317 6.05995 1 5.9846 1 5.89955C0.999965 5.8144 1.03313 5.73909 1.09988 5.67364Z"
							strokeWidth="0.5"
						/>
					</svg>
				</button>
				<div className="live-player-modal" style={modalStyle}>
					{this.renderStations( textStyle )}
				</div>
			</div>
		);
	}
}

Stations.propTypes = {
	play: PropTypes.func.isRequired,
	stream: PropTypes.oneOfType( [PropTypes.bool, PropTypes.object] ),
	streams: PropTypes.arrayOf( PropTypes.object ).isRequired,
	colors: PropTypes.shape( {} ).isRequired,
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

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)( Stations );
