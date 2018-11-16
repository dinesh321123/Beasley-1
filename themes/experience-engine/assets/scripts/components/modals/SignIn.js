import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import firebase from 'firebase/app';

import { showRestoreModal } from '../../redux/actions/modal';

import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';

class SignIn extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			email: '',
			password: '',
			message: '',
		};

		self.onFieldChange = self.handleFieldChange.bind( self );
		self.onFormSubmit = self.handleFormSubmit.bind( self );
		self.onRestoreClick = self.handleRestoreClick.bind( self );
	}

	handleFieldChange( e ) {
		const { target } = e;
		this.setState( { [target.name]: target.value } );
	}

	handleFormSubmit( e ) {
		const self = this;
		const { email, password } = self.state;
		const auth = firebase.auth();

		e.preventDefault();

		auth.signInWithEmailAndPassword( email, password )
			.then( self.props.close )
			.catch( error => self.setState( { message: error.message } ) );
	}

	handleRestoreClick() {
		this.props.restore();
	}

	render() {
		const self = this;
		const { email, password, message } = self.state;

		return (
			<Fragment>
				<Header>Sign In</Header>

				<Alert message={message} />

				<form onSubmit={self.onFormSubmit}>
					<div>
						<label htmlFor="user-email">Email</label>
						<input type="email" id="user-email" name="email" value={email} onChange={self.onFieldChange} />
					</div>
					<div>
						<label htmlFor="user-password">Password</label>
						<input type="password" id="user-password" name="password" value={password} onChange={self.onFieldChange} />
					</div>
					<div>
						<button type="submit">Sign In</button>
						<button type="button" onClick={self.onRestoreClick}>Forgot Password</button>
					</div>
				</form>

				<OAuthButtons />
			</Fragment>
		);
	}

}

SignIn.propTypes = {
	restore: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
};

const mapDispatchToProps = ( dispatch ) => bindActionCreators( { restore: showRestoreModal }, dispatch );

export default connect( null, mapDispatchToProps )( SignIn );
