import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import firebase from 'firebase';

import { showRestoreModal, showSignUpModal } from '../../redux/actions/modal';
import {
	ensureUserHasCurrentChannel,
} from '../../library/experience-engine';

import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';
import trapHOC from '@10up/react-focus-trap-hoc';

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
	}

	componentDidMount() {
		this.props.activateTrap();
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
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
			.then( () => {
				ensureUserHasCurrentChannel()
					.then( () => {
						self.props.close();
						window.location.reload();
						document.body.innerHTML = '';
					} );
			} )
			.catch( error => self.setState( { message: mapAuthErrorCodeToFriendlyMessage( error ) } ) );
	}

	render() {
		const self = this;
		const { email, password, message } = self.state;
		const { restore, signup } = self.props;
		const { title } = window.bbgiconfig.publisher;

		return (
			<Fragment>
				<Header>
					<h3>Sign In to {title}</h3>
				</Header>

				<Alert message={message} />

				<div className="signin-options">
					<div className="option">
						<p className="p-label"><em>Sign in with:</em></p>
						<OAuthButtons />
					</div>
					<div className="option">
						<form className="modal-form -form-sign-in" onSubmit={self.onFormSubmit}>
							<div className="modal-form-group">
								<label className="modal-form-label" htmlFor="user-email">Email</label>
								<input
									className="modal-form-field"
									type="email" id="user-email"
									name="email" value={email}
									onChange={self.onFieldChange}
									placeholder="your@emailaddress.com"
								/>
							</div>
							<div className="modal-form-group">
								<label className="modal-form-label" htmlFor="user-password">Password</label>
								<input
									className="modal-form-field"
									type="password"
									id="user-password"
									name="password"
									value={password}
									onChange={self.onFieldChange}
									placeholder="Your password"
								/>
							</div>
							<div className="modal-form-actions">
								<button className="btn -sign-in" type="submit">Sign In</button>
								<button className="btn -empty -nobor -forgot-password" type="button" onClick={restore}>Forgot Password</button>
							</div>
						</form>
					</div>
				</div>

				<div className="register">
					<h3>Not yet a member?</h3>
					<div className="blurb">
						<p>Sign up to {title} today for exclusive content and start listening live today!</p>
						<button className="btn -sign-up -empty" type="button" onClick={signup}>Sign Up</button>
					</div>
				</div>
			</Fragment>
		);
	}

}

SignIn.propTypes = {
	restore: PropTypes.func.isRequired,
	signup: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
};

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		signup: showSignUpModal,
		restore: showRestoreModal,
	}, dispatch );
}

function mapAuthErrorCodeToFriendlyMessage( error ) {
	switch( error.code ) {
		case 'auth/invalid-email':
			return 'We are sorry, but we can\'t find that email address. Please try another or register a new account.';
			break;
		case 'auth/user-disabled':
			return 'Whoops! Your user account has been disabled. Please register a new account if you wish to sign in.';
			break;
		case 'auth/user-not-found':
			return 'Whoops! Your user account was not found. Please register a new account if you wish to sign in.';
			break;
		case 'auth/wrong-password':
			return 'Whoops! The password you entered was incorrect. Please try again or click Forgot Password below to reset it.';
			break;
		default:
			return 'Whoops! We are sorry, but we can not log you in at this time. Please try again or click Forgot Password below.';
	}
}

export default connect( null, mapDispatchToProps )( trapHOC()( SignIn ) );
