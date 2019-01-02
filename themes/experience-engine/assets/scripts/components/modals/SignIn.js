import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';
import firebase from 'firebase';

import { showRestoreModal } from '../../redux/actions/modal';

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
		self.onRestoreClick = self.handleRestoreClick.bind( self );
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
				<Header>
					<h3>Sign In</h3>
				</Header>

				<Alert message={message} />

				<form className="modal-form -form-sign-in" onSubmit={self.onFormSubmit}>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-email">Email</label>
						<input
							className="modal-form-field"
							type="email" id="user-email"
							name="email" value={email}
							onChange={self.onFieldChange}
							placeholder="Your email address"
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
						<button className="button -sign-in" type="submit">Sign In</button>
						<button
							className="button -forgot-password"
							type="button"
							onClick={self.onRestoreClick}
						>
							Forgot Password
						</button>
					</div>
				</form>
				<h5 className="section-head">
					<span>Or sign in with</span>
				</h5>
				<OAuthButtons />
			</Fragment>
		);
	}

}

SignIn.propTypes = {
	restore: PropTypes.func.isRequired,
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
};

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( { restore: showRestoreModal }, dispatch );
}

export default connect( null, mapDispatchToProps )( trapHOC()( SignIn ) );
