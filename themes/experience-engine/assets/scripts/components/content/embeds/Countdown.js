import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dfp from './Dfp';

const SECOND_IN_MILLISECONDS = 1000;
const MINUTE_IN_MILLISECONDS = SECOND_IN_MILLISECONDS * 60;
const HOUR_IN_MILLISECONDS = MINUTE_IN_MILLISECONDS * 60;
const DAY_IN_MILLISECONDS = HOUR_IN_MILLISECONDS * 24;

class Countdown extends Component {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = self.getTimeRemaining( false );
		self.interval = null;

		self.getTimeRemaining = self.getTimeRemaining.bind( self );
	}

	componentDidMount() {
		const self = this;
		const { payload } = self.props;
		if ( payload && payload.countdownTime ) {
			self.interval = setInterval( self.getTimeRemaining, 1000 );
		}
	}

	componentWillUnmount() {
		this.stopInterval();
	}

	stopInterval() {
		clearInterval( this.interval );
	}

	getTimeRemaining( updateState = true ) {
		const self = this;

		const total = Date.parse( self.props.payload.countdownTime ) - Date.parse( new Date() );
		if ( 0 >= total ) {
			self.stopInterval();
		}

		const seconds = Math.floor( total / SECOND_IN_MILLISECONDS ) % 60;
		const minutes = Math.floor( total / MINUTE_IN_MILLISECONDS ) % 60;
		const hours = Math.floor( total / HOUR_IN_MILLISECONDS ) % 24;
		const days = Math.floor( total / DAY_IN_MILLISECONDS );

		const remaining = { days, hours, minutes, seconds };
		if ( updateState ) {
			self.setState( remaining );
		}

		return remaining;
	}

	getSponsor() {
		const { network, unitId, unitName } = window.bbgiconfig.dfp.countdown;
		const { placeholder } = this.props;

		const params = {
			id: `${placeholder}-adunit`,
			className: 'countdown-sponsor',
		};

		// we use createElement to make sure we don't add empty spaces here, thus DFP can properly collapse it when nothing to show here
		return React.createElement( 'div', params, [
			<Dfp key="sponsor" placeholder={params.id} network={network} unitId={unitId} unitName={unitName} />,
		] );
	}

	render() {
		const self = this;
		const { payload } = self.props;

		if ( !payload ) {
			return false;
		}

		const {
			title,
			background,
			link,
		} = payload;

		const { color, image } = background;
		const blockStyle = {};

		if ( color ) {
			blockStyle.backgroundColor = color;
		}

		if ( image ) {
			blockStyle.backgroundImage = `url(${image})`;
		}

		const titleText = link
			? <a href={link}>{title}</a>
			: title;

		const { days, hours, minutes, seconds } = self.state;

		return (
			<div className="countdown" style={blockStyle}>
				<div className="countdown-content">
					<h4 className="countdown-title">
						{titleText}
					</h4>

					<div className="countdown-timer">
						<div className="time" title="Days">{( '0' + days ).slice( -2 )}</div>
						<div className="time" title="Hours">{( '0' + hours ).slice( -2 )}</div>
						<div className="time" title="Minutes">{( '0' + minutes ).slice( -2 )}</div>
						<div className="time" title="Seconds">{( '0' + seconds ).slice( -2 )}</div>
					</div>

					{self.getSponsor()}
				</div>
			</div>
		);
	}

}

Countdown.propTypes = {
	placeholder: PropTypes.string.isRequired,
	payload: PropTypes.oneOfType( [PropTypes.bool, PropTypes.object] ),
};

Countdown.defaultProps = {
	payload: false,
};

export default Countdown;
