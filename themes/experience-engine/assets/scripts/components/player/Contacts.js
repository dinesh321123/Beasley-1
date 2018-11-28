import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import ContactIcon from '../icons/ContactIcon';

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
					<p className="live-player-modal-image"><img src={image} alt={title} /></p>
					<p className="live-player-modal-text"><a href={`tel:${phone}`}><strong>{phone}</strong></a></p>
					<p className="live-player-modal-text"><a href={`mailto:${email}`}><strong>{email}</strong></a></p>
					<p className="live-player-modal-text">{address}</p>
				</div>
			);
		}

		return (
			<Fragment>
				{contacts}
				<div className="live-player-contacts -center -section">
					<button className="live-player-control-button -icon-highlight" onClick={self.onToggle}>
						<ContactIcon />
					</button>
				</div>
			</Fragment>
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
