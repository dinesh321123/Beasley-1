import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';

class Contacts extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;
		self.state = { isOpen: false };

		self.onToggle = self.handleToggleClick.bind( self );
	}

	handleToggleClick() {
		this.setState( prevState => ( { isOpen: !prevState.isOpen } ) );
	}

	render() {
		const self = this;
		const { stream } = self.props;
		if ( !stream ) {
			return false;
		}

		const { title, email, phone, address, picture } = stream;
		const { isOpen } = self.state;

		let contacts = false;
		if ( isOpen ) {
			const image = picture && picture.large && picture.large.url ? picture.large.url : false;

			contacts = (
				<div className="live-player-modal">
					<img src={image} alt={title} />
					<a href={`tel:${phone}`}>{phone}</a>
					<a href={`mailto:${email}`}>{email}</a>
					<span>{address}</span>
				</div>
			);
		}

		return (
			<div className="controls-contact control-border">
				{contacts}
				<button onClick={self.onToggle}>
					<svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-labelledby="contact-icon-title contact-icon-desc" xmlns="http://www.w3.org/2000/svg">
						<title id="contact-icon-title">Contacts</title>
						<desc id="contact-icon-desc">Icon of telephone</desc>
						<path d="M21.9895 17.3623C22.0361 17.7196 21.9273 18.0304 21.6635 18.2945L18.5659 21.3706C18.4262 21.526 18.2438 21.6582 18.0186 21.7668C17.7935 21.8756 17.5723 21.9455 17.355 21.9765C17.3395 21.9765 17.2928 21.9805 17.2151 21.9883C17.1376 21.996 17.0366 22 16.9124 22C16.6174 22 16.1399 21.9495 15.48 21.8485C14.8201 21.7475 14.0128 21.4988 13.058 21.1027C12.1029 20.7065 11.02 20.1122 9.80895 19.3199C8.59792 18.5276 7.30917 17.44 5.94283 16.0572C4.85597 14.9851 3.95543 13.9597 3.24121 12.9809C2.52699 12.0021 1.9525 11.0971 1.51776 10.2659C1.08298 9.43466 0.756921 8.68113 0.539549 8.00528C0.322176 7.32944 0.174674 6.74681 0.0970411 6.2574C0.0194082 5.768 -0.0116449 5.38347 0.00388164 5.1038C0.0194082 4.82414 0.0271715 4.66878 0.0271715 4.6377C0.0582247 4.42019 0.128094 4.19879 0.23678 3.97351C0.345466 3.74823 0.477442 3.56567 0.632708 3.42584L3.73026 0.326271C3.94763 0.108757 4.19606 0 4.47554 0C4.67738 0 4.85594 0.0582626 5.0112 0.174788C5.16647 0.291313 5.29844 0.435028 5.40713 0.605931L7.89915 5.33685C8.03888 5.58544 8.0777 5.85733 8.01559 6.15253C7.95349 6.44773 7.82151 6.69632 7.61967 6.89829L6.47846 8.04024C6.44741 8.07131 6.42024 8.12181 6.39695 8.19172C6.37366 8.26164 6.36201 8.3199 6.36201 8.36651C6.42412 8.69278 6.56386 9.06566 6.78123 9.48515C6.96755 9.85803 7.25479 10.3125 7.64296 10.8485C8.03112 11.3845 8.58231 12.0021 9.29654 12.7012C9.99523 13.416 10.6163 13.9713 11.1597 14.3676C11.703 14.7636 12.1573 15.0551 12.5222 15.2415C12.8871 15.4279 13.1666 15.5406 13.3606 15.5793L13.6517 15.6376C13.6827 15.6376 13.7333 15.6259 13.8031 15.6027C13.873 15.5793 13.9234 15.5522 13.9545 15.521L15.282 14.1694C15.5616 13.9208 15.8875 13.7965 16.2602 13.7965C16.5242 13.7965 16.7337 13.8431 16.889 13.9363H16.9122L21.4072 16.5931C21.7333 16.7952 21.9274 17.0515 21.9895 17.3623Z" fill="#FF0000"/>
					</svg>
				</button>
			</div>
		);
	}

}

Contacts.propTypes = {
	stream: PropTypes.oneOfType( [ PropTypes.object, PropTypes.bool ] ),
};

Contacts.defaultProps = {
	stream: false,
};

function mapStateToProps( { player } ) {
	return {
		stream: player.streams.find( item => item.stream_call_letters === player.station ),
	};
}

export default connect( mapStateToProps )( Contacts );
