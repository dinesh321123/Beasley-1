import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { bindActionCreators } from 'redux';

import trapHOC from '@10up/react-focus-trap-hoc';
import { firebaseAuth } from '../../library/firebase';
import { showRestoreModal, showSignUpModal } from '../../redux/actions/modal';
import { ensureUserHasCurrentChannel } from '../../library/experience-engine';

import Header from './elements/Header';
import Alert from './elements/Alert';
import OAuthButtons from './authentication/OAuthButtons';
import { mapAuthErrorCodeToFriendlyMessage } from '../../library/friendly-error-messages';

class SignIn extends PureComponent {
	constructor(props) {
		super(props);

		this.state = {
			email: '',
			password: '',
			message: '',
		};

		this.onFieldChange = this.handleFieldChange.bind(this);
		this.onFormSubmit = this.handleFormSubmit.bind(this);
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

	handleFormSubmit(e) {
		const { email, password } = this.state;

		e.preventDefault();

		firebaseAuth
			.signInWithEmailAndPassword(email, password)
			.then(() => {
				ensureUserHasCurrentChannel().then(() => {
					this.props.close();
					window.location.reload();
					document.body.innerHTML = '';
				});
			})
			.catch(error =>
				this.setState({ message: mapAuthErrorCodeToFriendlyMessage(error) }),
			);
	}

	render() {
		const { email, password, message } = this.state;
		const { restore, signup } = this.props;
		const { title } = window.bbgiconfig.publisher;

		return (
			<>
				<Header>
					<h3>Sign In to {title}</h3>
				</Header>

				<Alert message={message} />

				<div className="signin-options">
					<div className="option">
						<p className="p-label">
							<em>Sign in with:</em>
						</p>
						<OAuthButtons />
					</div>
					<div className="option">
						<form
							className="modal-form -form-sign-in"
							onSubmit={this.onFormSubmit}
						>
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
									placeholder="your@emailaddress.com"
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
							<div className="modal-form-actions">
								<button className="btn -sign-in" type="submit">
									Sign In
								</button>
								<button
									className="btn -empty -nobor -forgot-password"
									type="button"
									onClick={restore}
								>
									Forgot Password
								</button>
							</div>
						</form>
					</div>
				</div>

				<div className="register">
					<h3>Not yet a member?</h3>
					<div className="blurb">
						<p>
							Sign up to {title} to personalize your experience, customize your
							homepage and discover new content!
						</p>
						<button
							className="btn -sign-up -empty"
							type="button"
							onClick={signup}
						>
							Sign Up
						</button>
					</div>
				</div>
			</>
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

function mapDispatchToProps(dispatch) {
	return bindActionCreators(
		{
			signup: showSignUpModal,
			restore: showRestoreModal,
		},
		dispatch,
	);
}

export default connect(null, mapDispatchToProps)(trapHOC()(SignIn));
