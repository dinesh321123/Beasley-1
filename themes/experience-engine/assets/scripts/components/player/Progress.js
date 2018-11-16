import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { seekPosition } from '../../redux/actions/player';

class Progress extends PureComponent {

	static format( time ) {
		const HOUR_IN_SECONDS = 3600;
		const MINUTE_IN_SECONDS = 60;

		const hours = Math.floor( time / HOUR_IN_SECONDS );
		const minutes = Math.floor( ( time % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
		const seconds = Math.floor( time % MINUTE_IN_SECONDS );

		const toFixed = ( value ) => 2 === value.toString().length ? value : `0${value}`;
		let result = `${toFixed( minutes )}:${toFixed( seconds )}`;
		if ( 0 < hours ) {
			result = `${toFixed( hours )}:${result}`;
		}

		return result;
	}

	constructor( props ) {
		super( props );

		const self = this;
		self.onSeek = self.handleSeekPosition.bind( self );
	}

	handleSeekPosition( e ) {
		const { target } = e;

		let time = parseFloat( target.value );
		if ( Number.isNaN( time ) ) {
			time = 0;
		}

		this.props.seek( time );
	}

	render() {
		const self = this;
		const { time, duration } = self.props;

		if ( 0 >= duration ) {
			return false;
		}

		return (
			<div>
				<span>{Progress.format( time )}</span>
				<input type="range" min="0" max={duration} value={time} onChange={self.onSeek} />
				<span>{Progress.format( duration )}</span>
			</div>
		);
	}

}

Progress.propTypes = {
	time: PropTypes.number.isRequired,
	duration: PropTypes.number.isRequired,
	seek: PropTypes.func.isRequired,
};

const mapStateToProps = ( { player } ) => ( {
	time: player.time,
	duration: player.duration,
} );

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { seek: seekPosition }, dispatch );

export default connect( mapStateToProps, mapDispatchToProps )( Progress );
