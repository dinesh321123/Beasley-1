import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import md5 from 'md5';
import trapHOC from '@10up/react-focus-trap-hoc';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import { firebaseAuth } from '../../library/firebase';
import Header from './elements/Header';
import Alert from './elements/Alert';
// import OAuthButtons from './authentication/OAuthButtons';
import { mapAuthErrorCodeToFriendlyMessage } from '../../library/friendly-error-messages';
import { setMParticleUserAtributes } from '../../library';

import {
	saveUser,
	validateDate,
	validateFutureDate,
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
			showError: false,
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

		if (
			firstname === '' ||
			lastname === '' ||
			password === '' ||
			zip === '' ||
			gender === '' ||
			bday === ''
		) {
			this.setState({
				showError: true,
			});
		}

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

		if (validateEmail(email)) {
			const inputElement = document.getElementById('user-email');
			inputElement.style.borderColor = '#000000';
		} else {
			this.setState({ error: 'Please enter a valid email address.' });
			const inputElement = document.getElementById('user-email');
			inputElement.style.borderColor = 'red';
			return false;
		}

		if (password === '') {
			this.setState({ error: 'Please enter a password.' });
			return false;
		}

		if (validateZipcode(zip) === false) {
			this.setState({ error: 'Please enter a valid US Zipcode.' });
			this.setState({
				showError: true,
			});
			return false;
		}

		if (validateFutureDate(bday) === false) {
			this.setState({ error: "Date can't be a future date." });
			const inputElement = document.getElementById('user-bday');
			inputElement.style.borderColor = 'red';
			return false;
		}

		if (validateDate(bday)) {
			const inputElement = document.getElementById('user-bday');
			inputElement.style.borderColor = '#000000';
		} else {
			this.setState({ error: 'Please ensure date is in MM/DD/YYYY format' });
			const inputElement = document.getElementById('user-bday');
			inputElement.style.borderColor = 'red';
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
				setMParticleUserAtributes(firstname, lastname, zip, gender, bday);
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
			showError,
		} = this.state;
		const { bbgiconfig } = window;
		const { publisher } = bbgiconfig || {};
		console.log(publisher);
		const { signin } = this.props;
		const subtitle = `Create Your Account & Unlock More ${publisher.title} Than Ever Before.`;
		const subtitle_innerText = `When logged in, you'll discover exclusive audio, video, and articles and have a customized experience with ${publisher.title}. Plus, you'll be among a group of fans helping us with direct feedback on music, events, and content.`;

		return (
			<>
				<Header>
					<h3>Sign Up for Exclusive Access</h3>
				</Header>

				<div className="modal-subtitle">
					<p>{subtitle}</p>
					<p>{subtitle_innerText}</p>
				</div>

				<Alert message={error} />

				<p>&nbsp;</p>
				{/* Temporarily Hide Auth Buttons Due To Issues
				<p className="p-label">
					<em>Register with:</em>
				</p>
				<OAuthButtons horizontal />
				*/}

				<form className="modal-form -form-sign-up" onSubmit={this.onFormSubmit}>
					<div className="modal-form-group-inline">
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-firstname">
								First Name
							</label>
							<input
								className={`modal-form-field
									${showError && firstname === '' ? 'error-field' : ''}
									`}
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
								className={`modal-form-field
								${showError && lastname === '' ? 'error-field' : ''}
								`}
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
								className={`modal-form-field
								${showError && email === '' ? 'error-field' : ''}
								`}
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
								className={`modal-form-field
								${showError && password === '' ? 'error-field' : ''}
								`}
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
								className={`modal-form-field
								${showError && zip === '' ? 'error-field' : ''}
								`}
								type="text"
								id="user-zip"
								name="zip"
								value={zip}
								onChange={this.onFieldChange}
								placeholder="90210"
								pattern="\d{5}"
							/>
						</div>
						<div className="modal-form-group">
							<label className="modal-form-label" htmlFor="user-bday">
								Birthday
							</label>
							<input
								className={`modal-form-field
								${showError && bday === '' ? 'error-field' : ''}
								`}
								type="text"
								id="user-bday"
								name="bday"
								value={bday}
								onChange={this.onFieldChange}
								pattern="\d{2}/\d{2}/\d{4}"
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
