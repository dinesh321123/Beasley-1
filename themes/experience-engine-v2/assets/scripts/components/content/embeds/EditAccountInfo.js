import React, { Component } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { firebaseAuth } from '../../../library';
import * as authActions from '../../../redux/actions/auth';
import Alert from '../../modals/elements/Alert';

class EditAccountInfo extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isLoggedIn: false,
			firstname: '',
			lastname: '',
			zip: '',
			gender: '',
			bday: '',
			showError: false,
			error: '',
		};
		this.onFieldChange = this.handleFieldChange.bind(this);
	}

	handleFieldChange(e) {
		const { target } = e;
		this.setState({ [target.name]: target.value });
	}

	componentDidMount() {
		const { firebase: config } = window.bbgiconfig;
		const { setUser } = this.props;
		if (config.projectId) {
			firebaseAuth.onAuthStateChanged(
				function(user) {
					if (user) {
						setUser(user);
						this.setState({ isLoggedIn: true });
					} else {
						this.setState({ isLoggedIn: false });
					}
				}.bind(this),
			);
		} else {
			// eslint-disable-next-line no-console
			console.error('Firebase Project ID not found in bbgiconfig.');
		}
	}

	render() {
		const { user } = this.props;
		console.log(user);
		console.log('eeeeeeeeeeee');
		const {
			firstname,
			lastname,
			zip,
			gender,
			bday,
			showError,
			error,
		} = this.state;
		console.log(showError);
		return (
			<div>
				{this.state.isLoggedIn ? (
					<div className="user-account-info-container">
						<Alert message={error} />
						<form className="sign_up_form">
							<div className="input_wrap">
								<div className="sign_up_single_row">
									<label className="form-label" htmlFor="user-firstname">
										First Name:
									</label>
									<input
										className="form-field"
										type="text"
										id="user-firstname"
										name="firstname"
										value={firstname}
										placeholder="Your name"
										onChange={this.onFieldChange}
									/>
								</div>
								<div className="sign_up_single_row">
									<label className="form-label" htmlFor="user-lastname">
										Last Name:
									</label>
									<input
										className="form-field"
										type="text"
										id="user-firstname"
										name="lastname"
										value={lastname}
										placeholder="Last name"
										onChange={this.onFieldChange}
									/>
								</div>
							</div>
							<div className="input_wrap">
								<div className="sign_up_single_row">
									<label className="form-label" htmlFor="user-zip">
										Zip:
									</label>
									<input
										className="form-field"
										type="text"
										id="user-zip"
										name="zip"
										value={zip}
										placeholder="Your zip"
										onChange={this.onFieldChange}
									/>
								</div>
								<div className="sign_up_single_row">
									<label className="form-label" htmlFor="user-bday">
										Birthday:
									</label>
									<input
										className="form-field"
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
							<div className="input_wrap">
								<div className="sign_up_single_row">
									<label className="form-label" htmlFor="user-gender-male">
										Gender:
									</label>
									<div className="form-radio">
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
									<div className="form-radio">
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
							</div>
							<div className="form-actions -signup">
								<button className="btn-sign-up" type="submit">
									Save
								</button>
							</div>
						</form>
					</div>
				) : null}
			</div>
		);
	}
}

EditAccountInfo.propTypes = {
	setUser: PropTypes.func.isRequired,
	user: PropTypes.oneOfType([PropTypes.object, PropTypes.bool]).isRequired,
};

export default connect(
	({ auth }) => ({
		user: auth.user || false,
	}),
	{
		setUser: authActions.setUser,
	},
)(EditAccountInfo);
