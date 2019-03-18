import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import firebase from 'firebase';
import md5 from 'md5';
import trapHOC from '@10up/react-focus-trap-hoc';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';

import { saveUser } from '../../library/experience-engine';

import { showSignInModal } from '../../redux/actions/modal';
import { suppressUserCheck, setDisplayName } from '../../redux/actions/auth';

class SignUp extends PureComponent {

	constructor( props ) {
		super( props );

		const self = this;

		self.state = {
			email: '',
			password: '',
			firstname: '',
			lastname: '',
			zip: '',
			gender: '',
			bday: '',
			error: '',
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
		const { email, password, firstname, lastname, zip, gender, bday } = self.state;
		const auth = firebase.auth();

		const emailAddress = email.trim().toLowerCase();
		const userData = {
			displayName: `${firstname} ${lastname}`,
			photoURL: `//www.gravatar.com/avatar/${md5( emailAddress )}.jpg?s=100`,
		};

		e.preventDefault();

		self.props.suppressUserCheck();
		auth.createUserWithEmailAndPassword( emailAddress, password )
			.then( ( response ) => {
				const { user } = response;

				saveUser( emailAddress, zip, gender, bday );
				user.updateProfile( userData );

				self.props.setDisplayName( userData.displayName );
			} )
			.then( () => self.props.close() )
			.catch( error => self.setState( { error: error.message } ) );
	}

	render() {
		const self = this;
		const { email, password, firstname, lastname, zip, gender, bday, error } = self.state;
		const { signin } = self.props;

		return (
			<Fragment>
				<Header>
					<h3>Sign Up for Exclusive Access</h3>
				</Header>

				<Alert message={error} />

				<p className="p-label"><em>Register with:</em></p>
				<OAuthButtons horizontal />

				<form className="modal-form -form-sign-up" onSubmit={self.onFormSubmit}>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-firstname">First Name</label>
							<input className="modal-form-field" type="text" id="user-firstname" name="firstname" value={firstname} onChange={self.onFieldChange} placeholder="Your name" />
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-lastname">Last Name</label>
							<input className="modal-form-field" type="text" id="user-lastname" name="lastname" value={lastname} onChange={self.onFieldChange} placeholder="Your surname" />
						</div>
					</div>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-email">Email</label>
							<input className="modal-form-field" type="email" id="user-email" name="email" value={email} onChange={self.onFieldChange} placeholder="yourname@yourdomain.com" />
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-password">Password</label>
							<input className="modal-form-field" type="password" id="user-password" name="password" value={password} onChange={self.onFieldChange} placeholder="Your password" />
						</div>
					</div>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-zip">Zip</label>
							<input className="modal-form-field" type="text" id="user-zip" name="zip" value={zip} onChange={self.onFieldChange} placeholder="90210" />
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-bday">Birthday</label>
							<input className="modal-form-field" type="date" id="user-bday" name="bday" value={bday} onChange={self.onFieldChange} placeholder="Enter your birthday" />
						</div>
					</div>

					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-gender-male">Gender</label>
						<div className="modal-form-radio">
							<input type="radio" id="user-gender-male" name="gender" value="male" checked={'male' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-male">Male</label>
						</div>
						<div className="modal-form-radio">
							<input type="radio" id="user-gender-female" name="gender" value="female" checked={'female' === gender} onChange={self.onFieldChange} />
							<label htmlFor="user-gender-female">Female</label>
						</div>
					</div>
					<div className="modal-form-actions -signup">
						<button className="btn -sign-up" type="submit">Sign Up</button>
						<p><strong>Already a member?</strong> <button className="btn -empty -nobor -sign-in" type="button" onClick={signin}>Sign In</button></p>
					</div>
				</form>
			</Fragment>
		);
	}

}

SignUp.propTypes = {
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	suppressUserCheck: PropTypes.func.isRequired,
	signin: PropTypes.func.isRequired,
	setDisplayName: PropTypes.func.isRequired,
};

function mapDispatchToProps( dispatch ) {
	return bindActionCreators( {
		suppressUserCheck,
		setDisplayName,
		signin: showSignInModal,
	}, dispatch );
}

export default connect( null, mapDispatchToProps )( trapHOC()( SignUp ) );
