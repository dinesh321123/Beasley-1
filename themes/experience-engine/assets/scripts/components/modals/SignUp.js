import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import md5 from 'md5';
import trapHOC from '@10up/react-focus-trap-hoc';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { firebaseAuth } from '../../library/firebase';
import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';
import { mapAuthErrorCodeToFriendlyMessage } from '../../library/friendly-error-messages';

import {
	saveUser,
	validateDate,
	validateEmail,
	validateZipcode,
	validateGender,
	ensureUserHasCurrentChannel,
} from '../../library/experience-engine';

import { isChrome, isFireFox, isIOS, isWebKit } from '../../library/browser';

import { showSignInModal } from '../../redux/actions/modal';
import { suppressUserCheck, setDisplayName } from '../../redux/actions/auth';

class SignUp extends PureComponent {
	static createMask(value) {
		return value.toString().replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
	}

	static detectSupportedDevices(browsers) {
		const { userAgent } = window.navigator;
		const iOSChrome = isIOS() && !userAgent.match(/Chrome/i);
		const iOSSafari = isIOS() && isWebKit() && !userAgent.match(/CriOS/i);
		const iOSFireFox = isIOS() && isFireFox();

		/* Dont fallback on supported or partially supported browsers */

		if (browsers === 'supported') {
			return !isChrome() && !iOSSafari && !iOSFireFox && !iOSChrome;
		}

		return false;
	}

	static isMS() {
		const { userAgent } = window.navigator;
		return document.documentMode || !!userAgent.match(/Edge/i);
	}

	constructor(props) {
		super(props);

		this.hiddenBday = React.createRef();

		this.state = {
			email: '',
			password: '',
			firstname: '',
			lastname: '',
			zip: '',
			gender: '',
			bday: '',
			error: '',
		};

		this.onFieldChange = this.handleFieldChange.bind(this);
		this.onFormSubmit = this.handleFormSubmit.bind(this);
		this.handleInputMask = this.handleInputMask.bind(this);
	}

	componentDidMount() {
		this.props.activateTrap();
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
	}

	handleFieldChange(e) {
		const { target } = e;
		this.setState({ [target.name]: target.value });
	}

	handleInputMask(e) {
		const { target } = e;
		this.setState({ [target.name]: SignUp.createMask(target.value) });
	}

	handleFormSubmit(e) {
		const {
			email,
			password,
			firstname,
			lastname,
			zip,
			gender,
			bday,
		} = this.state;

		const emailAddress = email.trim().toLowerCase();
		const userData = {
			displayName: `${firstname} ${lastname}`,
			photoURL: `//www.gravatar.com/avatar/${md5(emailAddress)}.jpg?s=100`,
		};

		e.preventDefault();

		this.props.suppressUserCheck();

		if (!firstname) {
			this.setState({ error: 'Please enter your first name.' });
			return false;
		}

		if (!lastname) {
			this.setState({ error: 'Please enter your last name.' });
			return false;
		}

		if (validateEmail(email) === false) {
			this.setState({ error: 'Please enter a valid email address.' });
			return false;
		}

		if (validateZipcode(zip) === false) {
			this.setState({ error: 'Please enter a valid US Zipcode.' });
			return false;
		}

		if (validateDate(bday) === false) {
			this.setState({ error: 'Please ensure date is in MM/DD/YYYY format' });
			return false;
		}
		this.setState({ error: '' });

		if (validateGender(gender) === false) {
			this.setState({ error: 'Please select your gender.' });
			return false;
		}

		return firebaseAuth
			.createUserWithEmailAndPassword(emailAddress, password)
			.then(response => {
				const { user } = response;

				saveUser(emailAddress, zip, gender, bday);
				user.updateProfile(userData);

				this.props.setDisplayName(userData.displayName);
			})
			.then(() => {
				ensureUserHasCurrentChannel().then(() => {
					this.props.close();
					window.location.reload();
					document.body.innerHTML = '';
				});
			})
			.catch(error =>
				this.setState({ error: mapAuthErrorCodeToFriendlyMessage(error) }),
			);
	}

	render() {
		const {
			email,
			password,
			firstname,
			lastname,
			zip,
			gender,
			bday,
			error,
		} = this.state;
		const { signin } = this.props;

		return (
			<>
				<Header>
					<h3>Sign Up for Exclusive Access</h3>
				</Header>

				<Alert message={error} />

				<p className="p-label">
					<em>Register with:</em>
				</p>
				<OAuthButtons horizontal />

				<form className="modal-form -form-sign-up" onSubmit={this.onFormSubmit}>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-firstname">
								First Name
							</label>
							<input
								className="modal-form-field"
								type="text"
								id="user-firstname"
								name="firstname"
								value={firstname}
								onChange={this.onFieldChange}
								placeholder="Your name"
							/>
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-lastname">
								Last Name
							</label>
							<input
								className="modal-form-field"
								type="text"
								id="user-lastname"
								name="lastname"
								value={lastname}
								onChange={this.onFieldChange}
								placeholder="Your surname"
							/>
						</div>
					</div>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-email">
								Email
							</label>
							<input
								className="modal-form-field"
								type="email"
								id="user-email"
								name="email"
								value={email}
								onChange={this.onFieldChange}
								placeholder="yourname@yourdomain.com"
							/>
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-password">
								Password
							</label>
							<input
								className="modal-form-field"
								type="password"
								id="user-password"
								name="password"
								value={password}
								onChange={this.onFieldChange}
								placeholder="Your password"
							/>
						</div>
					</div>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-zip">
								Zip
							</label>
							<input
								className="modal-form-field"
								type="text"
								id="user-zip"
								name="zip"
								value={zip}
								onChange={this.onFieldChange}
								placeholder="90210"
							/>
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-bday">
								Birthday
							</label>
							<input
								className="modal-form-field"
								type="text"
								id="user-bday"
								name="bday"
								value={bday}
								onChange={this.onFieldChange}
								placeholder="mm/dd/yyyy"
							/>
						</div>
					</div>

					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-gender-male">
							Gender
						</label>
						<div className="modal-form-radio">
							<input
								type="radio"
								id="user-gender-male"
								name="gender"
								value="male"
								checked={gender === 'male'}
								onChange={this.onFieldChange}
							/>
							<label htmlFor="user-gender-male">Male</label>
						</div>
						<div className="modal-form-radio">
							<input
								type="radio"
								id="user-gender-female"
								name="gender"
								value="female"
								checked={gender === 'female'}
								onChange={this.onFieldChange}
							/>
							<label htmlFor="user-gender-female">Female</label>
						</div>
					</div>
					<div className="modal-form-actions -signup">
						<button className="btn -sign-up" type="submit">
							Sign Up
						</button>
						<p>
							<strong>Already a member?</strong>{' '}
							<button
								className="btn -empty -nobor -sign-in"
								type="button"
								onClick={signin}
							>
								Sign In
							</button>
						</p>
					</div>
				</form>
			</>
		);
	}
}

SignUp.propTypes = {
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	suppressUserCheck: PropTypes.func.isRequired,
	signin: PropTypes.func.isRequired,
	setDisplayName: PropTypes.func.isRequired,
};

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			suppressUserCheck,
			setDisplayName,
			signin: showSignInModal,
		},
		dispatch,
	);
}

export default connect(null, mapDispatchToProps)(trapHOC()(SignUp));
