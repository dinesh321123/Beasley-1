import React, { PureComponent, Fragment } from 'react';
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
				<div>
					<img src={image} alt={title} />
					<a href={`tel:${phone}`}>{phone}</a>
					<a href={`mailto:${email}`}>{email}</a>
					<span>{address}</span>
				</div>
			);
		}

		return (
			<Fragment>
				{contacts}
				<button onClick={self.onToggle}>Contacts</button>
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
