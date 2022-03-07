import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import trapHOC from '@10up/react-focus-trap-hoc';

import Header from './elements/Header';
import Alert from './elements/Alert';
import UserNav from '../../modules/UserNav';
import { firebaseAuth } from '../../library/firebase';

import {
	saveUser,
	validateDate,
	validateEmail,
	validateZipcode,
	validateGender,
} from '../../library/experience-engine';

class CompleteSignup extends PureComponent {
	constructor(props) {
		super(props);

		this.state = {
			email: '',
			zip: '',
			gender: '',
			bday: '',
			error: '',
		};

		this.onFieldChange = this.handleFieldChange.bind(this);
		this.onFormSubmit = this.handleFormSubmit.bind(this);
	}

	componentDidMount() {
		this.props.activateTrap();

		/**
		 * The following sets a trap for the Complete Signup modal. The
		 * first time the Close button is clicked we show a warning. The
		 * second time we close the modal and signout the user.
		 */
		window.beforeBeasleyModalClose = () => {
			/* First time close button was clicked */
			this.setState({
				error:
					'You must complete your profile information before you can login. Click close to continue as a non-member.',
			});

			/* Second time close button was clicked */
			window.beforeBeasleyModalClose = () => {
				firebaseAuth.signOut();

				if (UserNav.isHomepage()) {
					window.location.reload();
				}

				window.beforeBeasleyModalClose = null;
				this.props.close();

				/* return true to allow modal to close */
				return true;
			};

			/* return false to force modal to ignore close click */
			return false;
		};
	}

	componentWillUnmount() {
		this.props.deactivateTrap();
		window.beforeBeasleyModalClose = null;
	}

	handleFieldChange(e) {
		const { target } = e;
		this.setState({ [target.name]: target.value });
	}

	handleFormSubmit(e) {
		let { bday } = this.state;
		const { zip, gender, email } = this.state;
		const { user, close } = this.props;

		/* Convert bday since validateDate expects date in mm/dd/yyyy format */
		if (bday && bday.indexOf('-') !== -1) {
			bday = bday
				.split('-')
				.reverse()
				.join('/');
		}

		e.preventDefault();

		if (validateEmail(email) === false) {
			this.setState({ error: 'Please enter a valid email address.' });
			return false;
		}

		if (validateZipcode(zip) === false) {
			this.setState({ error: 'Please enter a valid US Zipcode.' });
			return false;
		}

		// @TODO :: This currently breaks on date specific inputs. We could consider just removing the date input type and using a text input.
		if (validateDate(bday) === false) {
			this.setState({ error: 'Please ensure date is in MM/DD/YYYY format.' });
			return false;
		}

		if (validateGender(gender) === false) {
			this.setState({ error: 'Please select your gender.' });
			return false;
		}

		this.setState({ error: '' });

		if (user) {
			saveUser(email, zip, gender, bday).then(() => {
				close();
				window.location.reload();
			});
		}

		return true;
	}

	render() {
		const { email, zip, gender, bday, error } = this.state;
		const { user } = this.props;

		/** If Firebase gave us an email use it as the default */
		if (!email && user.email) {
			this.setState({ email: user.email });
		}

		return (
			<>
				<Header>
					<h3>Complete Your Profile</h3>
				</Header>

				<Alert message={error} />

				<form className="modal-form -form-sign-up" onSubmit={this.onFormSubmit}>
					<div className="modal-form-group">
						<label className="modal-form-label" htmlFor="user-email">
							Email
						</label>
						<input
							className="modal-form-field"
							type="text"
							id="user-email"
							name="email"
							value={email}
							onChange={this.onFieldChange}
							placeholder=""
						/>
					</div>
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
							placeholder="Enter your birthday"
						/>
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
					<div className="modal-form-actions">
						<button className="btn -sign-in" type="submit">
							Save
						</button>
					</div>
				</form>
			</>
		);
	}
}

CompleteSignup.propTypes = {
	close: PropTypes.func.isRequired,
	activateTrap: PropTypes.func.isRequired,
	deactivateTrap: PropTypes.func.isRequired,
	user: PropTypes.oneOfType([PropTypes.object, PropTypes.bool]).isRequired,
};

function mapStateToProps({ auth }) {
	return { user: auth.user || false };
}

export default connect(mapStateToProps)(trapHOC()(CompleteSignup));
