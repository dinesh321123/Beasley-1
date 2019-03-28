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

import { saveUser, validateDate } from '../../library/experience-engine';

import { showSignInModal } from '../../redux/actions/modal';
import { suppressUserCheck, setDisplayName } from '../../redux/actions/auth';

class SignUp extends PureComponent {

	static createMask( value ) {
		return value.toString().replace( /(\d{4})(\d{2})(\d{2})/, '$1/$2/$3' );
	}

	static isMS() {
		const { userAgent } = window.navigator;
		return document.documentMode || !!userAgent.match( /Edge/i );
	}

	constructor( props ) {
		super( props );

		const self = this;
		this.hiddenBday = React.createRef();

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
		self.handleInputMask = self.handleInputMask.bind( self );
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

		if ( false === validateDate( bday ) ) {
			self.setState( { error: 'Please ensure date is in MM/DD/YYYY format' } );
			return false;
		} else if ( ! emailAddress ) {
			self.setState( { error: 'Please enter a valid email address. '} );
		} else if ( ! password ) {
			self.setState( { error: 'Please enter a password.' } );
		} else {
			self.setState( { error: '' } );
		}

		auth.createUserWithEmailAndPassword( emailAddress, password, bday )
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
							<input className="modal-form-field" type="text" id="user-bday" name="bday" value={bday} onChange={self.onFieldChange} placeholder="mm/dd/yyyy" />
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
