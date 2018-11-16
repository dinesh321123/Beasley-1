import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';

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
		const { isOpen } = self.state;
		const { address, email, phone } = self.props;

		let contacts = false;
		if ( isOpen ) {
			contacts = (
				<div>
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
	address: PropTypes.string.isRequired,
	email: PropTypes.string.isRequired,
	phone: PropTypes.string.isRequired,
};

export default Contacts;
